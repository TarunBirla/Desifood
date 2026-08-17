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
                            <span>Take way Address</span>
                        </div>
                        <button type="button" @click="showAddressModal = true" class="btn btn-outline btn-sm" style="font-size: 0.88rem; color: var(--saffron-deep); font-weight: 600;">
                            <i class="fa-solid fa-plus me-1"></i> Add New Address
                        </button>
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
                            <p style="color: var(--charcoal-light); margin-bottom: 12px;">No Take way address found in your account.</p>
                            <button type="button" @click="showAddressModal = true" class="btn btn-primary btn-sm">+ Add Take way Address</button>
                        </div>
                    @endif
                </div>

                <!-- Step 2: Delivery Method -->
                <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <span style="background: var(--maroon); color: var(--white); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700;">2</span>
                        <span>Take way Option</span>
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($shippingMethods as $method)
                            <label style="border: 1px solid var(--cream-dark); border-radius: 16px; padding: 16px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s var(--ease);"
                                   :style="selectedShippingId === {{ $method->id }} ? 'border-color: var(--saffron); background: rgba(230,126,34,0.06);' : ''">
                                <div style="display: flex; align-items: center; gap: 14px;">
                                    <input type="radio" name="shipping_method_id" value="{{ $method->id }}" x-model="selectedShippingId" @change="recalculateTotals({{ $method->cost }})">
                                    <div>
                                        <div style="font-weight: 700; color: var(--maroon);">{{ $method->name }}</div>
                                        <div style="font-size: 0.82rem; color: var(--charcoal-light);">Est. Take way: <strong>{{ $method->estimated_days }}</strong></div>
                                    </div>
                                </div>
                                <span style="font-weight: 700; color: var(--maroon); font-size: 1.05rem;">
                                    {{ $method->cost == 0 ? 'FREE' : '£' . number_format($method->cost, 2) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Notice Box (Payment gateway options removed as requested) -->
                <div style="background: #E8F8F5; border: 1px solid #A3E4D7; border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 16px;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: #117864; color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <div>
                        <h4 style="font-family: 'Playfair Display', serif; font-size: 1.15rem; color: #117864; margin-bottom: 2px;">Pay on Delivery (Cash or Card at Doorstep)</h4>
                        <p style="font-size: 0.88rem; color: #145A32; margin: 0;">No online payment required. Pay via cash or card when your groceries arrive or at store pickup.</p>
                    </div>
                    <input type="hidden" name="payment_method" value="cod">
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

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 16px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 10px;" :disabled="isPlacing">
                        <i class="fa-solid fa-lock"></i>
                        <span x-text="isPlacing ? 'Placing Order...' : 'Place Grocery Order'"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Centered Add Address Modal with Smooth Scrollable Container -->
    <div x-show="showAddressModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position: fixed; inset: 0; background: rgba(44, 24, 16, 0.75); backdrop-filter: blur(8px); z-index: 999999; display: grid; place-items: center; padding: 20px; overflow-y: auto;" 
         x-cloak>
        
        <div @click.away="showAddressModal = false" 
             style="background: var(--white); border-radius: 24px; padding: 32px; width: 100%; max-width: 540px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); border: 1px solid var(--cream-dark); margin: auto; max-height: 85vh; overflow-y: auto;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-map-location-dot" style="color: var(--saffron-deep);"></i> Add Take way Address
                </h3>
                <button type="button" @click="showAddressModal = false" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--maroon); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--cream);">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('account.addresses.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 14px;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Full Name</label>
                    <input type="text" name="name" required placeholder="e.g. Jyoshna Patel" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Phone Number (UK)</label>
                    <input type="text" name="phone" required placeholder="07700 900123" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Address Line 1</label>
                    <input type="text" name="address_line_1" required placeholder="House / Flat #, Street" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Address Line 2 (Optional)</label>
                    <input type="text" name="address_line_2" placeholder="Locality / Area" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Town / City</label>
                        <input type="text" name="city" value="Hounslow" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Postcode</label>
                        <input type="text" name="pincode" required placeholder="TW3 2EN" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                    </div>
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">County / State</label>
                    <input type="text" name="state" value="Greater London" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Address Type</label>
                    <select name="address_type" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                        <option value="home">Home</option>
                        <option value="work">Work</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 10px; border-top: 1px solid var(--cream-dark);">
                    <button type="button" @click="showAddressModal = false" class="btn btn-outline btn-sm" style="padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 24px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Address
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function checkoutApp() {
        return {
            selectedAddressId: {{ $addresses->first() ? $addresses->first()->id : 'null' }},
            selectedShippingId: {{ $shippingMethods->first() ? $shippingMethods->first()->id : 'null' }},
            paymentMethod: 'cod',
            showAddressModal: false,
            couponCode: '',
            couponMessage: '',
            couponSuccess: false,
            discountAmount: 0,
            baseSubtotal: {{ $cart->items->sum('subtotal') }},
            shippingFee: {{ $shippingMethods->first() ? $shippingMethods->first()->cost : 0 }},
            isPlacing: false,

            get grandTotal() {
                return Math.max(0, this.baseSubtotal - this.discountAmount) + this.shippingFee;
            },

            recalculateTotals(cost) {
                this.shippingFee = parseFloat(cost);
            },

            applyCoupon() {
                if (!this.couponCode) return;
                fetch('{{ route("checkout.coupon.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: this.couponCode, subtotal: this.baseSubtotal })
                })
                .then(res => res.json())
                .then(data => {
                    this.couponMessage = data.message;
                    this.couponSuccess = data.success;
                    if (data.success) {
                        this.discountAmount = parseFloat(data.discount);
                    } else {
                        this.discountAmount = 0;
                    }
                });
            },

            submitOrder() {
                if (!this.selectedAddressId) {
                    alert('Please select or add a Take way address.');
                    return;
                }
                this.isPlacing = true;
                
                fetch('{{ route("checkout.place") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        address_id: this.selectedAddressId,
                        shipping_method_id: this.selectedShippingId,
                        payment_method: 'cod',
                        coupon_code: this.couponCode
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.message || 'Could not place order.');
                        this.isPlacing = false;
                    }
                })
                .catch(() => {
                    alert('Order placement failed.');
                    this.isPlacing = false;
                });
            }
        }
    }
</script>
@endsection
