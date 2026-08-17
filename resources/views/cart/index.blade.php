@extends('layouts.app')

@section('title', 'Your Grocery Cart | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">Your Shopping Cart</h1>

    @if($cart && $cart->items->count() > 0)
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
                        @foreach($cart->items as $item)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <img src="{{ $item->variant && $item->variant->image ? $item->variant->image : ($item->product->primaryImage ? $item->product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800') }}" 
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px; border: 1px solid var(--cream-dark);">
                                        <div>
                                            <a href="{{ route('products.show', $item->product->slug) }}" style="font-weight: 600; color: var(--maroon);">
                                                {{ $item->product->name }}
                                            </a>
                                            <div style="font-size: 0.8rem; color: var(--saffron-deep); font-weight: 500;">
                                                {{ $item->product->brand ? $item->product->brand->name : 'Desi Foods' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight: 600; color: var(--maroon);">£{{ number_format($item->unit_price, 2) }}</td>
                                <td>
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display: flex; align-items: center; gap: 6px;">
                                        @csrf
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" style="width: 60px; padding: 6px; border: 1px solid var(--cream-dark); border-radius: 8px; text-align: center; font-weight: 600;">
                                        <button type="submit" class="btn btn-outline btn-sm" style="padding: 6px 10px;">Update</button>
                                    </form>
                                </td>
                                <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($item->subtotal, 2) }}</td>
                                <td>
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background: none; border: none; color: var(--saffron-deep); cursor: pointer; font-size: 0.88rem; font-weight: 600;">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Summary Box -->
            <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; box-shadow: var(--shadow-sm); height: fit-content;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 10px;">Order Summary</h3>
                
                @php
                    $subtotal = $cart->items->sum('subtotal');
                    $freeShippingThreshold = 30.00;
                    $neededForFreeShipping = max(0, $freeShippingThreshold - $subtotal);
                @endphp

                @if($neededForFreeShipping > 0)
                    <div style="background: rgba(230,126,34,0.1); color: var(--saffron-deep); padding: 10px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>Add £{{ number_format($neededForFreeShipping, 2) }} more for FREE Doorstep Delivery!</span>
                    </div>
                @else
                    <div style="background: rgba(46, 125, 50, 0.1); color: #2E7D32; padding: 10px 14px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>You qualify for FREE Delivery across Hounslow & UK!</span>
                    </div>
                @endif

                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem;">
                    <span style="color: var(--charcoal-light);">Subtotal</span>
                    <span style="font-weight: 600; color: var(--maroon);">£{{ number_format($subtotal, 2) }}</span>
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
                    <span style="font-size: 1.8rem; font-weight: 700; color: var(--maroon);">£{{ number_format($subtotal, 2) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block" style="padding: 14px 24px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <span>Proceed to Checkout</span> <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    @else
        <div style="background: var(--white); border: 1px solid var(--cream-dark); padding: 60px; text-align: center; border-radius: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 3.5rem; color: var(--saffron); margin-bottom: 16px;">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.6rem; color: var(--maroon); margin-bottom: 12px;">Your Cart is Empty</h3>
            <p style="color: var(--charcoal-light); margin-bottom: 24px;">Explore our 4,000+ authentic Indian spices, Basmati rice, and snacks to add items.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Start Shopping</a>
        </div>
    @endif
</div>

@endsection
