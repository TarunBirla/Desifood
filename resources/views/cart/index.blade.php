@extends('layouts.app')

@section('title', 'Your Grocery Cart | Desi Foods Hounslow')

@section('content')

@php
    $cartItems = $cart && $cart->items->count() > 0 ? $cart->items->map(function($i) use ($userRecurringProductIds) {
        return [
            'id' => $i->id,
            'product_id' => $i->product_id,
            'variant_id' => $i->variant_id,
            'name' => $i->product->name,
            'slug' => $i->product->slug,
            'brand' => $i->product->brand ? $i->product->brand->name : 'Desi Foods',
            'image' => $i->variant && $i->variant->image ? $i->variant->image : ($i->product->primaryImage ? $i->product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800'),
            'price' => (float)$i->unit_price,
            'qty' => (int)$i->quantity,
            'subtotal' => (float)$i->subtotal,
            'is_recurring' => in_array($i->product_id, $userRecurringProductIds ?? []),
        ];
    })->values() : collect([]);
@endphp

<div class="site-container" style="max-width: 1320px; margin: 40px auto; padding: 0 24px;" x-data="shoppingCartApp({{ json_encode($cartItems) }})">
    <h1 class="site-page-title" style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">Your Shopping Cart</h1>

    <template x-if="items.length > 0">
        <div class="cart-layout">
            <!-- Cart Items Container -->
            <div>
                <!-- Desktop Table View (>= 768px) -->
                <div class="cart-desktop-table">
                    <div class="table-responsive">
                        <table class="custom-table" style="width: 100%; min-width: 600px;">
                            <thead>
                                <tr>
                                    <th>Grocery Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="item.id">
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 16px;">
                                                <img :src="item.image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px; border: 1px solid var(--cream-dark);">
                                                <div>
                                                    <a :href="'/products/' + item.slug" style="font-weight: 600; color: var(--maroon);" x-text="item.name"></a>
                                                    <div style="font-size: 0.8rem; color: var(--saffron-deep); font-weight: 500;" x-text="item.brand"></div>
                                                    
                                                    <!-- Recurring Next-Month Checkbox -->
                                                    <label style="margin-top: 6px; display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; color: var(--charcoal); cursor: pointer; background: rgba(230,126,34,0.08); padding: 4px 10px; border-radius: 20px; border: 1px solid rgba(230,126,34,0.2);" title="Select to prepare a draft order for next month. You won't be charged automatically.">
                                                        <input type="checkbox" :checked="item.is_recurring" @change="toggleRecurring(item, $event)" style="accent-color: var(--saffron); cursor: pointer;">
                                                        <span style="font-weight: 600; color: var(--maroon);"><i class="fa-solid fa-repeat me-1" style="color: var(--saffron);"></i> Order Again Next Month ({{ $targetMonthName ?? 'Next Month' }})</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-weight: 600; color: var(--maroon);" x-text="'£' + item.price.toFixed(2)"></td>
                                        <td>
                                            <div style="display: inline-flex; align-items: center; border: 1px solid var(--cream-dark); border-radius: 12px; background: var(--cream); overflow: hidden;">
                                                <button type="button" @click="changeQuantity(item, -1)" style="width: 34px; height: 34px; border: none; background: none; font-weight: 700; color: var(--maroon); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fa-solid fa-minus" style="font-size: 0.75rem;"></i>
                                                </button>
                                                <input type="number" x-model.number="item.qty" readonly style="width: 42px; text-align: center; border: none; background: none; font-weight: 700; color: var(--maroon); font-size: 0.95rem; outline: none;">
                                                <button type="button" @click="changeQuantity(item, 1)" style="width: 34px; height: 34px; border: none; background: none; font-weight: 700; color: var(--maroon); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td style="font-weight: 700; color: var(--maroon);" x-text="'£' + (item.price * item.qty).toFixed(2)"></td>
                                        <td>
                                            <button type="button" @click="removeItem(item, index)" style="background: none; border: none; color: #C0392B; cursor: pointer; font-size: 0.88rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-trash-can"></i> Remove
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Grocery Cards View (< 768px) -->
                <div class="cart-mobile-cards" style="display: flex; flex-direction: column; gap: 14px;">
                    <template x-for="(item, index) in items" :key="'mob-' + item.id">
                        <div style="background: var(--white); border: 1.5px solid var(--cream-dark); border-radius: 18px; padding: 16px; box-shadow: var(--shadow-sm); position: relative;">
                            <!-- Top Item Row: Image + Name/Brand + Remove Button -->
                            <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 12px;">
                                <img :src="item.image" style="width: 64px; height: 64px; object-fit: cover; border-radius: 12px; border: 1px solid var(--cream-dark); flex-shrink: 0;">
                                <div style="flex: 1; padding-right: 28px;">
                                    <a :href="'/products/' + item.slug" style="font-weight: 700; color: var(--maroon); font-size: 0.98rem; line-height: 1.35; display: block; margin-bottom: 2px;" x-text="item.name"></a>
                                    <div style="font-size: 0.78rem; color: var(--saffron-deep); font-weight: 600;" x-text="item.brand"></div>
                                </div>
                                <button type="button" @click="removeItem(item, index)" style="position: absolute; top: 14px; right: 14px; background: none; border: none; color: #C0392B; cursor: pointer; font-size: 1.1rem; padding: 4px;" title="Remove Item">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>

                            <!-- Recurring Selection Box -->
                            <div style="margin-bottom: 14px;">
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--charcoal); cursor: pointer; background: rgba(230,126,34,0.08); padding: 6px 12px; border-radius: 14px; border: 1px solid rgba(230,126,34,0.2);">
                                    <input type="checkbox" :checked="item.is_recurring" @change="toggleRecurring(item, $event)" style="accent-color: var(--saffron); cursor: pointer; width: 16px; height: 16px;">
                                    <span style="font-weight: 600; color: var(--maroon);"><i class="fa-solid fa-repeat me-1" style="color: var(--saffron);"></i> Order Again Next Month ({{ $targetMonthName ?? 'Next Month' }})</span>
                                </label>
                            </div>

                            <!-- Bottom Row: Price, Stepper & Subtotal -->
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed var(--cream-dark); padding-top: 12px;">
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--muted); display: block;">Unit Price</span>
                                    <span style="font-weight: 700; color: var(--maroon); font-size: 1rem;" x-text="'£' + item.price.toFixed(2)"></span>
                                </div>

                                <!-- Stepper -->
                                <div style="display: inline-flex; align-items: center; border: 1px solid var(--cream-dark); border-radius: 12px; background: var(--cream); overflow: hidden; height: 36px;">
                                    <button type="button" @click="changeQuantity(item, -1)" style="width: 32px; height: 100%; border: none; background: none; font-weight: 700; color: var(--maroon); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-minus" style="font-size: 0.75rem;"></i>
                                    </button>
                                    <input type="number" x-model.number="item.qty" readonly style="width: 36px; text-align: center; border: none; background: none; font-weight: 700; color: var(--maroon); font-size: 0.95rem; outline: none;">
                                    <button type="button" @click="changeQuantity(item, 1)" style="width: 32px; height: 100%; border: none; background: none; font-weight: 700; color: var(--maroon); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>

                                <div style="text-align: right;">
                                    <span style="font-size: 0.75rem; color: var(--muted); display: block;">Subtotal</span>
                                    <span style="font-weight: 800; color: var(--maroon); font-size: 1.1rem;" x-text="'£' + (item.price * item.qty).toFixed(2)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Summary Box -->
            <div class="cart-summary-box" style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm); height: fit-content; width: 100%; box-sizing: border-box;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 10px;">Order Summary</h3>
                
                <template x-if="subtotal < 35">
                    <div style="background: rgba(230,126,34,0.1); color: var(--saffron-deep); padding: 10px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>Add <strong x-text="'£' + (35 - subtotal).toFixed(2)"></strong> more for FREE Doorstep Delivery!</span>
                    </div>
                </template>
                <template x-if="subtotal >= 35">
                    <div style="background: rgba(46, 125, 50, 0.1); color: #2E7D32; padding: 10px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>You qualify for FREE Delivery across Hounslow & UK!</span>
                    </div>
                </template>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 0.95rem; width: 100%; box-sizing: border-box;">
                    <span style="color: var(--charcoal-light);">Subtotal</span>
                    <span style="font-weight: 700; color: var(--maroon);" x-text="'£' + subtotal.toFixed(2)"></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 0.95rem; width: 100%; box-sizing: border-box;">
                    <span style="color: var(--charcoal-light);">VAT (Grocery 0%)</span>
                    <span style="font-weight: 700; color: var(--maroon);">£0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 0.95rem; width: 100%; box-sizing: border-box;">
                    <span style="color: var(--charcoal-light);">Shipping & Discounts</span>
                    <span style="color: var(--saffron-deep); font-weight: 600;">Calculated at Checkout</span>
                </div>

                <div style="border-top: 1px solid var(--cream-dark); padding-top: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: baseline; width: 100%; box-sizing: border-box;">
                    <span style="font-size: 1.1rem; font-weight: 700; color: var(--maroon);">Est. Subtotal</span>
                    <span style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: var(--maroon);" x-text="'£' + subtotal.toFixed(2)"></span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary" style="text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; box-sizing: border-box; font-size: 1rem; padding: 14px 20px; font-weight: 700; border-radius: 50px;">
                    <span>Proceed to Checkout</span> <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </template>

    <template x-if="items.length === 0">
        <div style="text-align: center; padding: 80px 20px; background: var(--white); border-radius: 24px; border: 1px solid var(--cream-dark);">
            <i class="fa-solid fa-basket-shopping" style="font-size: 4rem; color: var(--cream-dark); margin-bottom: 20px; display: block;"></i>
            <h2 style="font-family: 'Playfair Display', serif; color: var(--maroon); font-size: 1.8rem; margin-bottom: 12px;">Your Shopping Cart is Empty</h2>
            <p style="color: var(--charcoal-light); margin-bottom: 28px;">Explore our authentic Indian groceries, spices, basmati rice, and fresh sweets!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Browse Food Catalog</a>
        </div>
    </template>
</div>

@endsection

@section('scripts')
<script>
    function shoppingCartApp(initialItems) {
        return {
            items: initialItems,
            get subtotal() {
                return this.items.reduce((sum, i) => sum + (i.price * i.qty), 0);
            },
            changeQuantity(item, delta) {
                const newQty = item.qty + delta;
                if (newQty < 1) return;
                
                item.qty = newQty;
                
                fetch(`/cart/update/${item.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ quantity: newQty })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const badge = document.getElementById('globalCartCountBadge');
                        if (badge) badge.textContent = data.cart_count;
                        showToast('Cart quantity updated.');
                    } else {
                        item.qty -= delta;
                        showToast(data.message || 'Could not update stock.', 'error');
                    }
                })
                .catch(err => {
                    item.qty -= delta;
                    showToast('Error updating cart.', 'error');
                });
            },
            removeItem(item, index) {
                if (confirm(`Remove ${item.name} from cart?`)) {
                    fetch(`/cart/remove/${item.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.items.splice(index, 1);
                        const badge = document.getElementById('globalCartCountBadge');
                        if (badge) {
                            badge.textContent = data.cart_count;
                            if (data.cart_count <= 0) badge.style.display = 'none';
                        }
                        showToast('Item removed from cart.');
                    });
                }
            },
            toggleRecurring(item, event) {
                const checked = event.target.checked;
                item.is_recurring = checked;
                
                fetch("{{ route('cart.recurring.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: item.product_id,
                        variant_id: item.variant_id || null,
                        recurring: checked ? 1 : 0,
                        quantity: item.qty
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                    } else {
                        event.target.checked = !checked;
                        item.is_recurring = !checked;
                        showToast(data.message || 'Could not update recurring preference.', 'error');
                    }
                })
                .catch(err => {
                    event.target.checked = !checked;
                    item.is_recurring = !checked;
                    showToast('Error updating recurring order preference.', 'error');
                });
            }
        }
    }
</script>
@endsection
