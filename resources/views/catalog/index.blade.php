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
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--maroon); margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fa-solid fa-filter" style="font-size: 1rem; color: var(--saffron);"></i> Filter Groceries</span>
                <a href="{{ route('products.index') }}" style="font-size: 0.8rem; font-family: 'Inter', sans-serif; color: var(--saffron-deep); font-weight: 600; text-decoration: underline;">Reset All</a>
            </h3>

            <!-- Category Filter Clickable Tabs -->
            <div style="margin-bottom: 24px;">
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 12px;">Category Tabs</label>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <a href="{{ route('products.index', array_merge(request()->except(['category', 'page']))) }}" 
                       style="padding: 7px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; {{ !request('category') ? 'background: var(--maroon); color: var(--white); box-shadow: 0 4px 10px rgba(137,15,20,0.2);' : 'background: var(--cream); color: var(--maroon); border: 1px solid var(--cream-dark);' }}">
                        All Categories
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('products.index', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}" 
                           style="padding: 7px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; {{ request('category') == $cat->slug ? 'background: var(--maroon); color: var(--white); box-shadow: 0 4px 10px rgba(137,15,20,0.2);' : 'background: var(--cream); color: var(--maroon); border: 1px solid var(--cream-dark);' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Brand Filter Clickable Tabs -->
            <div style="margin-bottom: 24px;">
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 12px;">Brand Tabs</label>
                <div style="display: flex; flex-wrap: wrap; gap: 8px; max-height: 220px; overflow-y: auto; padding-right: 4px;">
                    <a href="{{ route('products.index', array_merge(request()->except(['brand', 'page']))) }}" 
                       style="padding: 6px 12px; border-radius: 16px; font-size: 0.82rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; {{ !request('brand') ? 'background: var(--saffron-deep); color: var(--white); box-shadow: 0 4px 10px rgba(230,126,34,0.2);' : 'background: var(--cream); color: var(--charcoal-light); border: 1px solid var(--cream-dark);' }}">
                        All Brands
                    </a>
                    @foreach($brands as $b)
                        <a href="{{ route('products.index', array_merge(request()->except(['brand', 'page']), ['brand' => $b->slug])) }}" 
                           style="padding: 6px 12px; border-radius: 16px; font-size: 0.82rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; {{ request('brand') == $b->slug ? 'background: var(--saffron-deep); color: var(--white); box-shadow: 0 4px 10px rgba(230,126,34,0.2);' : 'background: var(--cream); color: var(--charcoal-light); border: 1px solid var(--cream-dark);' }}">
                            {{ $b->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Price & Stock Filter Form -->
            <form action="{{ route('products.index') }}" method="GET">
                @foreach(request()->only(['category', 'brand', 'search', 'sort']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                <!-- Price Range -->
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 8px;">Price Range (£)</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <input type="number" step="0.01" name="min_price" placeholder="Min £" value="{{ request('min_price') }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.85rem; outline: none; background: var(--cream);">
                        <input type="number" step="0.01" name="max_price" placeholder="Max £" value="{{ request('max_price') }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.85rem; outline: none; background: var(--cream);">
                    </div>
                </div>

                <!-- Availability -->
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.88rem; color: var(--charcoal-light); display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()" style="accent-color: var(--maroon);">
                        <span>In Stock Only</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-sm" style="padding: 10px;">Apply Price Filter</button>
            </form>
        </aside>

        <!-- Catalog Items Grid -->
        <div>
            <!-- Top Controls bar with Clickable Sort Tabs -->
            <div style="background: var(--white); padding: 18px 24px; border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div style="font-size: 0.9rem; color: var(--charcoal-light); font-weight: 500;">
                    Showing <strong style="color: var(--maroon);">{{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}</strong> of {{ $products->total() }} Authentic Indian Groceries
                </div>
                
                <!-- Sort Tabs -->
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-size: 0.85rem; color: var(--maroon); font-weight: 600; margin-right: 4px;">Sort:</span>
                    
                    <a href="{{ route('products.index', array_merge(request()->except(['sort', 'page']), ['sort' => 'newest'])) }}" 
                       style="padding: 6px 12px; border-radius: 14px; font-size: 0.82rem; font-weight: 600; text-decoration: none; {{ request('sort', 'newest') == 'newest' ? 'background: var(--maroon); color: var(--white);' : 'background: var(--cream); color: var(--charcoal); border: 1px solid var(--cream-dark);' }}">
                        Newest
                    </a>
                    
                    <a href="{{ route('products.index', array_merge(request()->except(['sort', 'page']), ['sort' => 'price_low_high'])) }}" 
                       style="padding: 6px 12px; border-radius: 14px; font-size: 0.82rem; font-weight: 600; text-decoration: none; {{ request('sort') == 'price_low_high' ? 'background: var(--maroon); color: var(--white);' : 'background: var(--cream); color: var(--charcoal); border: 1px solid var(--cream-dark);' }}">
                        Price: Low to High
                    </a>

                    <a href="{{ route('products.index', array_merge(request()->except(['sort', 'page']), ['sort' => 'price_high_low'])) }}" 
                       style="padding: 6px 12px; border-radius: 14px; font-size: 0.82rem; font-weight: 600; text-decoration: none; {{ request('sort') == 'price_high_low' ? 'background: var(--maroon); color: var(--white);' : 'background: var(--cream); color: var(--charcoal); border: 1px solid var(--cream-dark);' }}">
                        Price: High to Low
                    </a>

                    <a href="{{ route('products.index', array_merge(request()->except(['sort', 'page']), ['sort' => 'best_rated'])) }}" 
                       style="padding: 6px 12px; border-radius: 14px; font-size: 0.82rem; font-weight: 600; text-decoration: none; {{ request('sort') == 'best_rated' ? 'background: var(--maroon); color: var(--white);' : 'background: var(--cream); color: var(--charcoal); border: 1px solid var(--cream-dark);' }}">
                        ★ Best Rated
                    </a>
                </div>
            </div>

            <!-- Active Filters Indicator Bar -->
            @if(request('category') || request('brand') || request('search') || request('min_price') || request('max_price'))
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                    <span style="font-size: 0.82rem; font-weight: 600; color: var(--muted);">Active Filters:</span>
                    @if(request('category'))
                        <span style="background: rgba(137,15,20,0.1); color: var(--maroon); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                            Category: {{ request('category') }}
                            <a href="{{ route('products.index', request()->except(['category', 'page'])) }}" style="color: var(--maroon); font-weight: 700; text-decoration: none;">&times;</a>
                        </span>
                    @endif
                    @if(request('brand'))
                        <span style="background: rgba(230,126,34,0.12); color: var(--saffron-deep); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                            Brand: {{ request('brand') }}
                            <a href="{{ route('products.index', request()->except(['brand', 'page'])) }}" style="color: var(--saffron-deep); font-weight: 700; text-decoration: none;">&times;</a>
                        </span>
                    @endif
                </div>
            @endif

            @if($products->count() > 0)
                <div class="product-grid">
                    @foreach($products as $product)
                        @php
                            $inWishlist = in_array($product->id, $userWishlistProductIds ?? []);
                            $inCart = in_array($product->id, $userCartProductIds ?? []);
                        @endphp
                        <div class="product-card">
                            <!-- Wishlist Form -->
                            <form action="{{ route('account.wishlist.toggle') }}" method="POST" style="position: absolute; top: 12px; right: 12px; z-index: 5;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn-wishlist" title="{{ $inWishlist ? 'Remove Wishlist' : 'Add Wishlist' }}">
                                    <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart" style="{{ $inWishlist ? 'color: #e74c3c;' : '' }}"></i>
                                </button>
                            </form>

                            <a href="{{ route('products.show', $product->slug) }}" class="media-wrapper" style="display: block;">
                                <img src="{{ $product->primaryImage ? $product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800' }}" alt="{{ $product->name }}">
                            </a>

                            <div class="content">
                                <span class="category-name">{{ $product->brand ? $product->brand->name : ($product->category ? $product->category->name : 'Desi Foods') }}</span>
                                <a href="{{ route('products.show', $product->slug) }}" class="title">{{ $product->name }}</a>
                                
                                <div class="rating-stars" style="margin-bottom: 10px; color: var(--gold); font-size: 0.85rem;">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <span style="color: var(--muted); font-size: 0.82rem; margin-left: 4px;">({{ $product->reviews_count }})</span>
                                </div>

                                <div class="price-row">
                                    <span class="price">£{{ number_format($product->effective_price, 2) }}</span>
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="original-price">£{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>

                                <!-- Dual Action Buttons: View Details & Add to Cart -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 14px;">
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 10px; border-color: var(--cream-dark); color: var(--maroon);">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>

                                    @if($inCart)
                                        <a href="{{ route('cart.index') }}" class="btn btn-outline btn-sm" style="color: var(--maroon); border-color: var(--saffron); background: rgba(230,126,34,0.1); width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 4px; padding: 8px 10px;">
                                            <i class="fa-solid fa-check"></i> Added
                                        </a>
                                    @else
                                        <button type="button" onclick="addToCartAjax({{ $product->id }}, 1, event)" class="btn btn-primary btn-sm" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 4px; padding: 8px 10px;">
                                            <i class="fa-solid fa-plus"></i> Add
                                        </button>
                                    @endif
                                </div>
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
                    <i class="fa-solid fa-basket-shopping" style="font-size: 3rem; color: var(--saffron); margin-bottom: 16px; display: block;"></i>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--maroon); margin-bottom: 8px;">No Products Found</h3>
                    <p style="color: var(--charcoal-light); margin-bottom: 20px;">Try resetting your search query or selecting a different category tab.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Reset All Filters</a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
