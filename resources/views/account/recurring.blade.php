@extends('layouts.app')

@section('title', 'Next-Month Recurring Orders | Desi Foods Account')

@section('content')
<div style="max-width: 1200px; margin: 40px auto; padding: 0 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--maroon); margin-bottom: 6px;">Next-Month Recurring Orders</h1>
            <p style="color: var(--charcoal-light); font-size: 0.95rem; margin: 0;">Items prepared for your upcoming <strong>{{ $targetMonthName }}</strong> grocery order.</p>
        </div>

        <a href="{{ route('account.orders') }}" class="btn btn-outline-primary" style="border-radius: 30px; font-weight: 600;">
            <i class="fa-solid fa-clock-rotate-left me-1"></i> View Order History
        </a>
    </div>

    <!-- Account Sidebar Navigation -->
    <div class="account-nav-wrapper" style="margin-bottom: 24px;">
        <a href="{{ route('account.dashboard') }}" class="account-nav-pill">
            <i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span>
        </a>
        <a href="{{ route('account.orders') }}" class="account-nav-pill">
            <i class="fa-solid fa-box"></i> <span>My Grocery Orders</span>
        </a>
        <a href="{{ route('account.recurring') }}" class="account-nav-pill active">
            <i class="fa-solid fa-repeat"></i> <span>Next-Month Orders</span>
        </a>
        <a href="{{ route('account.profile') }}" class="account-nav-pill">
            <i class="fa-solid fa-user-gear"></i> <span>Profile Details</span>
        </a>
        <a href="{{ route('account.wishlist') }}" class="account-nav-pill">
            <i class="fa-solid fa-heart"></i> <span>Wishlist</span>
        </a>
    </div>

    <!-- Important Notice Alert -->
    <div style="background: rgba(230,126,34,0.08); border-left: 4px solid var(--saffron); border-radius: 12px; padding: 16px 20px; margin-bottom: 32px;">
        <div style="display: flex; gap: 12px; align-items: flex-start;">
            <i class="fa-solid fa-circle-info" style="color: var(--saffron); font-size: 1.3rem; margin-top: 2px;"></i>
            <div>
                <strong style="color: var(--maroon); font-size: 1rem; display: block; margin-bottom: 4px;">Transparent & Flexible Recurring Selection</strong>
                <span style="color: var(--charcoal); font-size: 0.9rem; line-height: 1.5;">
                    Your recurring selections build a draft order for next month. <strong>We will NEVER automatically charge your card or silently place an order.</strong> When {{ $targetMonthName }} arrives, you will receive an email reminder to review your cart, edit quantities, add/remove items, and confirm payment manually.
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(46,125,50,0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 4px solid #2E7D32; margin-bottom: 24px; font-weight: 600;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(192,57,43,0.1); color: #C0392B; padding: 14px 20px; border-radius: 12px; border-left: 4px solid #C0392B; margin-bottom: 24px; font-weight: 600;">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif

    @if($recurringOrders->count() > 0)
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 32px;">
            <div style="padding: 20px 24px; background: var(--cream); border-bottom: 1px solid var(--cream-dark); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <span style="font-weight: 700; color: var(--maroon); font-size: 1.05rem;">
                    <i class="fa-solid fa-calendar-check me-2" style="color: var(--saffron);"></i>
                    Target Month: {{ $targetMonthName }} ({{ $recurringOrders->count() }} items)
                </span>
                
                <form action="{{ route('account.recurring.checkout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="font-weight: 700; padding: 10px 20px; border-radius: 30px;">
                        <i class="fa-solid fa-cart-shopping me-1"></i> Add All Items to Cart & Checkout
                    </button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Est. Subtotal</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalEst = 0; @endphp
                        @foreach($recurringOrders as $item)
                            @php 
                                $price = $item->variant ? $item->variant->effective_price : ($item->product ? $item->product->effective_price : $item->unit_price);
                                $subtotal = $price * $item->quantity;
                                $totalEst += $subtotal;
                                $img = $item->variant && $item->variant->image ? $item->variant->image : ($item->product && $item->product->primaryImage ? $item->product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800');
                            @endphp
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <img src="{{ $img }}" style="width: 54px; height: 54px; object-fit: cover; border-radius: 10px; border: 1px solid var(--cream-dark);">
                                        <div>
                                            <a href="{{ $item->product ? route('products.show', $item->product->slug) : '#' }}" style="font-weight: 600; color: var(--maroon); text-decoration: none;">
                                                {{ $item->product ? $item->product->name : 'Product' }}
                                            </a>
                                            @if($item->variant)
                                                <div style="font-size: 0.8rem; color: var(--muted);">Variant: {{ $item->variant->variant_name }}</div>
                                            @endif
                                            <div style="font-size: 0.78rem; color: var(--saffron-deep); font-weight: 600; margin-top: 2px;">
                                                <i class="fa-solid fa-rotate me-1"></i> Selected for {{ $item->target_month }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight: 600; color: var(--maroon);">£{{ number_format($price, 2) }}</td>
                                <td>
                                    <!-- Stylish Plus-Minus (+/-) Stepper Pill Widget -->
                                    <form action="{{ route('account.recurring.update', $item->id) }}" method="POST" style="display: inline-block; margin: 0;">
                                        @csrf
                                        <div style="display: inline-flex; align-items: center; border: 1.5px solid var(--cream-dark); border-radius: 30px; background: var(--white); padding: 4px 14px; gap: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                            <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" style="border: none; background: none; color: var(--maroon); font-size: 1rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 2px 4px; outline: none; transition: transform 0.15s ease;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='var(--maroon)'" title="Decrease quantity">
                                                <i class="fa-solid fa-minus" style="font-size: 0.85rem;"></i>
                                            </button>

                                            <span style="font-weight: 800; font-size: 1.1rem; color: var(--maroon); min-width: 24px; text-align: center; display: inline-block; user-select: none;">
                                                {{ $item->quantity }}
                                            </span>

                                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" style="border: none; background: none; color: var(--maroon); font-size: 1rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 2px 4px; outline: none; transition: transform 0.15s ease;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='var(--maroon)'" title="Increase quantity">
                                                <i class="fa-solid fa-plus" style="font-size: 0.85rem;"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($subtotal, 2) }}</td>
                                <td style="text-align: right;">
                                    <form action="{{ route('account.recurring.delete', $item->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Remove this item from next-month recurring selection?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; color: #C0392B; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                                            <i class="fa-solid fa-trash-can me-1"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 20px 24px; background: var(--cream); border-top: 1px solid var(--cream-dark); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <span style="font-size: 0.95rem; color: var(--charcoal-light);">Estimated Draft Order Subtotal:</span>
                    <strong style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--maroon); margin-left: 8px;">£{{ number_format($totalEst, 2) }}</strong>
                </div>

                <form action="{{ route('account.recurring.checkout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg" style="font-weight: 700; padding: 12px 28px; border-radius: 30px;">
                        Proceed to Review & Checkout <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 70px 20px; background: var(--white); border-radius: 20px; border: 1px solid var(--cream-dark);">
            <i class="fa-solid fa-rotate" style="font-size: 3.5rem; color: var(--cream-dark); margin-bottom: 18px; display: block;"></i>
            <h3 style="font-family: 'Playfair Display', serif; color: var(--maroon); font-size: 1.6rem; margin-bottom: 10px;">No Recurring Items Selected</h3>
            <p style="color: var(--charcoal-light); margin-bottom: 24px; max-width: 500px; margin-left: auto; margin-right: auto;">
                When browsing products or reviewing your cart, check the <strong>"Order Again Next Month"</strong> box on any item to save it for your next monthly grocery order!
            </p>
            <a href="{{ route('cart.index') }}" class="btn btn-primary btn-lg">View Shopping Cart</a>
        </div>
    @endif
</div>
@endsection
