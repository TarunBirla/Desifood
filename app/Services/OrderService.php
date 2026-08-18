<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create Order with server-side validation and DB transaction
     */
    public function createOrder(User $user, ?int $addressId = null, int $shippingMethodId = 1, ?string $couponCode = null, string $paymentMethod = 'cod', ?string $customerNote = null, ?string $pickupDate = null, ?string $pickupTimeSlot = null): Order
    {
        return DB::transaction(function () use ($user, $addressId, $shippingMethodId, $couponCode, $paymentMethod, $customerNote, $pickupDate, $pickupTimeSlot) {
            // 1. Get Address Snapshot (Takeaway Store Pickup Snapshot)
            if ($addressId && $address = Address::where('id', $addressId)->where('user_id', $user->id)->first()) {
                $addressSnapshot = $address->toArray();
            } else {
                $addressSnapshot = [
                    'name' => $user->name,
                    'phone' => $user->phone ?? 'N/A',
                    'address_type' => 'takeaway',
                    'address_line_1' => 'Takeaway Store Pickup: 3-4 Green Parade, Whitton Road',
                    'city' => 'Hounslow',
                    'state' => 'London',
                    'pincode' => 'TW3 2EN',
                    'pickup_date' => $pickupDate ?? date('Y-m-d'),
                    'pickup_time_slot' => $pickupTimeSlot ?? 'Store Opening Hours',
                ];
            }

            // 2. Get Cart & Items
            $cart = Cart::with('items.product', 'items.variant')->where('user_id', $user->id)->first();
            if (!$cart || $cart->items->isEmpty()) {
                throw new Exception('Your shopping cart is empty.');
            }

            // 3. Revalidate every item stock & price server-side
            $subtotal = 0.00;
            $itemsToProcess = [];

            foreach ($cart->items as $item) {
                $product = Product::find($item->product_id);
                if (!$product || !$product->is_active) {
                    throw new Exception("Product '{$item->product->name}' is no longer available.");
                }

                $variant = $item->variant_id ? ProductVariant::find($item->variant_id) : null;
                $currentStock = $variant ? $variant->stock : $product->stock;

                if ($currentStock < $item->quantity) {
                    $name = $variant ? "{$product->name} ({$variant->variant_name})" : $product->name;
                    throw new Exception("Insufficient stock for '{$name}'. Available: {$currentStock}, requested: {$item->quantity}.");
                }

                $unitPrice = $variant ? $variant->effective_price : $product->effective_price;
                $itemSubtotal = $unitPrice * $item->quantity;
                $subtotal += $itemSubtotal;

                $itemsToProcess[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'product_name' => $product->name,
                    'variant_name' => $variant ? $variant->variant_name : null,
                    'sku' => $variant ? $variant->sku : $product->sku,
                    'product_image' => $variant && $variant->image ? $variant->image : ($product->primaryImage ? $product->primaryImage->image_path : null),
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // 4. Validate Shipping Method & Calculate Fee
            $shippingMethod = ShippingMethod::findOrFail($shippingMethodId);
            $shippingFee = $shippingMethod->cost;
            if ($shippingMethod->free_shipping_threshold && $subtotal >= $shippingMethod->free_shipping_threshold) {
                $shippingFee = 0.00;
            }

            // 5. Validate Coupon Code server-side
            $discountAmount = 0.00;
            $appliedCoupon = null;
            if ($couponCode) {
                $coupon = Coupon::where('code', strtoupper($couponCode))->first();
                if ($coupon) {
                    $validation = $coupon->isValidForOrder($subtotal, $user->id);
                    if ($validation['valid']) {
                        $discountAmount = $validation['discount'];
                        $appliedCoupon = $coupon;
                    } else {
                        throw new Exception($validation['message']);
                    }
                } else {
                    throw new Exception("Invalid coupon code '{$couponCode}'.");
                }
            }

            // 6. Tax calculation (18% GST if enabled in settings, or standard breakdown)
            $taxAmount = round(($subtotal - $discountAmount) * 0.18, 2);
            $grandTotal = round(($subtotal - $discountAmount) + $shippingFee + $taxAmount, 2);

            // 7. Generate Unique Order Number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // 8. Determine Order Type from Session
            $orderType = session('order_type', 'normal');
            $parentOrderId = session('repeat_parent_order_id', null);

            // 9. Create Order Record
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'shipping_address_id' => isset($address) ? $address->id : null,
                'shipping_address_json' => $addressSnapshot,
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $paymentMethod,
                'order_type' => $orderType,
                'parent_order_id' => $parentOrderId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'coupon_code' => $appliedCoupon ? $appliedCoupon->code : null,
                'shipping_fee' => $shippingFee,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'customer_note' => $customerNote,
            ]);

            if ($orderType === 'recurring') {
                \App\Models\RecurringOrder::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->update(['completed_order_id' => $order->id]);
            }

            session()->forget(['order_type', 'repeat_parent_order_id']);

            // 9. Create Order Items & Deduct Stock
            foreach ($itemsToProcess as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);

                // Deduct stock & audit log
                $this->inventoryService->adjustStock(
                    $itemData['product_id'],
                    $itemData['variant_id'],
                    -$itemData['quantity'],
                    'sale',
                    $order->order_number,
                    "Stock reduced for Order #{$order->order_number}"
                );
            }

            // 10. Record Coupon Usage
            if ($appliedCoupon) {
                CouponUsage::create([
                    'coupon_id' => $appliedCoupon->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'discount_amount' => $discountAmount,
                ]);
                $appliedCoupon->increment('used_count');
            }

            // 11. Record Order Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'notes' => 'Order created and pending payment authorization.',
                'changed_by' => $user->id,
            ]);

            // 12. Create In-App Admin Notification Record
            try {
                \App\Models\AdminNotification::create([
                    'type' => 'new_order',
                    'title' => "New Order #{$order->order_number} Placed",
                    'message' => "Customer {$user->name} ({$user->email}) placed a new grocery order for £" . number_format($order->grand_total, 2) . ".",
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'grand_total' => $order->grand_total,
                    'is_read' => false,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Admin Notification Error: ' . $e->getMessage());
            }

            // 13. Clear Cart
            $cart->items()->delete();

            return $order;
        });
    }
}
