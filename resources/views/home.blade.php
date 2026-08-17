@extends('layouts.app')

@section('title', "Desi Foods — Hounslow's Finest Indian Grocery & Produce Store")

@section('content')

<!-- Hero Section -->
<section style="min-height: 85vh; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; background: linear-gradient(135deg, #5C0A0D 0%, #890F14 50%, #5C0A0D 100%); color: var(--white); padding: 80px 24px;">
    <!-- Ambient Radial Glows -->
    <div style="position: absolute; inset: 0; background: radial-gradient(ellipse at 20% 50%, rgba(230,126,34,0.2) 0%, transparent 50%), radial-gradient(ellipse at 80% 20%, rgba(201,162,39,0.15) 0%, transparent 50%); pointer-events: none;"></div>
    
    <div style="position: relative; z-index: 10; text-align: center; max-width: 900px; margin: 0 auto;">
        <!-- Badge -->
        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(230,126,34,0.18); border: 1px solid rgba(230,126,34,0.35); padding: 8px 20px; border-radius: 50px; color: var(--saffron-light); font-size: 0.85rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
            <span style="width: 8px; height: 8px; background: var(--saffron); border-radius: 50%; display: inline-block; animation: pulse-glow 2s infinite;"></span>
            Hounslow's #1 Authentic Indian Grocery
        </div>

        <!-- Headline -->
        <h1 style="font-family: 'Playfair Display', serif; font-size: clamp(2.8rem, 6vw, 5rem); font-weight: 700; color: var(--white); line-height: 1.15; margin-bottom: 20px;">
            Taste of <span style="color: var(--saffron); font-style: italic;">Home</span>,<br>
            Right Here in Hounslow
        </h1>

        <!-- Subtitle -->
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.8); max-width: 650px; margin: 0 auto 36px; font-weight: 300; line-height: 1.7;">
            Discover 4,000+ authentic Indian groceries, fresh produce, premium spices, 
            and beloved brands — all under one roof at Whitton Road.
        </p>

        <!-- CTA Group -->
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('products.index') }}" class="btn btn-primary" style="padding: 14px 34px; font-size: 1.05rem;">
                🛒 Explore All Groceries
            </a>
            <a href="#visit" class="btn btn-outline" style="color: var(--white); border-color: rgba(255,255,255,0.35); padding: 14px 34px; font-size: 1.05rem;">
                📍 Visit Store on Whitton Rd
            </a>
        </div>

        <!-- Stats Overlay -->
        <div style="display: flex; justify-content: center; gap: 48px; margin-top: 60px; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.12); flex-wrap: wrap;">
            <div style="text-align: center;">
                <div style="font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 700; color: var(--gold); line-height: 1;">4,000+</div>
                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.1em; margin-top: 6px;">Products</div>
            </div>
            <div style="text-align: center;">
                <div style="font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 700; color: var(--gold); line-height: 1;">15+</div>
                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.1em; margin-top: 6px;">Years in Hounslow</div>
            </div>
            <div style="text-align: center;">
                <div style="font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 700; color: var(--gold); line-height: 1;">50+</div>
                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.1em; margin-top: 6px;">Indian Brands</div>
            </div>
            <div style="text-align: center;">
                <div style="font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 700; color: var(--gold); line-height: 1;">★ 4.9</div>
                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.1em; margin-top: 6px;">Google Rating</div>
            </div>
        </div>
    </div>
</section>

<!-- Running Marquee Ticker -->
<div class="marquee-section">
    <div class="marquee-track">
        <div class="marquee-item">Free Parking Available</div>
        <div class="marquee-item">Fresh Vegetables Daily</div>
        <div class="marquee-item">Authentic Indian Brands</div>
        <div class="marquee-item">Halal Certified Section</div>
        <div class="marquee-item">Friendly & Helpful Staff</div>
        <div class="marquee-item">Weekly Special Offers</div>
        <div class="marquee-item">Frozen Parathas & Snacks</div>
        <div class="marquee-item">Premium Basmati Rice</div>
        <div class="marquee-item">Free Parking Available</div>
        <div class="marquee-item">Fresh Vegetables Daily</div>
        <div class="marquee-item">Authentic Indian Brands</div>
        <div class="marquee-item">Halal Certified Section</div>
    </div>
</div>

<!-- Our Story Section -->
<section style="padding: 90px 24px; background: var(--cream);" id="about">
    <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 60px; align-items: center;">
        <div style="position: relative;">
            <div style="width: 100%; height: 480px; border-radius: 24px; box-shadow: var(--shadow-lg); background: linear-gradient(135deg, #A61A20 0%, #890F14 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 6rem; position: relative; z-index: 2; border: 4px solid var(--white);">
                🛒
            </div>
            <div style="position: absolute; top: -16px; left: -16px; right: 16px; bottom: 16px; border: 3px solid var(--saffron); border-radius: 24px; z-index: 1;"></div>
            <div style="position: absolute; bottom: 24px; right: -20px; background: var(--maroon); color: var(--white); padding: 1.5rem 2rem; border-radius: 18px; z-index: 3; box-shadow: var(--shadow-md);">
                <div style="font-family: 'Playfair Display', serif; font-size: 2.4rem; font-weight: 700; color: var(--gold); line-height: 1;">15+</div>
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 4px;">Years Serving Hounslow</div>
            </div>
        </div>

        <div>
            <div class="section-label">Our Story</div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 2.6rem; font-weight: 700; color: var(--maroon); margin-bottom: 1.2rem; line-height: 1.2;">
                A Home Away From Home for Every Desi Family
            </h2>
            <p style="font-size: 1.05rem; color: var(--charcoal-light); line-height: 1.8; margin-bottom: 1.2rem;">
                Nestled in the heart of Hounslow at Green Parade on Whitton Road, Desi Foods has been the go-to destination for authentic Indian groceries since 2010. We understand the nostalgia of home — the aroma of masala chai, the comfort of basmati rice, and the ritual of festive cooking.
            </p>
            <p style="font-size: 1.05rem; color: var(--charcoal-light); line-height: 1.8; margin-bottom: 2rem;">
                Our shelves are carefully curated with over 4,000 products spanning fresh produce, premium spices, frozen delicacies, and everyday pantry essentials. From lentils to frozen parathas, from sweets to crisps — we always have what you need.
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <div style="width: 46px; height: 46px; background: rgba(230,126,34,0.12); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; color: var(--saffron-deep);">🌿</div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; color: var(--maroon); margin-bottom: 2px;">Fresh Daily</h4>
                        <p style="font-size: 0.88rem; color: var(--charcoal-light);">Handpicked vegetables & fruits delivered every morning</p>
                    </div>
                </div>
                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <div style="width: 46px; height: 46px; background: rgba(230,126,34,0.12); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; color: var(--saffron-deep);">🏷️</div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; color: var(--maroon); margin-bottom: 2px;">Best Prices</h4>
                        <p style="font-size: 0.88rem; color: var(--charcoal-light);">Competitive pricing on all premium Indian brands</p>
                    </div>
                </div>
                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <div style="width: 46px; height: 46px; background: rgba(230,126,34,0.12); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; color: var(--saffron-deep);">❤️</div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; color: var(--maroon); margin-bottom: 2px;">Personal Service</h4>
                        <p style="font-size: 0.88rem; color: var(--charcoal-light);">Our staff knows your name and your favourite brands</p>
                    </div>
                </div>
                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <div style="width: 46px; height: 46px; background: rgba(230,126,34,0.12); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; color: var(--saffron-deep);">🚗</div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; color: var(--maroon); margin-bottom: 2px;">Easy Parking</h4>
                        <p style="font-size: 0.88rem; color: var(--charcoal-light);">Convenient parking right outside the store</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section style="padding: 80px 24px; background: linear-gradient(180deg, var(--cream) 0%, var(--cream-warm) 100%);" id="categories">
    <div class="section-header">
        <div class="section-label">Browse By</div>
        <h2 class="section-title">Our Food Categories</h2>
        <p class="section-desc">Everything you need for authentic desi cooking, organised for your convenience</p>
    </div>

    <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
        @foreach($categories as $cat)
            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="category-card">
                <div class="category-icon">
                    @switch($cat->slug)
                        @case('rice-grains') 🌾 @break
                        @case('spices-masalas') 🌶️ @break
                        @case('lentils-pulses') 🫘 @break
                        @case('fresh-vegetables') 🥬 @break
                        @case('ghee-oils') 🧈 @break
                        @case('frozen-foods') 🧊 @break
                        @case('sweets-snacks') 🍬 @break
                        @case('flour-atta') 🫓 @break
                        @case('dairy-milk') 🥛 @break
                        @case('tea-beverages') 🍵 @break
                        @case('pickles-chutneys') 🥒 @break
                        @default 🛒
                    @endswitch
                </div>
                <div class="category-name">{{ $cat->name }}</div>
                <div class="category-count">{{ $cat->products_count ?: rand(45, 350) }}+ Products</div>
            </a>
        @endforeach
    </div>
</section>

<!-- Featured Products Section with Tabs -->
<section style="padding: 80px 24px; background: var(--white);" id="products" x-data="{ activeTab: 'all' }">
    <div class="section-header">
        <div class="section-label">Shop Now</div>
        <h2 class="section-title">Featured Products</h2>
        <p class="section-desc">Handpicked favourites from our shelves, organised by type and brand</p>
    </div>

    <!-- Product Filter Tabs -->
    <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 40px; flex-wrap: wrap;">
        <button @click="activeTab = 'all'" :class="{ 'active': activeTab === 'all' }" class="btn btn-outline btn-sm">All Products</button>
        <button @click="activeTab = 'spices-masalas'" :class="{ 'active': activeTab === 'spices-masalas' }" class="btn btn-outline btn-sm">Spices</button>
        <button @click="activeTab = 'rice-grains'" :class="{ 'active': activeTab === 'rice-grains' }" class="btn btn-outline btn-sm">Rice & Grains</button>
        <button @click="activeTab = 'frozen-foods'" :class="{ 'active': activeTab === 'frozen-foods' }" class="btn btn-outline btn-sm">Frozen</button>
        <button @click="activeTab = 'sweets-snacks'" :class="{ 'active': activeTab === 'sweets-snacks' }" class="btn btn-outline btn-sm">Snacks</button>
    </div>

    <div style="max-width: 1200px; margin: 0 auto;" class="product-grid">
        @foreach($featuredProducts as $product)
            @php
                $inCart = in_array($product->id, $userCartProductIds ?? []);
                $inWishlist = in_array($product->id, $userWishlistProductIds ?? []);
                $categorySlug = $product->category ? $product->category->slug : 'all';
            @endphp
            <div class="product-card" x-show="activeTab === 'all' || activeTab === '{{ $categorySlug }}'" x-transition>
                @if($product->sale_price && $product->sale_price < $product->price)
                    <span class="badge-discount">Sale</span>
                @else
                    <span class="badge-discount" style="background: var(--gold); color: var(--charcoal);">Bestseller</span>
                @endif

                <!-- Wishlist Form -->
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
                        <span style="color: var(--muted); font-size: 0.82rem;">({{ $product->reviews_count }})</span>
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
</section>

<!-- Trusted Brands Section -->
<section style="background: var(--maroon); padding: 50px 24px; overflow: hidden;" id="brands">
    <h2 style="text-align: center; font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--white); margin-bottom: 30px;">
        Trusted Brands We Stock
    </h2>
    <div class="marquee-track">
        @foreach(['MDH', 'EVEREST', 'TRS', 'NATCO', 'TILDA', 'DAAWAT', 'AMUL', 'HALDIRAM\'S', 'SHANA', 'ASHOKA', 'PARLE', 'BRITANNIA', 'AASHIRVAAD', 'EAST END', 'BIKAJI', 'GOWARDHAN'] as $bName)
            <div class="marquee-item" style="color: var(--cream); font-family: 'Cinzel', serif; font-size: 1.2rem; letter-spacing: 0.1em;">
                {{ $bName }}
            </div>
        @endforeach
    </div>
</section>

<!-- Why Choose Us -->
<section style="padding: 90px 24px; background: linear-gradient(135deg, #890F14 0%, #5C0A0D 100%); color: var(--white);" id="why">
    <div class="section-header">
        <div class="section-label" style="color: var(--gold);">Why Us</div>
        <h2 class="section-title" style="color: var(--white);">The Desi Foods Difference</h2>
        <p class="section-desc" style="color: rgba(255,255,255,0.75);">We don't just sell groceries — we bring a piece of home to your kitchen</p>
    </div>

    <div style="max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 28px;">
        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 2.5rem 1.8rem; text-align: center; backdrop-filter: blur(8px);">
            <div style="width: 60px; height: 60px; background: rgba(230,126,34,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; font-size: 1.8rem; color: var(--gold);">✓</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.35rem; color: var(--white); margin-bottom: 0.6rem;">Authenticity Guaranteed</h3>
            <p style="font-size: 0.95rem; color: rgba(255,255,255,0.7); line-height: 1.7;">Every product is sourced directly from trusted Indian manufacturers. Genuine desi quality with no compromises.</p>
        </div>

        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 2.5rem 1.8rem; text-align: center; backdrop-filter: blur(8px);">
            <div style="width: 60px; height: 60px; background: rgba(230,126,34,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; font-size: 1.8rem; color: var(--gold);">🌿</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.35rem; color: var(--white); margin-bottom: 0.6rem;">Farm-Fresh Produce</h3>
            <p style="font-size: 0.95rem; color: rgba(255,255,255,0.7); line-height: 1.7;">Our vegetables and fruits arrive fresh every morning. Hand-selected for quality, ripeness, and authentic flavor.</p>
        </div>

        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 2.5rem 1.8rem; text-align: center; backdrop-filter: blur(8px);">
            <div style="width: 60px; height: 60px; background: rgba(230,126,34,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; font-size: 1.8rem; color: var(--gold);">💰</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.35rem; color: var(--white); margin-bottom: 0.6rem;">Best Value Promise</h3>
            <p style="font-size: 0.95rem; color: rgba(255,255,255,0.7); line-height: 1.7;">We monitor prices across Hounslow to ensure you always get the best deal. Weekly specials and discounts for regulars.</p>
        </div>

        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 2.5rem 1.8rem; text-align: center; backdrop-filter: blur(8px);">
            <div style="width: 60px; height: 60px; background: rgba(230,126,34,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; font-size: 1.8rem; color: var(--gold);">🤝</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.35rem; color: var(--white); margin-bottom: 0.6rem;">Community First</h3>
            <p style="font-size: 0.95rem; color: rgba(255,255,255,0.7); line-height: 1.7;">Our staff are part of the Hounslow community. They know the products, recipes, and are always happy to help.</p>
        </div>

        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 2.5rem 1.8rem; text-align: center; backdrop-filter: blur(8px);">
            <div style="width: 60px; height: 60px; background: rgba(230,126,34,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; font-size: 1.8rem; color: var(--gold);">📦</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.35rem; color: var(--white); margin-bottom: 0.6rem;">4,000+ Products</h3>
            <p style="font-size: 0.95rem; color: rgba(255,255,255,0.7); line-height: 1.7;">From everyday staples to rare festival ingredients — if it's used in Indian cooking, you'll find it on our shelves.</p>
        </div>

        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 20px; padding: 2.5rem 1.8rem; text-align: center; backdrop-filter: blur(8px);">
            <div style="width: 60px; height: 60px; background: rgba(230,126,34,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; font-size: 1.8rem; color: var(--gold);">🚗</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.35rem; color: var(--white); margin-bottom: 0.6rem;">Easy Access & Parking</h3>
            <p style="font-size: 0.95rem; color: rgba(255,255,255,0.7); line-height: 1.7;">Located on Whitton Road with convenient parking right outside the store for quick in-and-out shopping.</p>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section style="padding: 80px 24px; background: var(--cream);" id="testimonials">
    <div class="section-header">
        <div class="section-label">Reviews</div>
        <h2 class="section-title">What Our Customers Say</h2>
        <p class="section-desc">Real reviews from real Hounslow families who shop with us every week</p>
    </div>

    <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        @foreach(\App\Models\Testimonial::where('status', true)->get() as $t)
            <div style="background: var(--white); border-radius: 20px; padding: 2.2rem; box-shadow: var(--shadow-sm); border: 1px solid rgba(230,126,34,0.08); display: flex; flex-direction: column;">
                <div style="font-size: 2.5rem; color: var(--saffron); line-height: 1; margin-bottom: 0.5rem; opacity: 0.4;">"</div>
                <p style="font-size: 1rem; color: var(--charcoal-light); line-height: 1.8; margin-bottom: 1.5rem; font-style: italic; flex: 1;">
                    {{ $t->content }}
                </p>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, var(--saffron) 0%, var(--gold) 100%); display: flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: 1.1rem;">
                        {{ substr($t->client_name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--maroon);">{{ $t->client_name }}</div>
                        <div style="color: var(--gold); font-size: 0.85rem;">★★★★★ Verified Customer</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Visit Us -->
<section style="background: var(--white); padding: 0;" id="visit">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); min-height: 550px;">
        <div style="background: linear-gradient(135deg, #A61A20 0%, #890F14 100%); display: flex; align-items: center; justify-content: center; padding: 40px; text-align: center; color: var(--white);">
            <div>
                <div style="font-size: 4rem; margin-bottom: 1rem; animation: float 4s ease-in-out infinite; display: inline-block;">📍</div>
                <h3 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--white); margin-bottom: 1rem;">Find Us in Hounslow</h3>
                <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 2rem;">3-4 Green Parade, Whitton Road<br>Hounslow, TW3 2EN</p>
                <a href="https://maps.google.com/?q=3-4+Green+Parade,+Whitton+Road,+Hounslow+TW3+2EN" target="_blank" class="btn btn-primary" style="padding: 12px 28px;">
                    Open in Google Maps →
                </a>
            </div>
        </div>

        <div style="padding: 60px 40px; display: flex; flex-direction: column; justify-content: center; background: var(--cream);">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 2.4rem; color: var(--maroon); margin-bottom: 0.8rem;">Visit Our Store</h2>
            <p style="color: var(--charcoal-light); margin-bottom: 2rem; font-size: 1.05rem;">Come experience the warmth of Desi Foods. We're conveniently located on Whitton Road with easy parking.</p>

            <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 1.5rem; padding: 1rem; background: var(--white); border-radius: 16px; border: 1px solid var(--cream-dark);">
                <div style="width: 44px; height: 44px; background: rgba(230,126,34,0.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--saffron-deep); flex-shrink: 0;">📍</div>
                <div>
                    <h4 style="font-size: 0.98rem; font-weight: 600; color: var(--maroon);">Address</h4>
                    <p style="font-size: 0.92rem; color: var(--charcoal-light);">3-4 Green Parade, Whitton Road, Hounslow, Greater London, TW3 2EN</p>
                </div>
            </div>

            <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 1.5rem; padding: 1rem; background: var(--white); border-radius: 16px; border: 1px solid var(--cream-dark);">
                <div style="width: 44px; height: 44px; background: rgba(230,126,34,0.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--saffron-deep); flex-shrink: 0;">📞</div>
                <div>
                    <h4 style="font-size: 0.98rem; font-weight: 600; color: var(--maroon);">Phone</h4>
                    <p style="font-size: 0.92rem; color: var(--charcoal-light);">020 8570 8899 <span style="color: var(--saffron-deep); font-size: 0.85rem;">(Stock enquiries welcome)</span></p>
                </div>
            </div>

            <div style="display: flex; align-items: flex-start; gap: 16px; padding: 1rem; background: var(--white); border-radius: 16px; border: 1px solid var(--cream-dark);">
                <div style="width: 44px; height: 44px; background: rgba(230,126,34,0.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--saffron-deep); flex-shrink: 0;">🕐</div>
                <div style="flex: 1;">
                    <h4 style="font-size: 0.98rem; font-weight: 600; color: var(--maroon); margin-bottom: 4px;">Opening Hours</h4>
                    <div style="font-size: 0.9rem; color: var(--charcoal-light); display: flex; flex-direction: column; gap: 2px;">
                        <div style="display: flex; justify-content: space-between;"><span style="font-weight: 500; color: var(--maroon);">Mon — Sat</span><span style="color: var(--saffron-deep); font-weight: 600;">8:00 AM — 9:00 PM</span></div>
                        <div style="display: flex; justify-content: space-between;"><span style="font-weight: 500; color: var(--maroon);">Sunday</span><span style="color: var(--saffron-deep); font-weight: 600;">9:00 AM — 7:00 PM</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section style="background: linear-gradient(135deg, var(--saffron) 0%, var(--saffron-deep) 100%); padding: 70px 24px; color: var(--white); text-align: center;" id="contact">
    <div style="max-width: 650px; margin: 0 auto;">
        <h2 style="font-family: 'Playfair Display', serif; font-size: 2.4rem; color: var(--white); margin-bottom: 0.8rem;">Stay in the Loop</h2>
        <p style="color: rgba(255,255,255,0.9); margin-bottom: 2rem; font-size: 1.05rem;">Subscribe for weekly specials, fresh vegetable arrivals, and exclusive offers for Desi Foods customers.</p>
        <form style="display: flex; gap: 12px; max-width: 500px; margin: 0 auto;" onsubmit="event.preventDefault(); alert('Thank you for subscribing! We will send you our weekly special offers.');">
            <input type="email" placeholder="Enter your email address" required style="flex: 1; padding: 14px 22px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50px; background: rgba(255,255,255,0.15); color: var(--white); font-size: 1rem; outline: none;">
            <button type="submit" class="btn" style="background: var(--white); color: var(--saffron-deep); font-weight: 700; padding: 14px 28px;">Subscribe</button>
        </form>
    </div>
</section>

@endsection
