<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Services\OrderService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected OrderService $orderService;
    protected PaymentService $paymentService;

    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::with('items.product.primaryImage', 'items.variant')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your shopping cart is empty.');
        }

        $addresses = Address::where('user_id', $user->id)->get();
        $shippingMethods = ShippingMethod::where('is_active', true)->get();

        return view('checkout.index', compact('user', 'cart', 'addresses', 'shippingMethods'));
    }

    public function verifyCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));
        if (empty($code)) {
            return response()->json(['valid' => false, 'discount' => 0, 'message' => 'Please enter a coupon code.']);
        }

        $user = Auth::user();
        $cart = Cart::with('items.product', 'items.variant')->where('user_id', $user->id)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['valid' => false, 'discount' => 0, 'message' => 'Cart is empty.']);
        }

        $subtotal = $cart->items->sum(function ($item) {
            $unitPrice = $item->variant ? $item->variant->effective_price : $item->product->effective_price;
            return $unitPrice * $item->quantity;
        });

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            return response()->json(['valid' => false, 'discount' => 0, 'message' => "Invalid coupon code '{$code}'."]);
        }

        $validation = $coupon->isValidForOrder($subtotal, $user->id);
        return response()->json([
            'valid' => $validation['valid'],
            'discount' => $validation['discount'] ?? 0,
            'message' => $validation['message'] ?? 'Coupon response received.'
        ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time_slot' => 'required|string',
            'customer_note' => 'nullable|string',
            'coupon_code' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();

            $shippingMethod = ShippingMethod::where('is_active', true)->first();
            $shippingMethodId = $shippingMethod ? $shippingMethod->id : 1;

            $pickupDetailsNote = "Takeaway Store Pickup on {$request->pickup_date} ({$request->pickup_time_slot}). " . ($request->customer_note ?? '');

            $order = $this->orderService->createOrder(
                $user,
                null,
                $shippingMethodId,
                $request->coupon_code,
                'cod',
                $pickupDetailsNote,
                $request->pickup_date,
                $request->pickup_time_slot
            );

            return response()->json([
                'success' => true,
                'is_online_payment' => false,
                'redirect_url' => route('checkout.confirmation', $order->order_number)
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'nullable|string',
        ]);

        $order = Order::where('order_number', $request->order_number)->firstOrFail();

        $this->paymentService->verifyAndCompletePayment(
            $order,
            $request->razorpay_payment_id,
            $request->razorpay_order_id,
            $request->razorpay_signature,
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully!',
            'redirect_url' => route('checkout.confirmation', $order->order_number)
        ]);
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::with(['items', 'user'])->where('order_number', $orderNumber)->firstOrFail();
        return view('checkout.confirmation', compact('order'));
    }
}
