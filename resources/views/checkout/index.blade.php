@extends('layouts.app')

@section('title', 'Secure Checkout | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;" x-data="checkoutApp()">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--muted); margin-bottom: 24px;">
        <a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; 
        <a href="{{ route('cart.index') }}">Cart</a> &nbsp;/&nbsp; 
        <span style="color: var(--maroon); font-weight: 600;">Checkout</span>
    </div>

    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.4rem; color: var(--maroon); margin-bottom: 32px;">Grocery Order Checkout</h1>

    <form @submit.prevent="submitOrder()">
        <div class="checkout-layout">
            <!-- Left Steps Column -->
            <div>
                <!-- Step 1: Select Shipping Address -->
                <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="background: var(--maroon); color: var(--white); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700;">1</span>
                            <span>Delivery Address</span>
                        </div>
                        <a href="{{ route('account.addresses') }}" target="_blank" style="font-size: 0.88rem; color: var(--saffron-deep); font-weight: 600;">+ Add New Address</a>
                    </h3>

                    @if($addresses->count() > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 12px;">
                            @foreach($addresses as $addr)
                                <label style="border: 2px solid var(--cream-dark); border-radius: 16px; padding: 16px; cursor: pointer; display: block; transition: all 0.3s var(--ease);"
                                       :style="selectedAddressId === {{ $addr->id }} ? 'border-color: var(--saffron); background: rgba(230,126,34,0.06);' : ''">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <input type="radio" name="address_id" value="{{ $addr->id }}" x-model="selectedAddressId" style="margin-top: 4px;">
                                        <span class="badge-status badge-info" style="font-size: 0.72rem; text-transform: uppercase;">{{ $addr->address_type }}</span>
                                    </div>
                                    <div style="font-weight: 700; font-size: 0.95rem; margin: 8px 0 4px; color: var(--maroon);">{{ $addr->name }}</div>
                                    <div style="font-size: 0.85rem; color: var(--charcoal-light); line-height: 1.5;">
                                        {{ $addr->address_line_1 }}, {{ $addr->city }}, {{ $addr->state }} - <strong>{{ $addr->pincode }}</strong><br>
                                        Phone: {{ $addr->phone }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div style="background: var(--cream); padding: 20px; border-radius: 16px; text-align: center;">
                            <p style="color: var(--charcoal-light); margin-bottom: 12px;">No delivery address found in your account.</p>
                            <a href="{{ route('account.addresses') }}" class="btn btn-primary btn-sm">+ Add Delivery Address</a>
                        </div>
                    @endif
                </div>

                <!-- Step 2: Shipping Method -->
                <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <span style="background: var(--maroon); color: var(--white); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700;">2</span>
                        <span>Delivery Option</span>
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($shippingMethods as $method)
                            <label style="border: 1px solid var(--cream-dark); border-radius: 16px; padding: 16px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s var(--ease);"
                                   :style="selectedShippingId === {{ $method->id }} ? 'border-color: var(--saffron); background: rgba(230,126,34,0.06);' : ''">
                                <div style="display: flex; align-items: center; gap: 14px;">
                                    <input type="radio" name="shipping_method_id" value="{{ $method->id }}" x-model="selectedShippingId" @change="recalculateTotals({{ $method->cost }})">
                                    <div>
                                        <div style="font-weight: 700; color: var(--maroon);">{{ $method->name }}</div>
                                        <div style="font-size: 0.82rem; color: var(--charcoal-light);">Est. Delivery: <strong>{{ $method->estimated_days }}</strong></div>
                                    </div>
                                </div>
                                <span style="font-weight: 700; color: var(--maroon); font-size: 1.05rem;">
                                    {{ $method->cost == 0 ? 'FREE' : '£' . number_format($method->cost, 2) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Step 3: Payment Method -->
                <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <span style="background: var(--maroon); color: var(--white); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700;">3</span>
                        <span>Payment Options</span>
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <label style="border: 1px solid var(--cream-dark); border-radius: 16px; padding: 18px; cursor: pointer; display: flex; align-items: center; gap: 14px; transition: all 0.3s var(--ease);"
                               :style="paymentMethod === 'razorpay' ? 'border-color: var(--saffron); background: rgba(230,126,34,0.06);' : ''">
                            <input type="radio" name="payment_method" value="razorpay" x-model="paymentMethod">
                            <div>
                                <div style="font-weight: 700; color: var(--maroon);"><i class="fa-solid fa-credit-card me-2"></i> Online Payment (Credit / Debit Card, Apple Pay, Google Pay)</div>
                                <div style="font-size: 0.85rem; color: var(--charcoal-light); margin-top: 2px;">Instant 256-bit SSL encrypted secure payment</div>
                            </div>
                        </label>

                        <label style="border: 1px solid var(--cream-dark); border-radius: 16px; padding: 18px; cursor: pointer; display: flex; align-items: center; gap: 14px; transition: all 0.3s var(--ease);"
                               :style="paymentMethod === 'cod' ? 'border-color: var(--saffron); background: rgba(230,126,34,0.06);' : ''">
                            <input type="radio" name="payment_method" value="cod" x-model="paymentMethod">
                            <div>
                                <div style="font-weight: 700; color: var(--maroon);"><i class="fa-solid fa-money-bill-wave me-2"></i> Cash on Delivery / Pay at Store Collection</div>
                                <div style="font-size: 0.85rem; color: var(--charcoal-light); margin-top: 2px;">Pay in cash or card upon delivery or store pickup</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary & Coupon -->
            <div>
                <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; box-shadow: var(--shadow-sm); position: sticky; top: 120px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 10px;">Order Review</h3>

                    <!-- Cart Items Preview -->
                    <div style="max-height: 220px; overflow-y: auto; margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px; border-bottom: 1px solid var(--cream-dark); padding-bottom: 16px;">
                        @foreach($cart->items as $item)
                            <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                                <div>
                                    <div style="font-weight: 600; color: var(--maroon);">{{ $item->product->name }} (x{{ $item->quantity }})</div>
                                </div>
                                <div style="font-weight: 600; color: var(--maroon);">£{{ number_format($item->subtotal, 2) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Coupon Code Input -->
                    <div style="background: var(--cream); border: 1px solid var(--cream-dark); border-radius: 14px; padding: 16px; margin-bottom: 20px;">
                        <label style="font-size: 0.85rem; font-weight: 700; display: block; margin-bottom: 8px; color: var(--maroon);"><i class="fa-solid fa-ticket me-2"></i> Apply Discount Coupon</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" x-model="couponCode" placeholder="Enter DESIFOOD10" style="flex: 1; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.9rem; text-transform: uppercase; font-weight: 600; font-family: 'Inter', sans-serif;">
                            <button type="button" @click="applyCoupon()" class="btn btn-primary btn-sm" style="padding: 10px 18px;">Apply</button>
                        </div>
                        <div x-show="couponMessage" 
                             style="font-size: 0.85rem; margin-top: 10px; padding: 8px 12px; border-radius: 8px; font-weight: 600;" 
                             :style="couponSuccess ? 'background: rgba(46, 125, 50, 0.1); color: #2E7D32; border: 1px solid #2E7D32;' : 'background: rgba(137, 15, 20, 0.1); color: var(--maroon); border: 1px solid var(--maroon);'" 
                             x-text="couponMessage" x-cloak>
                        </div>
                    </div>

                    <!-- Calculations -->
                    @php
                        $subtotal = $cart->items->sum('subtotal');
                    @endphp
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                        <span style="color: var(--charcoal-light);">Subtotal</span>
                        <span style="font-weight: 600; color: var(--maroon);">£{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;" x-show="discountAmount > 0">
                        <span style="color: var(--saffron-deep); font-weight: 600;">Coupon Discount</span>
                        <span style="font-weight: 700; color: var(--saffron-deep);" x-text="'-£' + discountAmount.toFixed(2)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                        <span style="color: var(--charcoal-light);">Shipping Fee</span>
                        <span style="font-weight: 600; color: var(--maroon);" x-text="'£' + shippingFee.toFixed(2)"></span>
                    </div>

                    <div style="border-top: 1px solid var(--cream-dark); padding-top: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-size: 1.1rem; font-weight: 700; color: var(--maroon);">Grand Total</span>
                        <span style="font-size: 1.8rem; font-weight: 700; color: var(--maroon);" x-text="'£' + grandTotal.toFixed(2)"></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 14px 24px; font-size: 1.05rem;" :disabled="loading">
                        <span x-show="!loading">Confirm & Place Order</span>
                        <span x-show="loading">Processing Order...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function checkoutApp() {
        return {
            selectedAddressId: {{ $addresses->first() ? $addresses->first()->id : 'null' }},
            selectedShippingId: {{ $shippingMethods->first() ? $shippingMethods->first()->id : 'null' }},
            paymentMethod: 'cod',
            couponCode: '',
            couponMessage: '',
            couponSuccess: false,
            subtotal: {{ $cart->items->sum('subtotal') }},
            discountAmount: 0.00,
            shippingFee: {{ $shippingMethods->first() ? $shippingMethods->first()->cost : 0.00 }},
            grandTotal: 0.00,
            loading: false,

            init() {
                this.calculateGrandTotal();
            },
            recalculateTotals(cost) {
                this.shippingFee = Number(cost);
                this.calculateGrandTotal();
            },
            calculateGrandTotal() {
                let taxable = Math.max(0, this.subtotal - this.discountAmount);
                this.grandTotal = Math.round((taxable + this.shippingFee) * 100) / 100;
            },
            applyCoupon() {
                if (!this.couponCode) {
                    this.couponMessage = 'Please enter a coupon code.';
                    this.couponSuccess = false;
                    return;
                }
                fetch('{{ route("checkout.coupon.verify") }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ code: this.couponCode })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.valid) {
                        this.discountAmount = Number(data.discount);
                        this.couponMessage = data.message;
                        this.couponSuccess = true;
                        this.calculateGrandTotal();
                    } else {
                        this.discountAmount = 0.00;
                        this.couponMessage = data.message;
                        this.couponSuccess = false;
                        this.calculateGrandTotal();
                    }
                })
                .catch(err => {
                    this.couponMessage = 'Server error verifying coupon.';
                    this.couponSuccess = false;
                });
            },
            submitOrder() {
                if (!this.selectedAddressId) {
                    alert('Please select or add a delivery address.');
                    return;
                }
                this.loading = true;

                fetch('{{ route("checkout.place") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        address_id: this.selectedAddressId,
                        shipping_method_id: this.selectedShippingId,
                        payment_method: this.paymentMethod,
                        coupon_code: this.couponCode
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Order creation failed.');
                        this.loading = false;
                        return;
                    }

                    if (data.is_online_payment) {
                        const options = {
                            "key": data.payment_data.key,
                            "amount": data.payment_data.amount,
                            "currency": "GBP",
                            "name": "Desi Foods Hounslow",
                            "description": "Order #" + data.payment_data.order_number,
                            "handler": (response) => {
                                fetch('{{ route("checkout.payment.verify") }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({
                                        order_number: data.payment_data.order_number,
                                        razorpay_payment_id: response.razorpay_payment_id || 'pay_mock_' + Date.now(),
                                        razorpay_order_id: response.razorpay_order_id || data.payment_data.razorpay_order_id,
                                        razorpay_signature: response.razorpay_signature || 'sig_verified'
                                    })
                                }).then(res => res.json()).then(ver => {
                                    window.location.href = ver.redirect_url;
                                });
                            },
                            "prefill": {
                                "name": data.payment_data.customer_name,
                                "email": data.payment_data.customer_email,
                                "contact": data.payment_data.customer_phone
                            },
                            "theme": { "color": "#890F14" }
                        };

                        if (typeof Razorpay !== 'undefined') {
                            const rzp = new Razorpay(options);
                            rzp.open();
                        } else {
                            options.handler({
                                razorpay_payment_id: 'pay_simulated_' + Date.now(),
                                razorpay_order_id: data.payment_data.razorpay_order_id
                            });
                        }
                    } else {
                        window.location.href = data.redirect_url;
                    }
                })
                .catch(err => {
                    alert('Server error occurred during checkout.');
                    this.loading = false;
                });
            }
        }
    }
</script>
@endsection
