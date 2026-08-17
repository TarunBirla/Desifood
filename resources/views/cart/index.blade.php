@extends('layouts.app')

@section('title', 'Your Grocery Cart | Desi Foods Hounslow')

@section('content')

@php
    $cartItems = $cart && $cart->items->count() > 0 ? $cart->items->map(function($i) {
        return [
            'id' => $i->id,
            'product_id' => $i->product_id,
            'name' => $i->product->name,
            'slug' => $i->product->slug,
            'brand' => $i->product->brand ? $i->product->brand->name : 'Desi Foods',
            'image' => $i->variant && $i->variant->image ? $i->variant->image : ($i->product->primaryImage ? $i->product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800'),
            'price' => (float)$i->unit_price,
            'qty' => (int)$i->quantity,
            'subtotal' => (float)$i->subtotal,
        ];
    })->values() : collect([]);
@endphp

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;" x-data="shoppingCartApp({{ json_encode($cartItems) }})">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">Your Shopping Cart</h1>

    <template x-if="items.length > 0">
        <div class="cart-layout">
            <!-- Cart Items Table -->
            <div>
                <div class="table-responsive">
                    <table class="custom-table">
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
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight: 600; color: var(--maroon);" x-text="'£' + item.price.toFixed(2)"></td>
                                <td>
                                    <!-- Auto-Updating Quantity Stepper without manual submit button -->
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

            <!-- Summary Box -->
            <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; box-shadow: var(--shadow-sm); height: fit-content;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 10px;">Order Summary</h3>
                
                <template x-if="subtotal < 35">
                    <div style="background: rgba(230,126,34,0.1); color: var(--saffron-deep); padding: 10px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>Add <strong x-text="'£' + (35 - subtotal).toFixed(2)"></strong> more for FREE Doorstep Delivery!</span>
                    </div>
                </template>
                <template x-if="subtotal >= 35">
                    <div style="background: rgba(46, 125, 50, 0.1); color: #2E7D32; padding: 10px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>You qualify for FREE Delivery across Hounslow & UK!</span>
                    </div>
                </template>

                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem;">
                    <span style="color: var(--charcoal-light);">Subtotal</span>
                    <span style="font-weight: 600; color: var(--maroon);" x-text="'£' + subtotal.toFixed(2)"></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem;">
                    <span style="color: var(--charcoal-light);">VAT (Grocery 0%)</span>
                    <span style="font-weight: 600; color: var(--maroon);">£0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 0.95rem;">
                    <span style="color: var(--charcoal-light);">Shipping & Discounts</span>
                    <span style="color: var(--saffron-deep); font-weight: 600;">Calculated at Checkout</span>
                </div>

                <div style="border-top: 1px solid var(--cream-dark); padding-top: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: baseline;">
                    <span style="font-size: 1.1rem; font-weight: 700; color: var(--maroon);">Est. Subtotal</span>
                    <span style="font-size: 1.8rem; font-weight: 700; color: var(--maroon);" x-text="'£' + subtotal.toFixed(2)"></span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block" style="padding: 14px 24px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <span>Proceed to Checkout</span> <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </template>

    <template x-if="items.length === 0">
        <div style="background: var(--white); border: 1px solid var(--cream-dark); padding: 60px; text-align: center; border-radius: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 3.5rem; color: var(--saffron); margin-bottom: 16px;">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.6rem; color: var(--maroon); margin-bottom: 12px;">Your Cart is Empty</h3>
            <p style="color: var(--charcoal-light); margin-bottom: 24px;">Explore our 4,000+ authentic Indian spices, Basmati rice, and snacks to add items.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Start Shopping</a>
        </div>
    </template>
</div>

@endsection

@section('scripts')
<script>
    function shoppingCartApp(initialItems) {
        return {
            items: initialItems || [],
            get subtotal() {
                return this.items.reduce((sum, i) => sum + (i.price * i.qty), 0);
            },
            changeQuantity(item, delta) {
                const newQty = item.qty + delta;
                if (newQty < 1) return;
                
                item.qty = newQty;
                
                // AJAX call to update server cart without page reload
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
                        item.qty -= delta; // revert
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
            }
        }
    }
</script>
@endsection
