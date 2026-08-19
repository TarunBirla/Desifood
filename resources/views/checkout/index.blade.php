@extends('layouts.app')

@section('title', 'Takeaway Store Pickup Checkout | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;" x-data="checkoutApp()">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--muted); margin-bottom: 24px;">
        <a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; 
        <a href="{{ route('cart.index') }}">Cart</a> &nbsp;/&nbsp; 
        <span style="color: var(--maroon); font-weight: 600;">Takeaway Checkout</span>
    </div>

    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.4rem; color: var(--maroon); margin-bottom: 24px;">In-Store Takeaway Order Checkout</h1>

    <!-- Live Product Quick Search Box for Last-Minute Additions -->
    <div x-data="quickCheckoutSearch()" style="position: relative; margin-bottom: 32px; width: 100%;">
        <div style="position: relative; width: 100%;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--saffron); font-size: 1.1rem;"></i>
            <input type="text" 
                   x-model="query" 
                   @input.debounce.300ms="fetchSuggestions()" 
                   @keydown.escape="showDropdown = false"
                   placeholder="Forgot something? Search and add items directly to your takeaway order (e.g. Samosas, Basmati Rice, Sweets)..." 
                   style="width: 100%; box-sizing: border-box; padding: 14px 18px 14px 48px; border-radius: 50px; border: 2px solid var(--cream-dark); background: var(--white); font-size: 0.98rem; outline: none; box-shadow: var(--shadow-sm); transition: border-color 0.2s;"
                   onfocus="this.style.borderColor='var(--saffron)'"
                   onblur="this.style.borderColor='var(--cream-dark)'">
            
            <template x-if="loading">
                <i class="fa-solid fa-spinner fa-spin" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: var(--saffron);"></i>
            </template>
        </div>

        <!-- Suggestions Floating Dropdown -->
        <div x-show="showDropdown && results.length > 0" 
             @click.away="showDropdown = false"
             style="position: absolute; top: 115%; left: 0; right: 0; background: var(--white); border: 1.5px solid var(--cream-dark); border-radius: 20px; box-shadow: 0 12px 36px rgba(0,0,0,0.15); z-index: 9999; max-height: 380px; overflow-y: auto; padding: 8px 0;"
             x-cloak>
            <template x-for="prod in results" :key="prod.id">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 18px; border-bottom: 1px solid var(--cream-dark); gap: 14px; transition: background 0.15s ease;"
                     onmouseover="this.style.background='rgba(230,126,34,0.06)'"
                     onmouseout="this.style.background='transparent'">
                    
                    <!-- Product Image & Details -->
                    <div style="display: flex; align-items: center; gap: 14px; flex: 1;">
                        <img :src="prod.image" style="width: 52px; height: 52px; object-fit: cover; border-radius: 12px; border: 1px solid var(--cream-dark); flex-shrink: 0;" onerror="this.src='https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800'">
                        <div>
                            <a :href="prod.url" target="_blank" style="font-weight: 700; color: var(--maroon); font-size: 0.95rem; text-decoration: none; display: block; line-height: 1.3;" x-text="prod.name"></a>
                            <div style="font-size: 0.78rem; color: var(--saffron-deep); font-weight: 600;" x-text="prod.brand"></div>
                            <span style="font-weight: 800; color: var(--maroon); font-size: 0.98rem;" x-text="prod.price"></span>
                        </div>
                    </div>

                    <!-- Quick + Add Button -->
                    <button type="button" 
                            @click="addDirectlyToCheckout(prod)" 
                            :disabled="addingId === prod.id"
                            style="background: var(--maroon); color: var(--white); border: none; padding: 8px 18px; border-radius: 30px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(137,15,20,0.2); flex-shrink: 0;">
                        <template x-if="addingId === prod.id">
                            <span><i class="fa-solid fa-spinner fa-spin me-1"></i> Adding...</span>
                        </template>
                        <template x-if="addingId !== prod.id">
                            <span><i class="fa-solid fa-plus me-1"></i> Add to Order</span>
                        </template>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <form @submit.prevent="submitOrder()">
        <div class="checkout-layout">
            <!-- Left Steps Column -->
            <div>
                <!-- Store Pickup Location Info Card -->
                <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                        <span style="background: var(--maroon); color: var(--white); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700;">1</span>
                        <span>Takeaway Pickup Location</span>
                    </h3>

                    <div style="background: var(--cream); border: 1.5px solid var(--cream-dark); border-radius: 16px; padding: 20px; display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--maroon); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h4 style="font-family: 'Playfair Display', serif; font-size: 1.15rem; color: var(--maroon); margin: 0 0 6px 0;">Desi Foods Hounslow Store</h4>
                            <p style="color: var(--charcoal-light); font-size: 0.9rem; line-height: 1.5; margin-bottom: 10px;">
                                <strong>Address:</strong> 3-4 Green Parade, Whitton Road, Hounslow, TW3 2EN<br>
                                <strong>Store Hours:</strong> Open Daily 09:00 AM &ndash; 09:00 PM<br>
                                <strong>Contact:</strong> +44 20 8570 1234
                            </p>
                            <span class="badge-status badge-success" style="font-size: 0.8rem; padding: 4px 12px; background: rgba(46,125,50,0.15); color: #2E7D32; font-weight: 700;">
                                <i class="fa-solid fa-circle-check me-1"></i> In-Store Takeaway Pickup Ready
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Takeaway Schedule (Date & Time Selection) -->
                <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <span style="background: var(--maroon); color: var(--white); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700;">2</span>
                        <span>Select Pickup Date & Time Slot</span>
                    </h3>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                        <div>
                            <label style="font-weight: 700; color: var(--maroon); font-size: 0.92rem; display: block; margin-bottom: 8px;">
                                <i class="fa-regular fa-calendar-days me-1" style="color: var(--saffron);"></i> When will you pick up? (Date)
                            </label>
                            <input type="date" name="pickup_date" x-model="pickupDate" min="{{ date('Y-m-d') }}" required
                                   style="width: 100%; padding: 12px 16px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; font-weight: 600; color: var(--maroon); outline: none; background: var(--white);">
                        </div>

                        <div>
                            <label style="font-weight: 700; color: var(--maroon); font-size: 0.92rem; display: block; margin-bottom: 8px;">
                                <i class="fa-regular fa-clock me-1" style="color: var(--saffron);"></i> Preferred Time Slot
                            </label>
                            <select name="pickup_time_slot" x-model="pickupTimeSlot" required
                                    style="width: 100%; padding: 12px 16px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; font-weight: 600; color: var(--maroon); outline: none; background: var(--white);">
                                <option value="09:00 AM - 11:00 AM">09:00 AM &ndash; 11:00 AM (Morning)</option>
                                <option value="11:00 AM - 01:00 PM">11:00 AM &ndash; 01:00 PM (Midday)</option>
                                <option value="01:00 PM - 03:00 PM">01:00 PM &ndash; 03:00 PM (Afternoon)</option>
                                <option value="03:00 PM - 05:00 PM">03:00 PM &ndash; 05:00 PM (Late Afternoon)</option>
                                <option value="05:00 PM - 07:00 PM">05:00 PM &ndash; 07:00 PM (Evening)</option>
                                <option value="07:00 PM - 09:00 PM">07:00 PM &ndash; 09:00 PM (Night)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Payment Option Card (Pay at Store) -->
                <div style="background: #E8F8F5; border: 1.5px solid #A3E4D7; border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 16px;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: #117864; color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h4 style="font-family: 'Playfair Display', serif; font-size: 1.15rem; color: #117864; margin-bottom: 2px;">Pay at Store on Takeaway Pickup</h4>
                        <p style="font-size: 0.88rem; color: #145A32; margin: 0;">Pay via cash or card when you arrive at our Hounslow store to collect your groceries.</p>
                    </div>
                    <input type="hidden" name="payment_method" value="cod">
                </div>
            </div>

            <!-- Right Column: Order Summary & Coupon -->
            <div>
                <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; box-shadow: var(--shadow-sm); position: sticky; top: 120px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 10px;">Order Review</h3>

                    <!-- Cart Items Preview -->
                    <div style="max-height: 240px; overflow-y: auto; margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px; padding-right: 4px;">
                        @foreach($cart->items as $item)
                            @php
                                $price = $item->variant ? $item->variant->effective_price : $item->product->effective_price;
                                $img = $item->variant && $item->variant->image ? $item->variant->image : ($item->product->primaryImage ? $item->product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800');
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ $img }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; border: 1px solid var(--cream-dark);">
                                    <div>
                                        <div style="font-weight: 600; color: var(--maroon); line-height: 1.2;">{{ $item->product->name }}</div>
                                        <div style="font-size: 0.78rem; color: var(--saffron-deep);">Qty: {{ $item->quantity }}</div>
                                    </div>
                                </div>
                                <span style="font-weight: 700; color: var(--maroon);">£{{ number_format($price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Coupon Code Input -->
                    <div style="margin-bottom: 20px; border-top: 1px solid var(--cream-dark); padding-top: 16px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--charcoal-light); margin-bottom: 6px; display: block;">Have a Promo Coupon Code?</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" x-model="couponCode" placeholder="ENTER CODE" style="flex: 1; padding: 10px 14px; border: 1.5px solid var(--cream-dark); border-radius: 10px; font-weight: 700; text-transform: uppercase; outline: none;" :disabled="couponApplied">
                            <button type="button" @click="applyCoupon()" class="btn btn-outline-primary btn-sm" style="border-radius: 10px; padding: 0 16px;" x-text="couponApplied ? 'Applied' : 'Apply'" :disabled="couponApplied"></button>
                        </div>
                        <template x-if="couponMessage">
                            <div :style="couponValid ? 'color: #2E7D32;' : 'color: #C0392B;'" style="font-size: 0.8rem; font-weight: 600; margin-top: 6px;" x-text="couponMessage"></div>
                        </template>
                    </div>

                    <!-- Pricing Summary Breakdown -->
                    @php
                        $subtotal = $cart->items->sum(function($i) {
                            $price = $i->variant ? $i->variant->effective_price : $i->product->effective_price;
                            return $price * $i->quantity;
                        });
                    @endphp

                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem;">
                        <span style="color: var(--charcoal-light);">Items Subtotal</span>
                        <span style="font-weight: 600; color: var(--maroon);">£{{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem;">
                        <span style="color: var(--charcoal-light);">In-Store Takeaway</span>
                        <span style="color: #2E7D32; font-weight: 700;">FREE</span>
                    </div>

                    <template x-if="discount > 0">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: #2E7D32;">
                            <span>Promo Discount</span>
                            <span style="font-weight: 700;" x-text="'-£' + discount.toFixed(2)"></span>
                        </div>
                    </template>

                    <div style="border-top: 2px solid var(--cream-dark); padding-top: 16px; margin-top: 10px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-size: 1.1rem; font-weight: 700; color: var(--maroon);">Grand Total</span>
                        <span style="font-family: 'Playfair Display', serif; font-size: 1.9rem; font-weight: 800; color: var(--maroon);" x-text="'£' + grandTotal.toFixed(2)"></span>
                    </div>

                    <!-- Additional Customer Note -->
                    <div style="margin-bottom: 20px;">
                        <textarea x-model="customerNote" placeholder="Special takeaway instructions (e.g. Please pack in extra paper bag)..." rows="2" style="width: 100%; padding: 10px 14px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.85rem; outline: none;"></textarea>
                    </div>

                    <!-- Confirm Order Button -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%; padding: 14px 24px; font-size: 1.1rem; font-weight: 700; border-radius: 30px;" :disabled="loading">
                        <span x-show="!loading"><i class="fa-solid fa-bag-shopping me-2"></i> Confirm Takeaway Order</span>
                        <span x-show="loading"><i class="fa-solid fa-spinner fa-spin me-2"></i> Processing Order...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    function checkoutApp() {
        return {
            pickupDate: '{{ date('Y-m-d') }}',
            pickupTimeSlot: '11:00 AM - 01:00 PM',
            couponCode: '',
            couponApplied: false,
            couponValid: false,
            couponMessage: '',
            discount: 0,
            baseSubtotal: {{ $subtotal }},
            shippingCost: 0,
            customerNote: '',
            loading: false,

            get grandTotal() {
                return Math.max(0, (this.baseSubtotal - this.discount) + this.shippingCost);
            },

            applyCoupon() {
                if (!this.couponCode.trim()) return;
                fetch("{{ route('checkout.coupon.verify') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ code: this.couponCode })
                })
                .then(res => res.json())
                .then(data => {
                    this.couponMessage = data.message;
                    if (data.valid) {
                        this.couponApplied = true;
                        this.couponValid = true;
                        this.discount = parseFloat(data.discount);
                    } else {
                        this.couponValid = false;
                    }
                })
                .catch(err => {
                    this.couponMessage = 'Error validating coupon.';
                    this.couponValid = false;
                });
            },

            submitOrder() {
                this.loading = true;
                
                fetch("{{ route('checkout.place') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        pickup_date: this.pickupDate,
                        pickup_time_slot: this.pickupTimeSlot,
                        coupon_code: this.couponApplied ? this.couponCode : null,
                        customer_note: this.customerNote
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        window.location.href = data.redirect_url;
                    } else {
                        showToast(data.message || 'Could not place order. Please try again.', 'error');
                    }
                })
                .catch(err => {
                    this.loading = false;
                    showToast('Error placing order.', 'error');
                });
            }
        }
    }

    function quickCheckoutSearch() {
        return {
            query: '',
            results: [],
            loading: false,
            showDropdown: false,
            addingId: null,

            fetchSuggestions() {
                if (this.query.trim().length < 2) {
                    this.results = [];
                    this.showDropdown = false;
                    return;
                }
                this.loading = true;
                fetch('/api/products/search?q=' + encodeURIComponent(this.query))
                    .then(res => res.json())
                    .then(data => {
                        this.results = data;
                        this.loading = false;
                        this.showDropdown = data.length > 0;
                    })
                    .catch(err => {
                        this.loading = false;
                        this.showDropdown = false;
                    });
            },

            addDirectlyToCheckout(product) {
                this.addingId = product.id;
                fetch("{{ route('cart.add') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: product.id,
                        quantity: 1
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.addingId = null;
                    if (data.success) {
                        window.location.reload();
                    } else {
                        showToast(data.message || 'Error adding product to cart.', 'error');
                    }
                })
                .catch(err => {
                    this.addingId = null;
                    showToast('Server error adding product to cart.', 'error');
                });
            }
        }
    }
</script>
@endsection
