@extends('layouts.app')

@section('title', 'My Wishlist | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">My Wishlist</h1>

    <div class="catalog-layout">
        <!-- Sidebar -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 20px; height: fit-content; box-shadow: var(--shadow-sm);">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 6px;">
                <li><a href="{{ route('account.dashboard') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Dashboard</a></li>
                <li><a href="{{ route('account.orders') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">My Grocery Orders</a></li>
                <li><a href="{{ route('account.profile') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Profile Details</a></li>
                <li><a href="{{ route('account.addresses') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Saved Delivery Addresses</a></li>
                <li><a href="{{ route('account.wishlist') }}" style="display: block; padding: 12px 16px; font-weight: 700; color: var(--maroon); background: rgba(137, 15, 20, 0.08); border-radius: 12px;">Wishlist</a></li>
            </ul>
        </div>

        <!-- Wishlist Grid -->
        <div>
            @if(isset($wishlists) && $wishlists->count() > 0)
                <div class="product-grid">
                    @foreach($wishlists as $item)
                        @php $product = $item->product; @endphp
                        @if($product)
                            <div class="product-card">
                                <form action="{{ route('account.wishlist.toggle') }}" method="POST" style="position: absolute; top: 12px; right: 12px; z-index: 5;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="btn-wishlist" title="Remove from Wishlist">
                                        <i class="fa-solid fa-heart" style="color: #e74c3c;"></i>
                                    </button>
                                </form>

                                <a href="{{ route('products.show', $product->slug) }}" class="media-wrapper" style="display: block;">
                                    <img src="{{ $product->primaryImage ? $product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800' }}" alt="{{ $product->name }}">
                                </a>

                                <div class="content">
                                    <span class="category-name">{{ $product->brand ? $product->brand->name : 'Desi Foods' }}</span>
                                    <a href="{{ route('products.show', $product->slug) }}" class="title">{{ $product->name }}</a>
                                    
                                    <div class="price-row">
                                        <span class="price">£{{ number_format($product->effective_price, 2) }}</span>
                                    </div>

                                    <!-- Dual Action Buttons: View Details & Add to Cart -->
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 14px;">
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 10px; border-color: var(--cream-dark); color: var(--maroon);">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>

                                        <form action="{{ route('cart.add') }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 4px; padding: 8px 10px;">
                                                <i class="fa-solid fa-plus"></i> Add
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div style="background: var(--white); border: 1px solid var(--cream-dark); padding: 50px; text-align: center; border-radius: 20px; box-shadow: var(--shadow-sm);">
                    <i class="fa-regular fa-heart" style="font-size: 3rem; color: var(--saffron); margin-bottom: 14px; display: block;"></i>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--maroon); margin-bottom: 8px;">Your Wishlist is Empty</h3>
                    <p style="color: var(--charcoal-light); margin-bottom: 20px;">Explore our catalog and click the heart icon on any product to save it here.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Explore Food Catalog</a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
