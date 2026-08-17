<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "Desi Foods — Hounslow's Finest Indian Grocery & Produce")</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts & FontAwesome CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('styles')
</head>
<body x-data="{ mobileMenuOpen: false }">

    <!-- Top Announcement Bar -->
    <div class="top-bar">
        <span><i class="fa-solid fa-star" style="color: var(--gold-light); margin-right: 6px;"></i> Authentic Desi Groceries & Fresh Produce | Free Parking at Whitton Road | Use Code: <strong>DESIFOOD10</strong> for 10% OFF</span>
    </div>

    <!-- Header Navbar -->
    <header class="site-header">
        <div class="nav-container">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="brand-logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                <img src="{{ asset('images/logo.svg') }}" alt="Desi Foods Hounslow Logo" style="height: 52px; width: auto; object-fit: contain;">
            </a>

            <!-- Search Form with Live Auto-Suggest -->
            <form action="{{ route('products.index') }}" method="GET" class="search-form" x-data="liveSearch()">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="search-input" placeholder="Search 4,000+ groceries, spices, basmati rice, parathas..." 
                       x-model="query" @input.debounce.300ms="fetchResults()" autocomplete="off">

                <!-- Search Suggestions Dropdown -->
                <div x-show="results.length > 0" @click.away="results = []" 
                     style="position: absolute; top: 110%; left: 0; right: 0; background: var(--white); border: 1px solid var(--cream-dark); border-radius: 16px; box-shadow: var(--shadow-md); z-index: 1000; overflow: hidden;" x-cloak>
                    <template x-for="item in results" :key="item.slug">
                        <a :href="item.url" style="display: flex; align-items: center; gap: 14px; padding: 12px 18px; border-bottom: 1px solid var(--line-soft); transition: background 0.2s;" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='var(--white)'">
                            <img :src="item.image || 'https://via.placeholder.com/40'" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px;">
                            <div>
                                <div style="font-weight: 600; font-size: 0.92rem; color: var(--maroon);" x-text="item.name"></div>
                                <div style="font-size: 0.85rem; color: var(--saffron-deep); font-weight: 700;" x-text="item.price"></div>
                            </div>
                        </a>
                    </template>
                </div>
            </form>

            <!-- Nav Actions (Wishlist, Cart, Account) -->
            <div class="nav-actions">
                @auth
                    <a href="{{ route('account.wishlist') }}" class="icon-btn" title="Wishlist">
                        <i class="fa-regular fa-heart" style="font-size: 1.25rem;"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="icon-btn" title="Cart" style="position: relative;">
                        <i class="fa-solid fa-basket-shopping" style="font-size: 1.2rem;"></i>
                        @php
                            $cartCount = \App\Models\CartItem::whereHas('cart', function($q) {
                                $q->where('user_id', auth()->id());
                            })->sum('quantity');
                        @endphp
                        @if($cartCount > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                    
                    <div style="position: relative;" x-data="{ open: false }">
                        <button @click="open = !open" class="btn btn-outline btn-sm" style="padding: 8px 16px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-regular fa-user"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem;"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             style="position: absolute; right: 0; top: 120%; background: var(--white); border: 1px solid var(--cream-dark); border-radius: 12px; box-shadow: var(--shadow-md); width: 200px; padding: 8px 0; z-index: 1000;" x-cloak>
                            <a href="{{ route('account.dashboard') }}" style="display: block; padding: 10px 18px; color: var(--charcoal-light); font-size: 0.9rem; font-weight: 500;">
                                <i class="fa-solid fa-gauge-high me-2" style="color: var(--saffron);"></i> My Account
                            </a>
                            <a href="{{ route('account.orders') }}" style="display: block; padding: 10px 18px; color: var(--charcoal-light); font-size: 0.9rem; font-weight: 500;">
                                <i class="fa-solid fa-box me-2" style="color: var(--saffron);"></i> Order History
                            </a>
                            @if(auth()->user()->isStaff())
                                <a href="{{ route('admin.dashboard') }}" style="display: block; padding: 10px 18px; color: var(--maroon); font-size: 0.9rem; font-weight: 700; background: rgba(137,15,20,0.06);">
                                    <i class="fa-solid fa-user-shield me-2"></i> Admin Panel
                                </a>
                            @endif
                            <hr style="margin: 6px 0; border: none; border-top: 1px solid var(--cream-dark);">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 18px; color: var(--saffron-deep); font-size: 0.9rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Log In</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer style="background-color: var(--charcoal); color: var(--white); padding: 70px 0 30px; margin-top: 80px; border-top: 4px solid var(--saffron);">
        <div style="max-width: 1320px; margin: 0 auto; padding: 0 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 40px;">
            <div>
                <img src="{{ asset('images/logo-light.svg') }}" alt="Desi Foods Hounslow Logo" style="height: 56px; width: auto; object-fit: contain; margin-bottom: 16px;">
                <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; line-height: 1.7; margin-bottom: 20px;">
                    Hounslow's premier destination for authentic Indian groceries since 2010. Bringing the taste of home to your kitchen with 4,000+ products, fresh produce daily, and beloved brands.
                </p>
                <div style="display: flex; gap: 10px;">
                    <a href="#" style="width: 38px; height: 38px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); text-decoration: none;"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" style="width: 38px; height: 38px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); text-decoration: none;"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" style="width: 38px; height: 38px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); text-decoration: none;"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--gold); margin-bottom: 20px;">Quick Links</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.95rem; color: rgba(255,255,255,0.7);">
                    <li><a href="{{ route('home') }}#about" style="transition: color 0.3s; text-decoration: none; color: inherit;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='rgba(255,255,255,0.7)'"><i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px; color: var(--saffron);"></i> Our Story</a></li>
                    <li><a href="{{ route('products.index') }}" style="transition: color 0.3s; text-decoration: none; color: inherit;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='rgba(255,255,255,0.7)'"><i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px; color: var(--saffron);"></i> All Groceries</a></li>
                    <li><a href="{{ route('blog.index') }}" style="transition: color 0.3s; text-decoration: none; color: inherit;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='rgba(255,255,255,0.7)'"><i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px; color: var(--saffron);"></i> Desi Cooking Blog</a></li>
                    <li><a href="{{ route('cart.index') }}" style="transition: color 0.3s; text-decoration: none; color: inherit;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='rgba(255,255,255,0.7)'"><i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px; color: var(--saffron);"></i> Shopping Cart</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--gold); margin-bottom: 20px;">Categories</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.95rem; color: rgba(255,255,255,0.7);">
                    <li><a href="{{ route('products.index', ['category' => 'spices-masalas']) }}" style="text-decoration: none; color: inherit;"><i class="fa-solid fa-pepper-hot" style="margin-right: 6px; color: var(--saffron);"></i> Spices & Masalas</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'rice-grains']) }}" style="text-decoration: none; color: inherit;"><i class="fa-solid fa-bowl-rice" style="margin-right: 6px; color: var(--saffron);"></i> Rice & Basmati</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'frozen-foods']) }}" style="text-decoration: none; color: inherit;"><i class="fa-solid fa-snowflake" style="margin-right: 6px; color: var(--saffron);"></i> Frozen Parathas & Meals</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'sweets-snacks']) }}" style="text-decoration: none; color: inherit;"><i class="fa-solid fa-cookie-bite" style="margin-right: 6px; color: var(--saffron);"></i> Indian Sweets & Mithai</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--gold); margin-bottom: 20px;">Visit Our Store</h4>
                <div style="color: rgba(255,255,255,0.7); font-size: 0.95rem; line-height: 1.8;">
                    <div style="margin-bottom: 6px;"><i class="fa-solid fa-location-dot" style="color: var(--saffron); margin-right: 8px;"></i> 3-4 Green Parade, Whitton Road, Hounslow, TW3 2EN</div>
                    <div style="margin-bottom: 6px;"><i class="fa-solid fa-phone" style="color: var(--saffron); margin-right: 8px;"></i> 020 8570 8899</div>
                    <div><i class="fa-regular fa-clock" style="color: var(--saffron); margin-right: 8px;"></i> Mon - Sat: 8am - 9pm | Sun: 9am - 7pm</div>
                </div>
            </div>
        </div>

        <div style="max-width: 1320px; margin: 40px auto 0; padding: 24px 24px 0; border-top: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; color: rgba(255,255,255,0.5); font-size: 0.88rem;">
            <div>© {{ date('Y') }} Desi Foods Hounslow. All rights reserved. Authentic Indian Grocery Store.</div>
            <div style="display: flex; gap: 14px; font-size: 1.6rem; color: rgba(255,255,255,0.7);">
                <i class="fa-brands fa-cc-visa" title="Visa"></i>
                <i class="fa-brands fa-cc-mastercard" title="Mastercard"></i>
                <i class="fa-brands fa-cc-apple-pay" title="Apple Pay"></i>
                <i class="fa-brands fa-google-pay" title="Google Pay"></i>
            </div>
        </div>
    </footer>

    <script>
        function liveSearch() {
            return {
                query: '',
                results: [],
                fetchResults() {
                    if (this.query.length < 2) {
                        this.results = [];
                        return;
                    }
                    fetch(`{{ route('products.search') }}?q=${encodeURIComponent(this.query)}`)
                        .then(res => res.json())
                        .then(data => { this.results = data; });
                }
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
