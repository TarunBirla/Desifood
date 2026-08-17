<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Desi Foods Hounslow - Finest Indian Grocery Store')</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Authentic Indian Grocery Store in Hounslow. Buy fresh vegetables, spices, Basmati rice, pulses, ghee, frozen parathas, and sweets online with fast doorstep delivery across London TW3 & UK.">
    <meta name="keywords" content="Desi Foods Hounslow, Indian Grocery London, Basmati Rice, MDH Spices, Haldirams Sweets, Fresh Okra, Frozen Samosas">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts: Playfair Display & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Store CSS Token System -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    @yield('styles')
</head>
<body>

    <!-- Header Navigation -->
    <header class="navbar">
        <div style="max-width: 1320px; margin: 0 auto; padding: 0 24px; display: flex; justify-content: space-between; align-items: center; width: 100%;">
            
            <!-- Logo Section -->
            <a href="{{ route('home') }}" class="logo-link">
                <img src="{{ asset('images/logo.svg') }}" alt="Desi Foods Logo" class="brand-logo">
                <div>
                    <span class="brand-name">Desi Foods</span>
                    <span class="brand-tagline">Hounslow's Finest Indian Grocery</span>
                </div>
            </a>

            <!-- Search Bar -->
            <div style="flex: 1; max-width: 460px; margin: 0 32px; position: relative;" x-data="liveSearch()">
                <div style="position: relative;">
                    <input type="text" x-model="query" @input.debounce.300ms="fetchResults()" placeholder="Search spices, Basmati rice, snacks, flour..." 
                           style="width: 100%; padding: 12px 18px 12px 42px; border-radius: 30px; border: 1px solid var(--cream-dark); outline: none; background-color: var(--cream); font-size: 0.9rem;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 0.95rem;"></i>
                </div>

                <!-- Live Search Dropdown -->
                <div x-show="results.length > 0" @click.away="results = []" class="search-dropdown" x-cloak>
                    <template x-for="item in results" :key="item.slug">
                        <a :href="item.url" class="search-item">
                            <img :src="item.image || 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=100'" style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px;">
                            <div>
                                <div style="font-weight: 600; color: var(--maroon);" x-text="item.name"></div>
                                <div style="font-size: 0.78rem; color: var(--saffron-deep);" x-text="item.category + ' • ' + item.price"></div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>

            <!-- Action Controls -->
            <div style="display: flex; align-items: center; gap: 18px;">
                <a href="{{ route('categories.index') }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-layer-group"></i> All Categories
                </a>

                @auth
                    <!-- Wishlist Icon -->
                    <a href="{{ route('account.wishlist') }}" style="position: relative; color: var(--maroon); font-size: 1.3rem; text-decoration: none;" title="Wishlist">
                        <i class="fa-regular fa-heart"></i>
                    </a>

                    <!-- Cart Icon with Dynamic Counter -->
                    <a href="{{ route('cart.index') }}" style="position: relative; color: var(--maroon); font-size: 1.3rem; text-decoration: none;" title="Shopping Cart">
                        <i class="fa-solid fa-basket-shopping"></i>
                        @php
                            $cartCount = \App\Models\Cart::where('user_id', auth()->id())->first()?->items()->sum('quantity') ?? 0;
                        @endphp
                        <span class="cart-badge" id="globalCartCountBadge" style="display: {{ $cartCount > 0 ? 'inline-flex' : 'none' }};">{{ $cartCount }}</span>
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

    <!-- Toast Notification Overlay -->
    <div id="globalToast" style="position: fixed; bottom: 30px; right: 30px; z-index: 999999; background: var(--maroon); color: var(--white); padding: 14px 22px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.35); font-weight: 600; display: flex; align-items: center; gap: 10px; opacity: 0; transform: translateY(20px); transition: all 0.3s ease; pointer-events: none;">
        <i id="toastIcon" class="fa-solid fa-circle-check" style="color: var(--saffron); font-size: 1.2rem;"></i>
        <span id="toastMessage">Item added to cart</span>
    </div>

    <!-- Footer -->
    <footer style="background-color: var(--charcoal); color: var(--white); padding: 70px 0 30px; margin-top: 80px; border-top: 4px solid var(--saffron);">
        <div style="max-width: 1320px; margin: 0 auto; padding: 0 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 40px;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                    <img src="{{ asset('images/logo.svg') }}" alt="Desi Foods Logo" style="height: 42px;">
                    <span style="font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; color: var(--saffron);">Desi Foods</span>
                </div>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; line-height: 1.7; margin-bottom: 20px;">
                    Hounslow's leading destination for authentic Indian groceries, spices, Basmati rice, fresh vegetables, and traditional sweets.
                </p>
                <div style="color: rgba(255,255,255,0.8); font-size: 0.88rem; display: flex; flex-direction: column; gap: 8px;">
                    <div><i class="fa-solid fa-location-dot me-2" style="color: var(--saffron);"></i> 3-4 Green Parade, Whitton Rd, Hounslow TW3 2EN</div>
                    <div><i class="fa-solid fa-phone me-2" style="color: var(--saffron);"></i> +44 (0)20 8570 1234</div>
                </div>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; color: var(--saffron); font-size: 1.15rem; margin-bottom: 20px;">Food Categories</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: rgba(255,255,255,0.7);">
                    <li><a href="{{ route('products.index', ['category' => 'spices-masalas']) }}" style="color: inherit;">Spices & Masalas</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'rice-grains']) }}" style="color: inherit;">Rice & Basmati</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'lentils-pulses']) }}" style="color: inherit;">Lentils & Pulses</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'frozen-foods']) }}" style="color: inherit;">Frozen Parathas & Foods</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'sweets-snacks']) }}" style="color: inherit;">Sweets & Snacks</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; color: var(--saffron); font-size: 1.15rem; margin-bottom: 20px;">Quick Links</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: rgba(255,255,255,0.7);">
                    <li><a href="{{ route('categories.index') }}" style="color: inherit;">All Food Categories</a></li>
                    <li><a href="{{ route('products.index') }}" style="color: inherit;">Full Food Catalog</a></li>
                    <li><a href="{{ route('blog.index') }}" style="color: inherit;">Indian Recipes & Blog</a></li>
                    <li><a href="{{ route('account.orders') }}" style="color: inherit;">Track Your Order</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; color: var(--saffron); font-size: 1.15rem; margin-bottom: 20px;">Store Opening Hours</h4>
                <div style="font-size: 0.88rem; color: rgba(255,255,255,0.7); display: flex; flex-direction: column; gap: 8px;">
                    <div><strong>Monday - Saturday:</strong> 8:00 AM - 9:00 PM</div>
                    <div><strong>Sunday:</strong> 9:00 AM - 8:00 PM</div>
                    <div style="margin-top: 10px; background: rgba(230,126,34,0.15); border: 1px solid var(--saffron); padding: 10px 14px; border-radius: 10px; color: var(--saffron); font-weight: 600;">
                        <i class="fa-solid fa-truck-fast me-1"></i> Same Day Express Hounslow Delivery
                    </div>
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

        window.showToast = function(msg, type = 'success') {
            const toast = document.getElementById('globalToast');
            const msgEl = document.getElementById('toastMessage');
            const iconEl = document.getElementById('toastIcon');
            if (!toast || !msgEl) return;
            
            msgEl.textContent = msg;
            if (type === 'error') {
                toast.style.background = '#C0392B';
                iconEl.className = 'fa-solid fa-circle-exclamation';
                iconEl.style.color = '#FFF';
            } else {
                toast.style.background = 'var(--maroon)';
                iconEl.className = 'fa-solid fa-circle-check';
                iconEl.style.color = 'var(--saffron)';
            }

            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
            }, 3000);
        };

        window.addToCartAjax = function(productId, quantity = 1, event = null) {
            if (event) event.preventDefault();
            fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                if (data.success) {
                    const badge = document.getElementById('globalCartCountBadge');
                    if (badge) {
                        badge.textContent = data.cart_count;
                        badge.style.display = 'inline-flex';
                    }
                    showToast(data.message || 'Added to cart!');
                } else {
                    showToast(data.message || 'Could not add to cart.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Please login to add items to cart.', 'error');
            });
        };
    </script>
    @yield('scripts')
</body>
</html>
