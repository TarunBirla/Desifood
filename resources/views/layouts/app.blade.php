<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "Desi Foods — Hounslow's Finest Indian Grocery & Produce")</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('styles')
</head>
<body x-data="{ mobileMenuOpen: false }">

    <!-- Top Announcement Bar -->
    <div class="top-bar">
        <span>✨ Authentic Desi Groceries & Fresh Produce | Free Parking at Whitton Road | Code: <strong>DESIFOOD10</strong> for 10% OFF</span>
    </div>

    <!-- Header Navbar -->
    <header class="site-header">
        <div class="nav-container">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="brand-logo">
                <div style="width: 36px; height: 36px; background: var(--saffron); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: var(--white); box-shadow: 0 4px 12px rgba(230,126,34,0.3);">
                    🛒
                </div>
                <span>Desi <span class="accent">Foods</span></span>
                <span class="badge-tag">Hounslow</span>
            </a>

            <!-- Search Form with Live Auto-Suggest -->
            <form action="{{ route('products.index') }}" method="GET" class="search-form" x-data="liveSearch()">
                <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
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
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </a>
                @endauth

                <a href="{{ route('cart.index') }}" class="icon-btn" title="Cart">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    @php
                        $cartCount = 0;
                        if (Auth::check()) {
                            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                            $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
                        }
                    @endphp
                    @if($cartCount > 0)
                        <span class="badge-count">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <div x-data="{ open: false }" style="position: relative;">
                        <button @click="open = !open" class="btn btn-outline btn-sm" style="border-radius: 50px; background: var(--white);">
                            <span>{{ Auth::user()->name }}</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" style="position: absolute; right: 0; top: 120%; background: var(--white); border: 1px solid var(--cream-dark); border-radius: 16px; min-width: 190px; box-shadow: var(--shadow-md); z-index: 1000; overflow: hidden;" x-cloak>
                            <a href="{{ route('account.dashboard') }}" style="display: block; padding: 12px 18px; border-bottom: 1px solid var(--line-soft); font-weight: 500;">My Dashboard</a>
                            <a href="{{ route('account.orders') }}" style="display: block; padding: 12px 18px; border-bottom: 1px solid var(--line-soft); font-weight: 500;">My Orders</a>
                            <a href="{{ route('account.profile') }}" style="display: block; padding: 12px 18px; border-bottom: 1px solid var(--line-soft); font-weight: 500;">My Profile</a>
                            @if(Auth::user()->isStaff())
                                <a href="{{ route('admin.dashboard') }}" style="display: block; padding: 12px 18px; color: var(--maroon); font-weight: 700; border-bottom: 1px solid var(--line-soft); background: rgba(137, 15, 20, 0.05);">⚡ Admin Panel</a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 12px 18px; color: var(--saffron-deep); cursor: pointer; font-weight: 600;">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                @endauth
            </div>
        </div>

        <!-- Categories Links Bar -->
        <nav class="category-nav">
            <div class="inner">
                <a href="{{ route('products.index') }}" class="category-link {{ !request()->has('category') ? 'active' : '' }}">🛒 All Groceries</a>
                @foreach(\App\Models\Category::whereNull('parent_id')->where('status', true)->take(7)->get() as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="category-link {{ request('category') == $cat->slug ? 'active' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    <!-- Flash Notifications -->
    <div style="max-width: 1320px; margin: 16px auto 0; padding: 0 24px;">
        @if(session('success'))
            <div style="background-color: rgba(46, 125, 50, 0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 5px solid #2E7D32; font-weight: 500; box-shadow: var(--shadow-sm);">
                ✓ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background-color: rgba(137, 15, 20, 0.1); color: var(--maroon); padding: 14px 20px; border-radius: 12px; border-left: 5px solid var(--maroon); font-weight: 500; box-shadow: var(--shadow-sm);">
                ⚠️ {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer style="background-color: var(--charcoal); color: var(--white); padding: 70px 0 30px; margin-top: 80px; border-top: 4px solid var(--saffron);">
        <div style="max-width: 1320px; margin: 0 auto; padding: 0 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 40px;">
            <div>
                <div style="font-family: 'Cinzel', serif; font-size: 1.8rem; font-weight: 700; color: var(--white); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <span style="color: var(--saffron);">🛒 Desi Foods</span>
                </div>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; line-height: 1.7; margin-bottom: 20px;">
                    Hounslow's premier destination for authentic Indian groceries since 2010. Bringing the taste of home to your kitchen with 4,000+ products, fresh produce daily, and beloved brands.
                </p>
                <div style="display: flex; gap: 10px;">
                    <a href="#" style="width: 38px; height: 38px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white);">f</a>
                    <a href="#" style="width: 38px; height: 38px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white);">📷</a>
                    <a href="#" style="width: 38px; height: 38px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white);">💬</a>
                </div>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--gold); margin-bottom: 20px;">Quick Links</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.95rem; color: rgba(255,255,255,0.7);">
                    <li><a href="{{ route('home') }}#about" style="transition: color 0.3s;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">Our Story</a></li>
                    <li><a href="{{ route('products.index') }}" style="transition: color 0.3s;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">All Groceries</a></li>
                    <li><a href="{{ route('blog.index') }}" style="transition: color 0.3s;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">Desi Cooking Blog</a></li>
                    <li><a href="{{ route('cart.index') }}" style="transition: color 0.3s;" onmouseover="this.style.color='var(--saffron)'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">Shopping Cart</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--gold); margin-bottom: 20px;">Categories</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.95rem; color: rgba(255,255,255,0.7);">
                    <li><a href="{{ route('products.index', ['category' => 'spices-masalas']) }}">Spices & Masalas</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'rice-grains']) }}">Rice & Basmati</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'frozen-foods']) }}">Frozen Parathas & Snacks</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'sweets-snacks']) }}">Indian Sweets & Mithai</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--gold); margin-bottom: 20px;">Visit Our Store</h4>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; line-height: 1.7;">
                    📍 3-4 Green Parade, Whitton Road<br>
                    Hounslow, Greater London, TW3 2EN<br>
                    📞 020 8570 8899<br>
                    🕐 Mon - Sat: 8am - 9pm | Sun: 9am - 7pm
                </p>
            </div>
        </div>

        <div style="max-width: 1320px; margin: 40px auto 0; padding: 24px 24px 0; border-top: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; color: rgba(255,255,255,0.5); font-size: 0.88rem;">
            <div>© {{ date('Y') }} Desi Foods Hounslow. All rights reserved. Authentic Indian Grocery Store.</div>
            <div style="display: flex; gap: 12px; font-size: 1.4rem;">
                <span>💳 VISA</span> <span>💳 Mastercard</span> <span>💳 Apple Pay</span>
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
