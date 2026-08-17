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
                        {{ $inWishlist ? '♥ Wishlisted' : '♡ Add to Wishlist' }}
                    </button>
                </form>
            </div>

            <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); line-height: 1.25; margin-bottom: 14px;">{{ $product->name }}</h1>
            
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div class="rating-stars">
                    <span>★★★★★</span> <span>{{ number_format($product->rating_avg, 1) }}</span>
                    <span style="color: var(--muted); font-size: 0.85rem;">({{ $product->reviews_count }} Customer Reviews)</span>
                </div>
                <span style="font-size: 0.85rem; color: var(--muted);">| SKU: <strong x-text="sku"></strong></span>
            </div>

            <!-- Price Container -->
            <div style="background-color: var(--cream); border: 1px solid var(--cream-dark); border-radius: 16px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: baseline; gap: 16px;">
                <span style="font-size: 2.2rem; font-weight: 700; color: var(--maroon);" x-text="'£' + Number(price).toFixed(2)"></span>
                <span x-show="salePrice && salePrice < price" style="font-size: 1.2rem; color: var(--muted); text-decoration: line-through;" x-text="'£' + Number(price).toFixed(2)"></span>
                <span style="font-size: 0.85rem; color: var(--saffron-deep); font-weight: 600;">✓ 100% Authentic Indian Grocery</span>
            </div>

            <p style="color: var(--charcoal-light); font-size: 1rem; margin-bottom: 24px; line-height: 1.7;">
                {{ $product->description }}
            </p>

            <!-- Stock Availability -->
            <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 600; font-size: 0.9rem; color: var(--maroon);">Stock Status:</span>
                <template x-if="stock > 0">
                    <span class="badge-status badge-success">In Stock (<span x-text="stock"></span> available at Whitton Rd)</span>
                </template>
                <template x-if="stock <= 0">
                    <span class="badge-status badge-danger">Out of Stock</span>
                </template>
            </div>

            <!-- Add to Cart / Added in Cart Button -->
            @if($inCart)
                <a href="{{ route('cart.index') }}" class="btn btn-outline btn-block" style="color: var(--maroon); border-color: var(--saffron); background: rgba(230,126,34,0.1); font-size: 1.05rem; padding: 14px 24px; margin-bottom: 32px; text-align: center;">
                    ✓ Item Added to Cart (Click to View Shopping Cart)
                </a>
            @else
                <form action="{{ route('cart.add') }}" method="POST" style="display: flex; gap: 16px; margin-bottom: 32px;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">

                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 14px 24px; font-size: 1.05rem;" :disabled="stock <= 0">
                        <span>+ Add to Shopping Cart</span>
                    </button>
                </form>
            @endif

            <!-- Policy Assurances -->
            <div style="border-top: 1px solid var(--cream-dark); padding-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.88rem; color: var(--charcoal-light);">
                <div>🌿 100% Genuine Indian Brand</div>
                <div>🚗 Free Parking at Store (Whitton Rd)</div>
            </div>
        </div>
    </div>

    <!-- Product Specifications & Customer Reviews -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 36px; box-shadow: var(--shadow-sm);">
        <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--maroon); margin-bottom: 20px;">Food Product Specifications</h2>
        @if($product->specifications)
            <table class="custom-table" style="margin-bottom: 40px;">
                <tbody>
                    @foreach($product->specifications as $key => $val)
                        <tr>
                            <td style="width: 30%; font-weight: 600; background: var(--cream); color: var(--maroon);">{{ $key }}</td>
                            <td>{{ $val }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--maroon); margin-bottom: 20px;">Customer Reviews & Ratings</h2>
        
        <!-- Review Submission Form for Logged in Users -->
        @auth
            <form action="{{ route('account.review.submit') }}" method="POST" style="background: var(--cream); border: 1px solid var(--cream-dark); padding: 24px; border-radius: 16px; margin-bottom: 32px;">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--maroon); margin-bottom: 12px;">Write a Customer Review</h4>
                
                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon);">Rating (1-5 Stars)</label>
                        <select name="rating" style="padding: 8px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-family: 'Inter', sans-serif;">
                            <option value="5">★★★★★ 5 - Excellent</option>
                            <option value="4">★★★★☆ 4 - Good</option>
                            <option value="3">★★★☆☆ 3 - Average</option>
                            <option value="2">★★☆☆☆ 2 - Poor</option>
                            <option value="1">★☆☆☆☆ 1 - Very Poor</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <textarea name="comment" rows="3" placeholder="Share your experience regarding freshness, taste, or packaging..." required style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.95rem; font-family: 'Inter', sans-serif;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Submit Verified Review</button>
            </form>
        @endauth

        <!-- Reviews List -->
        @if($product->reviews->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($product->reviews as $rev)
                    <div style="border-bottom: 1px solid var(--line-soft); padding-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <div style="font-weight: 600; color: var(--maroon);">{{ $rev->user ? $rev->user->name : 'Hounslow Shopper' }}</div>
                            <span style="font-size: 0.8rem; color: var(--muted);">{{ $rev->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="rating-stars" style="margin-bottom: 8px;">
                            @for($i=0; $i<$rev->rating; $i++) ★ @endfor
                            <span style="color: var(--saffron-deep); font-size: 0.78rem; font-weight: 600; margin-left: 8px;">✓ Verified Customer</span>
                        </div>
                        <p style="font-size: 0.95rem; color: var(--charcoal-light);">{{ $rev->comment }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--muted);">No reviews written yet for this item. Be the first to write a review!</p>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    function productDetail() {
        return {
            activeImage: '{{ $product->primaryImage ? $product->primaryImage->image_path : "https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800" }}',
            sku: '{{ $product->sku }}',
            price: {{ $product->effective_price }},
            salePrice: {{ $product->sale_price ?: 0 }},
            stock: {{ $product->stock }},
            quantity: 1
        }
    }
</script>
@endsection
