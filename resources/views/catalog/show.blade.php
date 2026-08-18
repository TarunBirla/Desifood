@extends('layouts.app')

@section('title', $product->name . ' | Desi Foods Hounslow')

@section('content')

@php
    $inCart = in_array($product->id, $userCartProductIds ?? []);
    $inWishlist = in_array($product->id, $userWishlistProductIds ?? []);
    
    $primaryImg = $product->primaryImage ? $product->primaryImage->image_path : ($product->images->first() ? $product->images->first()->image_path : 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800');
@endphp

<div class="site-container" style="max-width: 1320px; margin: 40px auto; padding: 0 24px;" x-data="productDetail()">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--muted); margin-bottom: 24px;">
        <a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; 
        <a href="{{ route('products.index') }}">Catalog</a> &nbsp;/&nbsp; 
        <span style="color: var(--maroon); font-weight: 600;">{{ $product->name }}</span>
    </div>

    <!-- Product Main Grid -->
    <div class="product-details-grid" style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 36px; box-shadow: var(--shadow-sm); margin-bottom: 48px;">
        <!-- Single Featured Product Image -->
        <div>
            <div class="product-single-image-box" style="height: 480px; background: linear-gradient(135deg, var(--cream-warm) 0%, var(--cream-dark) 100%); border-radius: 20px; overflow: hidden; position: relative; border: 1px solid var(--cream-dark); box-shadow: var(--shadow-sm);">
                <img src="{{ $primaryImg }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
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

            <h1 class="product-single-title" style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); line-height: 1.25; margin-bottom: 14px;">{{ $product->name }}</h1>
            
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
                <div class="rating-stars" style="color: var(--gold); font-size: 0.9rem;">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <span style="color: var(--charcoal); font-weight: 600; margin-left: 4px;">{{ number_format($product->rating_avg, 1) }}</span>
                    <span style="color: var(--muted); font-size: 0.85rem;">({{ $product->reviews->count() }} Reviews)</span>
                </div>
                <span style="font-size: 0.85rem; color: var(--muted);">| SKU: <strong x-text="sku"></strong></span>
            </div>

            <!-- Price Container -->
            <div class="product-price-box" style="background-color: var(--cream); border: 1px solid var(--cream-dark); border-radius: 16px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: baseline; gap: 16px; flex-wrap: wrap;">
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

            <!-- Quantity Selector & AJAX Add to Cart Button -->
            <div class="product-action-row" style="display: flex; gap: 16px; margin-bottom: 32px; align-items: center; flex-wrap: wrap;" x-data="{ itemQty: 1 }">
                <div class="stepper-box" style="display: inline-flex; align-items: center; border: 1px solid var(--cream-dark); border-radius: 14px; background: var(--cream); overflow: hidden; height: 50px;">
                    <button type="button" @click="if (itemQty > 1) itemQty--" style="width: 44px; height: 100%; border: none; background: none; font-weight: 700; color: var(--maroon); cursor: pointer; font-size: 1.1rem;">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <input type="number" x-model.number="itemQty" readonly style="width: 50px; text-align: center; border: none; background: none; font-weight: 700; color: var(--maroon); font-size: 1.1rem; outline: none;">
                    <button type="button" @click="itemQty++" style="width: 44px; height: 100%; border: none; background: none; font-weight: 700; color: var(--maroon); cursor: pointer; font-size: 1.1rem;">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>

                <button type="button" @click="addToCartAjax({{ $product->id }}, itemQty, $event)" class="btn btn-primary btn-add-cart" style="flex: 1; min-width: 220px; height: 50px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;" :disabled="stock <= 0">
                    <i class="fa-solid fa-plus"></i> <span>Add to Shopping Cart</span>
                </button>
            </div>

            <!-- Policy Assurances -->
            <div style="border-top: 1px solid var(--cream-dark); padding-top: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 0.88rem; color: var(--charcoal-light);">
                <div><i class="fa-solid fa-shield-halved me-2" style="color: var(--saffron);"></i> 100% Genuine Indian Brand</div>
                <div><i class="fa-solid fa-square-parking me-2" style="color: var(--saffron);"></i> Free Parking at Store (Whitton Rd)</div>
            </div>
        </div>
    </div>

    <!-- Customer Reviews & Ratings Section -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 36px; box-shadow: var(--shadow-sm); margin-bottom: 48px;" x-data="{ userRating: 5 }">
        <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--maroon); margin-bottom: 24px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <span>Customer Ratings & Reviews</span>
            <span style="font-size: 1rem; color: var(--gold); font-family: 'Inter', sans-serif;">
                <i class="fa-solid fa-star"></i> {{ number_format($product->rating_avg, 1) }} out of 5
            </span>
        </h2>

        @if(session('success'))
            <div style="background: #E8F8F5; border: 1px solid #A3E4D7; color: #117864; padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
            <!-- Write a Review Form -->
            <div style="background: var(--cream); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px;">Write a Review</h3>

                @auth
                    <form action="{{ route('account.review.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="rating" :value="userRating">

                        <!-- Rating Stars Input -->
                        <div style="margin-bottom: 16px;">
                            <label style="font-size: 0.88rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Select Rating</label>
                            <div style="display: flex; gap: 8px; font-size: 1.5rem; color: var(--gold); cursor: pointer;">
                                <template x-for="star in 5">
                                    <i class="fa-solid fa-star" 
                                       @click="userRating = star"
                                       :style="star <= userRating ? 'color: var(--gold);' : 'color: var(--cream-dark);'"></i>
                                </template>
                            </div>
                        </div>

                        <!-- Review Title -->
                        <div style="margin-bottom: 16px;">
                            <label style="font-size: 0.88rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Review Headline / Title</label>
                            <input type="text" name="title" required placeholder="e.g. Authentic taste and fresh quality!" 
                                   style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--white);">
                        </div>

                        <!-- Comment Textarea -->
                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 0.88rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Your Detailed Review</label>
                            <textarea name="comment" rows="4" required placeholder="Share your experience cooking or tasting this Indian grocery item..." 
                                      style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--white);"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; font-size: 0.98rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fa-solid fa-paper-plane"></i> Submit Customer Review
                        </button>
                    </form>
                @else
                    <div style="text-align: center; padding: 30px 16px; background: var(--white); border-radius: 14px; border: 1px dashed var(--cream-dark);">
                        <i class="fa-solid fa-user-lock" style="font-size: 2.2rem; color: var(--saffron); margin-bottom: 12px; display: block;"></i>
                        <p style="color: var(--charcoal-light); font-size: 0.95rem; margin-bottom: 16px;">Please log in to rate and leave a review for this product.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Log In to Review</a>
                    </div>
                @endauth
            </div>

            <!-- Existing Customer Reviews List -->
            <div>
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px;">Verified Customer Reviews</h3>

                @if($product->reviews && $product->reviews->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px; max-height: 480px; overflow-y: auto; padding-right: 8px;">
                        @foreach($product->reviews as $rev)
                            <div style="background: var(--cream); border: 1px solid var(--cream-dark); border-radius: 16px; padding: 18px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <div>
                                        <div style="font-weight: 700; color: var(--maroon); font-size: 0.98rem;">{{ $rev->user ? $rev->user->name : 'Verified Customer' }}</div>
                                        <div style="color: var(--gold); font-size: 0.8rem; margin-top: 2px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star" style="{{ $i <= $rev->rating ? 'color: var(--gold);' : 'color: var(--cream-dark);' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <span style="font-size: 0.78rem; color: var(--muted);">{{ $rev->created_at->diffForHumans() }}</span>
                                </div>

                                @if($rev->title)
                                    <h4 style="font-size: 0.92rem; font-weight: 600; color: var(--charcoal); margin-bottom: 6px;">{{ $rev->title }}</h4>
                                @endif
                                <p style="font-size: 0.88rem; color: var(--charcoal-light); line-height: 1.6; margin: 0;">{{ $rev->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; background: var(--cream); border-radius: 16px; border: 1px solid var(--cream-dark);">
                        <i class="fa-solid fa-comments" style="font-size: 2.4rem; color: var(--saffron); margin-bottom: 12px; display: block;"></i>
                        <h4 style="font-family: 'Playfair Display', serif; color: var(--maroon); margin-bottom: 6px;">No Reviews Yet</h4>
                        <p style="color: var(--charcoal-light); font-size: 0.9rem;">Be the first customer to review {{ $product->name }}!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function productDetail() {
        return {
            activeImage: '{{ $primaryImg }}',
            price: {{ $product->effective_price }},
            salePrice: {{ $product->sale_price ?: 0 }},
            stock: {{ $product->stock }},
            sku: '{{ $product->sku }}'
        }
    }
</script>
@endsection
