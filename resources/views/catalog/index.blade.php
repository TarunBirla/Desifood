@extends('layouts.app')

@section('title', "Indian Grocery Catalog | Desi Foods Hounslow")

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--muted); margin-bottom: 24px;">
        <a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <span style="color: var(--maroon); font-weight: 600;">Indian Grocery Catalog</span>
    </div>

    <div class="catalog-layout">
        <!-- Filters Sidebar -->
        <aside style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; height: fit-content; box-shadow: var(--shadow-sm);">
            <form action="{{ route('products.index') }}" method="GET">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--maroon); margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 10px;">Filter Groceries</h3>

                <!-- Categories -->
                <div style="margin-bottom: 24px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 10px;">Categories</label>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-size: 0.9rem; color: var(--charcoal-light); display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span>All Categories</span>
                        </label>
                        @foreach($categories as $cat)
                            <label style="font-size: 0.9rem; color: var(--charcoal-light); display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                <span>{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Brands -->
                <div style="margin-bottom: 24px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 10px;">Brands</label>
                    <div style="display: flex; flex-direction: column; gap: 8px; max-height: 200px; overflow-y: auto;">
                        <label style="font-size: 0.9rem; color: var(--charcoal-light); display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="brand" value="" {{ !request('brand') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span>All Brands</span>
                        </label>
                        @foreach($brands as $b)
                            <label style="font-size: 0.9rem; color: var(--charcoal-light); display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" name="brand" value="{{ $b->slug }}" {{ request('brand') == $b->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                <span>{{ $b->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div style="margin-bottom: 24px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 10px;">Price Range (£)</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.85rem;">
                        <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.85rem;">
                    </div>
                </div>

                <!-- Availability -->
                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.9rem; color: var(--charcoal-light); display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                        <span>In Stock Only</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-sm">Apply Filters</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline btn-block btn-sm" style="margin-top: 8px; text-align: center;">Reset Filters</a>
            </form>
        </aside>

        <!-- Catalog Items Grid -->
        <div>
            <!-- Top Controls bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; background: var(--white); padding: 16px 24px; border: 1px solid var(--cream-dark); border-radius: 16px; box-shadow: var(--shadow-sm); flex-wrap: wrap; gap: 12px;">
                <span style="font-size: 0.9rem; color: var(--charcoal-light); font-weight: 500;">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} Authentic Indian Groceries</span>
                
                <!-- Sort Dropdown -->
                <form action="{{ route('products.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
                    @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <label style="font-size: 0.88rem; color: var(--maroon); font-weight: 600;">Sort By:</label>
                    <select name="sort" onchange="this.form.submit()" style="padding: 8px 14px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.88rem; outline: none; background: var(--white); color: var(--charcoal);">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="best_rated" {{ request('sort') == 'best_rated' ? 'selected' : '' }}>Best Rated</option>
                    </select>
                </form>
            </div>

            <!-- Grid -->
            @if($products->count() > 0)
                <div class="product-grid">
                    @foreach($products as $product)
                        @php
                            $inCart = in_array($product->id, $userCartProductIds ?? []);
                            $inWishlist = in_array($product->id, $userWishlistProductIds ?? []);
                        @endphp
                        <div class="product-card">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="badge-discount">Sale</span>
                            @else
                                <span class="badge-discount" style="background: var(--gold); color: var(--charcoal);">Bestseller</span>
                            @endif

                            <form action="{{ route('account.wishlist.toggle') }}" method="POST" style="position: absolute; top: 12px; right: 12px; z-index: 5;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn-wishlist" title="{{ $inWishlist ? 'Remove Wishlist' : 'Add Wishlist' }}">
                                    {{ $inWishlist ? '♥' : '♡' }}
                                </button>
                            </form>

                            <div class="media-wrapper">
                                <img src="{{ $product->primaryImage ? $product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800' }}" alt="{{ $product->name }}">
                            </div>

                            <div class="content">
                                <span class="category-name">{{ $product->brand ? $product->brand->name : ($product->category ? $product->category->name : 'Desi Foods') }}</span>
                                <a href="{{ route('products.show', $product->slug) }}" class="title">{{ $product->name }}</a>
                                
                                <div class="rating-stars" style="margin-bottom: 10px;">
                                    <span>★★★★★</span>
                                    <span style="color: var(--muted); font-size: 0.8rem;">({{ $product->reviews_count }})</span>
                                </div>

                                <div class="price-row">
                                    <span class="price">£{{ number_format($product->effective_price, 2) }}</span>
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="original-price">£{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>

                                @if($inCart)
                                    <a href="{{ route('cart.index') }}" class="btn btn-outline btn-sm btn-block" style="margin-top: 14px; color: var(--maroon); border-color: var(--saffron); background: rgba(230,126,34,0.1);">
                                        ✓ Added in Cart
                                    </a>
                                @else
                                    <form action="{{ route('cart.add') }}" method="POST" style="margin-top: 14px;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                                            + Add to Cart
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div style="margin-top: 40px;">
                    {{ $products->links() }}
                </div>
            @else
                <div style="background: var(--white); border: 1px solid var(--cream-dark); padding: 60px; text-align: center; border-radius: 20px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--maroon); margin-bottom: 8px;">No Food Products Found</h3>
                    <p style="color: var(--charcoal-light); margin-bottom: 20px;">Try adjusting your search query or clear your category filter.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Reset All Filters</a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
