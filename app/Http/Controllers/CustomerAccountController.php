<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RecurringOrder;
use App\Models\ReturnRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAccountController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $recentOrders = Order::with('items')->where('user_id', $user->id)->latest()->take(5)->get();
        $totalOrdersCount = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)->where('payment_status', 'paid')->sum('grand_total');

        return view('account.dashboard', compact('user', 'recentOrders', 'totalOrdersCount', 'totalSpent'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user->update($request->only('name', 'phone', 'date_of_birth', 'gender'));
        return back()->with('success', 'Profile updated successfully.');
    }

    public function addresses()
    {
        return redirect()->route('account.dashboard');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address_line_1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|string',
            'address_type' => 'required|in:home,work,other',
        ]);

        if ($request->has('is_default')) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create(array_merge($request->all(), [
            'user_id' => Auth::id(),
            'is_default' => $request->has('is_default')
        ]));

        return back()->with('success', 'Address added successfully.');
    }

    public function deleteAddress($id)
    {
        Address::where('id', $id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'Address deleted.');
    }

    public function orders()
    {
        $orders = Order::with('items.product.primaryImage')->where('user_id', Auth::id())->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }

    public function orderDetails(string $orderNumber)
    {
        $order = Order::with(['items.product', 'items.variant', 'statusHistories'])->where('order_number', $orderNumber)->where('user_id', Auth::id())->firstOrFail();
        return view('account.order_details', compact('order'));
    }

    public function downloadInvoice(string $orderNumber)
    {
        $order = Order::with(['items', 'user'])->where('order_number', $orderNumber)->firstOrFail();
        return view('invoices.invoice_pdf', compact('order'));
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string',
            'comment' => 'required|string',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_verified_purchase' => true,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Thank you for your review!');
    }

    public function requestReturn(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'reason' => 'required|string',
            'description' => 'required|string',
        ]);

        ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'requested',
            'refund_amount' => $order->grand_total,
        ]);

        $order->update(['order_status' => 'return_requested']);

        return back()->with('success', 'Return request submitted. Our team will contact you shortly.');
    }

    public function repeatOrder(string $orderNumber)
    {
        $order = Order::with('items.product', 'items.variant')->where('order_number', $orderNumber)->where('user_id', Auth::id())->firstOrFail();
        
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        
        $addedCount = 0;
        $outOfStockItems = [];

        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product || !$product->is_active) {
                $outOfStockItems[] = ($item->product_name ?? 'Product') . ' (Discontinued)';
                continue;
            }

            $variant = $item->variant_id ? ProductVariant::find($item->variant_id) : null;
            $stock = $variant ? $variant->stock : $product->stock;

            if ($stock < 1) {
                $outOfStockItems[] = $product->name . ' (Out of stock)';
                continue;
            }

            $qtyToAdd = min($item->quantity, $stock);
            $unitPrice = $variant ? $variant->effective_price : $product->effective_price;

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity = $qtyToAdd;
                $cartItem->unit_price = $unitPrice;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $qtyToAdd,
                    'unit_price' => $unitPrice,
                ]);
            }

            $addedCount++;
        }

        session(['order_type' => 'repeat', 'repeat_parent_order_id' => $order->id]);

        $message = "{$addedCount} item(s) from Order #{$order->order_number} added to your cart for Repeat Order!";
        if (!empty($outOfStockItems)) {
            $message .= " Note: Some items were skipped: " . implode(', ', $outOfStockItems);
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    public function recurringOrders()
    {
        $user = Auth::user();
        $targetMonth = date('Y-m', strtotime('+1 month'));
        $targetMonthName = date('F Y', strtotime('+1 month'));

        $recurringOrders = RecurringOrder::with(['product.primaryImage', 'variant'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('account.recurring', compact('user', 'recurringOrders', 'targetMonth', 'targetMonthName'));
    }

    public function updateRecurringOrder(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        
        $recurring = RecurringOrder::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $recurring->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Next-month recurring quantity updated.');
    }

    public function deleteRecurringOrder($id)
    {
        $recurring = RecurringOrder::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $recurring->update(['status' => 'cancelled']);

        return back()->with('success', 'Item removed from your next-month recurring order selection.');
    }

    public function checkoutRecurringOrder(Request $request)
    {
        $user = Auth::user();

        $recurringItems = RecurringOrder::with('product', 'variant')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        if ($recurringItems->isEmpty()) {
            return redirect()->route('account.recurring')->with('error', 'No next-month recurring items found.');
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $addedCount = 0;

        foreach ($recurringItems as $item) {
            $product = $item->product;
            if (!$product || !$product->is_active) continue;

            $variant = $item->variant;
            $unitPrice = $variant ? $variant->effective_price : $product->effective_price;

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity = $item->quantity;
                $cartItem->unit_price = $unitPrice;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                ]);
            }
            $addedCount++;
        }

        session(['order_type' => 'recurring']);

        return redirect()->route('cart.index')->with('success', "{$addedCount} recurring product(s) added to your cart for Next-Month Checkout!");
    }
}
