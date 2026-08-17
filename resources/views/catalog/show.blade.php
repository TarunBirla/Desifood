@extends('layouts.app')

@section('title', $product->name . ' | Desi Foods Hounslow')

@section('content')

@php
    $inCart = in_array($product->id, $userCartProductIds ?? []);
    $inWishlist = in_array($product->id, $userWishlistProductIds ?? []);
@endphp

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;" x-data="productDetail()">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--muted); margin-bottom: 24px;">
        <a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; 
        <a href="{{ route('products.index') }}">Catalog</a> &nbsp;/&nbsp; 
        <span style="color: var(--maroon); font-weight: 600;">{{ $product->name }}</span>
    </div>

    <!-- Product Main Grid -->
    <div class="product-details-grid" style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 36px; box-shadow: var(--shadow-sm); margin-bottom: 48px;">
        <!-- Images Gallery -->
        <div>
            <div style="height: 440px; background: linear-gradient(135deg, var(--cream-warm) 0%, var(--cream-dark) 100%); border-radius: 20px; overflow: hidden; margin-bottom: 16px; position: relative; border: 1px solid var(--cream-dark);">
                <img :src="activeImage" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

            <div style="display: flex; gap: 12px; overflow-x: auto;">
                @foreach($product->images as $img)
                    <div @click="activeImage = '{{ $img->image_path }}'" 
                          style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; border: 2px solid var(--cream-dark); cursor: pointer;"
                          :style="activeImage === '{{ $img->image_path }}' ? 'border-color: var(--saffron);' : ''">
                        <img src="{{ $img->image_path }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Product Details -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <div style="font-size: 0.85rem; color: var(--saffron-deep); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
                    {{ $product->brand ? $product->brand->name : ($product->category ? $product->category->name : 'Desi Foods') }}
                </div>

                <!-- Wishlist Toggle -->
                <form action="{{ route('account.wishlist.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn btn-outline btn-sm" style="{{ $inWishlist ? 'color: var(--maroon); border-color: var(--maroon); background: rgba(137, 15, 20, 0.08);' : '' }}">
                        <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart me-1" style="{{ $inWishlist ? 'color: #e74c3c;' : '' }}"></i>
                        {{ $inWishlist ? 'Wishlisted' : 'Add to Wishlist' }}
                    </button>
                </form>
            </div>

            <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); line-height: 1.25; margin-bottom: 14px;">{{ $product->name }}</h1>
            
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div class="rating-stars" style="color: var(--gold); font-size: 0.9rem;">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <span style="color: var(--charcoal); font-weight: 600; margin-left: 4px;">{{ number_format($product->rating_avg, 1) }}</span>
                    <span style="color: var(--muted); font-size: 0.85rem;">({{ $product->reviews_count }} Customer Reviews)</span>
                </div>
                <span style="font-size: 0.85rem; color: var(--muted);">| SKU: <strong x-text="sku"></strong></span>
            </div>

            <!-- Price Container -->
            <div style="background-color: var(--cream); border: 1px solid var(--cream-dark); border-radius: 16px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: baseline; gap: 16px; flex-wrap: wrap;">
                <span style="font-size: 2.2rem; font-weight: 700; color: var(--maroon);" x-text="'£' + Number(price).toFixed(2)"></span>
                <span x-show="salePrice && salePrice < price" style="font-size: 1.2rem; color: var(--muted); text-decoration: line-through;" x-text="'£' + Number(price).toFixed(2)"></span>
                <span style="font-size: 0.85rem; color: #2E7D32; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-check"></i> 100% Authentic Indian Grocery
                </span>
            </div>

            <p style="color: var(--charcoal-light); font-size: 1rem; margin-bottom: 24px; line-height: 1.7;">
                {{ $product->description }}
            </p>

            <!-- Stock Availability -->
            <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 600; font-size: 0.9rem; color: var(--maroon);">Stock Status:</span>
                <template x-if="stock > 0">
                    <span class="badge-status badge-success"><i class="fa-solid fa-boxes-stacked me-1"></i> In Stock (<span x-text="stock"></span> available at Whitton Rd)</span>
                </template>
                <template x-if="stock <= 0">
                    <span class="badge-status badge-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Out of Stock</span>
                </template>
            </div>

            <!-- Add to Cart / Added in Cart Button -->
            @if($inCart)
                <a href="{{ route('cart.index') }}" class="btn btn-outline btn-block" style="color: var(--maroon); border-color: var(--saffron); background: rgba(230,126,34,0.1); font-size: 1.05rem; padding: 14px 24px; margin-bottom: 32px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-check"></i> Item Added to Cart (Click to View Shopping Cart)
                </a>
            @else
                <form action="{{ route('cart.add') }}" method="POST" style="display: flex; gap: 16px; margin-bottom: 32px;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">

                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 14px 24px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;" :disabled="stock <= 0">
                        <i class="fa-solid fa-plus"></i> <span>Add to Shopping Cart</span>
                    </button>
                </form>
            @endif

            <!-- Policy Assurances -->
            <div style="border-top: 1px solid var(--cream-dark); padding-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.88rem; color: var(--charcoal-light);">
                <div><i class="fa-solid fa-shield-halved me-2" style="color: var(--saffron);"></i> 100% Genuine Indian Brand</div>
                <div><i class="fa-solid fa-square-parking me-2" style="color: var(--saffron);"></i> Free Parking at Store (Whitton Rd)</div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function productDetail() {
        return {
            activeImage: '{{ $product->primaryImage ? $product->primaryImage->image_path : "https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800" }}',
            price: {{ $product->effective_price }},
            salePrice: {{ $product->sale_price ?: 0 }},
            stock: {{ $product->stock_quantity }},
            sku: '{{ $product->sku }}'
        }
    }
</script>
@endsection
