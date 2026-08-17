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
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    @yield('styles')
</head>
<body>

   

    <!-- Sticky Main Header Navigation -->
    <header style="position: sticky; top: 0; z-index: 9999; background-color: rgba(255, 248, 240, 0.96); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid var(--cream-dark); box-shadow: var(--shadow-sm); transition: all 0.3s ease;">
        <div style="max-width: 1320px; margin: 0 auto; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 20px;">
            
            <!-- Logo Section -->
            <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                <img src="{{ asset('images/logo.svg') }}" alt="Desi Foods Logo" style="height: 48px; width: auto;">
                
            </a>

            <!-- Search Bar -->
            <div style="flex: 1; max-width: 480px; position: relative;" x-data="liveSearch()">
                <div style="position: relative;">
                    <input type="text" x-model="query" @input.debounce.300ms="fetchResults()" placeholder="Search Basmati rice, MDH masalas, frozen parathas, sweets..." 
                           style="width: 100%; padding: 12px 18px 12px 42px; border-radius: 30px; border: 1.5px solid var(--cream-dark); outline: none; background-color: var(--white); font-size: 0.9rem; color: var(--charcoal); transition: all 0.3s ease;"
                           onfocus="this.style.borderColor='var(--saffron)'; this.style.boxShadow='0 0 0 3px rgba(230, 126, 34, 0.15)';"
                           onblur="this.style.borderColor='var(--cream-dark)'; this.style.boxShadow='none';">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--saffron-deep); font-size: 0.95rem;"></i>
                </div>

                <!-- Live Search Dropdown -->
                <div x-show="results.length > 0" @click.away="results = []" 
                     style="position: absolute; top: 110%; left: 0; right: 0; background: var(--white); border: 1px solid var(--cream-dark); border-radius: 16px; box-shadow: var(--shadow-md); overflow: hidden; z-index: 10000;" x-cloak>
                    <template x-for="item in results" :key="item.slug">
                        <a :href="item.url" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--cream-dark); text-decoration: none; transition: background 0.2s;"
                           onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                            <img :src="item.image || 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=100'" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                            <div>
                                <div style="font-weight: 600; color: var(--maroon); font-size: 0.92rem;" x-text="item.name"></div>
                                <div style="font-size: 0.78rem; color: var(--saffron-deep); font-weight: 500;" x-text="item.category + ' • ' + item.price"></div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>

            <!-- Action Controls -->
            <div style="display: flex; align-items: center; gap: 16px;">
                <a href="{{ route('categories.index') }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; padding: 8px 16px;">
                    <i class="fa-solid fa-layer-group" style="color: var(--saffron);"></i> Categories
                </a>

                @auth
                    <!-- Wishlist Icon with Dynamic Badge Counter -->
                    <a href="{{ route('account.wishlist') }}" style="position: relative; color: var(--maroon); font-size: 1.3rem; text-decoration: none; width: 40px; height: 40px; border-radius: 50%; background: rgba(230, 126, 34, 0.08); display: flex; align-items: center; justify-content: center; transition: all 0.2s;" title="Wishlist"
                       onmouseover="this.style.background='var(--saffron)'; this.style.color='var(--white)';" onmouseout="this.style.background='rgba(230, 126, 34, 0.08)'; this.style.color='var(--maroon)';">
                        <i class="fa-regular fa-heart"></i>
                        @php
                            $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
                        @endphp
                        <span class="cart-badge" id="globalWishlistCountBadge" style="display: {{ $wishlistCount > 0 ? 'inline-flex' : 'none' }}; position: absolute; top: -4px; right: -4px; background: var(--maroon); color: #FFF; font-size: 0.72rem; font-weight: 700; width: 20px; height: 20px; border-radius: 50%; align-items: center; justify-content: center; border: 2px solid #FFF;">{{ $wishlistCount }}</span>
                    </a>

                    <!-- Cart Icon with Dynamic Counter -->
                    <a href="{{ route('cart.index') }}" style="position: relative; color: var(--maroon); font-size: 1.3rem; text-decoration: none; width: 40px; height: 40px; border-radius: 50%; background: rgba(230, 126, 34, 0.08); display: flex; align-items: center; justify-content: center; transition: all 0.2s;" title="Shopping Cart"
                       onmouseover="this.style.background='var(--saffron)'; this.style.color='var(--white)';" onmouseout="this.style.background='rgba(230, 126, 34, 0.08)'; this.style.color='var(--maroon)';">
                        <i class="fa-solid fa-basket-shopping"></i>
                        @php
                            $cartCount = \App\Models\Cart::where('user_id', auth()->id())->first()?->items()->count() ?? 0;
                        @endphp
                        <span class="cart-badge" id="globalCartCountBadge" style="display: {{ $cartCount > 0 ? 'inline-flex' : 'none' }}; position: absolute; top: -4px; right: -4px; background: var(--saffron-deep); color: #FFF; font-size: 0.72rem; font-weight: 700; width: 20px; height: 20px; border-radius: 50%; align-items: center; justify-content: center; border: 2px solid #FFF;">{{ $cartCount }}</span>
                    </a>
                    
                    <div style="position: relative;" x-data="{ open: false }">
                        <button @click="open = !open" class="btn btn-outline btn-sm" style="padding: 8px 16px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-regular fa-user" style="color: var(--saffron);"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem;"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             style="position: absolute; right: 0; top: 120%; background: var(--white); border: 1px solid var(--cream-dark); border-radius: 14px; box-shadow: var(--shadow-md); width: 210px; padding: 8px 0; z-index: 1000;" x-cloak>
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

    <!-- Enhanced Premium Footer -->
    <footer style="background-color: var(--charcoal); color: var(--white); margin-top: 80px; border-top: 4px solid var(--saffron);">
        
        <!-- Top Feature Highlights Banner -->
        <div style="background: rgba(255,255,255,0.04); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 32px 0;">
            <div style="max-width: 1320px; margin: 0 auto; padding: 0 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(230,126,34,0.15); color: var(--saffron); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;"><i class="fa-solid fa-truck-fast"></i></div>
                    <div>
                        <div style="font-weight: 700; color: var(--saffron); font-size: 0.95rem;">Free UK Delivery</div>
                        <div style="font-size: 0.82rem; color: rgba(255,255,255,0.6);">On orders above £35</div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(230,126,34,0.15); color: var(--saffron); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;"><i class="fa-solid fa-leaf"></i></div>
                    <div>
                        <div style="font-weight: 700; color: var(--saffron); font-size: 0.95rem;">100% Genuine Brands</div>
                        <div style="font-size: 0.82rem; color: rgba(255,255,255,0.6);">Directly imported Indian products</div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(230,126,34,0.15); color: var(--saffron); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;"><i class="fa-solid fa-square-parking"></i></div>
                    <div>
                        <div style="font-weight: 700; color: var(--saffron); font-size: 0.95rem;">Free Store Parking</div>
                        <div style="font-size: 0.82rem; color: rgba(255,255,255,0.6);">Whitton Road Store, TW3 2EN</div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(230,126,34,0.15); color: var(--saffron); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;"><i class="fa-solid fa-headset"></i></div>
                    <div>
                        <div style="font-weight: 700; color: var(--saffron); font-size: 0.95rem;">Store Assistance</div>
                        <div style="font-size: 0.82rem; color: rgba(255,255,255,0.6);">+44 (0)20 8570 1234</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="max-width: 1320px; margin: 0 auto; padding: 60px 24px 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                    <img src="{{ asset('images/logo.svg') }}" alt="Desi Foods Logo" style="height: 44px;">
                </div>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; line-height: 1.7; margin-bottom: 20px;">
                    Hounslow's leading destination for authentic Indian groceries, spices, Basmati rice, fresh vegetables, frozen parathas, and traditional sweets.
                </p>
                <div style="color: rgba(255,255,255,0.8); font-size: 0.88rem; display: flex; flex-direction: column; gap: 10px;">
                    <div><i class="fa-solid fa-location-dot me-2" style="color: var(--saffron);"></i> 3-4 Green Parade, Whitton Rd, Hounslow TW3 2EN</div>
                    <div><i class="fa-solid fa-phone me-2" style="color: var(--saffron);"></i> +44 (0)20 8570 1234</div>
                    <div><i class="fa-solid fa-envelope me-2" style="color: var(--saffron);"></i> support@desifoods.com</div>
                    <div style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">VAT Reg #: GB 987 6543 21</div>
                </div>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; color: var(--saffron); font-size: 1.15rem; margin-bottom: 20px; border-bottom: 2px solid rgba(230,126,34,0.3); padding-bottom: 8px;">Food Categories</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: rgba(255,255,255,0.75);">
                    <li><a href="{{ route('products.index', ['category' => 'spices-masalas']) }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Spices & Masalas</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'rice-grains']) }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Basmati Rice & Grains</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'lentils-pulses']) }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Lentils & Pulses</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'frozen-foods']) }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Frozen Parathas & Foods</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'sweets-snacks']) }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Sweets & Snacks</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; color: var(--saffron); font-size: 1.15rem; margin-bottom: 20px; border-bottom: 2px solid rgba(230,126,34,0.3); padding-bottom: 8px;">Quick Links</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: rgba(255,255,255,0.75);">
                    <li><a href="{{ route('categories.index') }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> All Food Categories</a></li>
                    <li><a href="{{ route('products.index') }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Full Grocery Catalog</a></li>
                    <li><a href="{{ route('blog.index') }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Indian Recipes & Blog</a></li>
                    <li><a href="{{ route('account.orders') }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Track Your Order</a></li>
                    <li><a href="{{ route('account.wishlist') }}" style="color: inherit;"><i class="fa-solid fa-angle-right me-1" style="font-size: 0.75rem; color: var(--saffron);"></i> Saved Wishlist</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-family: 'Playfair Display', serif; color: var(--saffron); font-size: 1.15rem; margin-bottom: 20px; border-bottom: 2px solid rgba(230,126,34,0.3); padding-bottom: 8px;">Store Hours & Express Delivery</h4>
                <div style="font-size: 0.88rem; color: rgba(255,255,255,0.75); display: flex; flex-direction: column; gap: 8px;">
                    <div><strong>Monday - Saturday:</strong> 8:00 AM - 9:00 PM</div>
                    <div><strong>Sunday:</strong> 9:00 AM - 8:00 PM</div>
                    <div style="margin-top: 12px; background: rgba(230,126,34,0.15); border: 1px solid var(--saffron); padding: 12px 16px; border-radius: 12px; color: var(--saffron); font-weight: 600;">
                        <i class="fa-solid fa-truck-fast me-1"></i> Same Day Express Hounslow Delivery Available!
                    </div>
                </div>
            </div>
        </div>

        <div style="max-width: 1320px; margin: 20px auto 0; padding: 24px 24px 30px; border-top: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; color: rgba(255,255,255,0.5); font-size: 0.88rem;">
            <div>© {{ date('Y') }} Desi Foods Hounslow. All rights reserved. Authentic Indian Grocery Store in London TW3.</div>
            <div style="display: flex; gap: 16px; font-size: 1.8rem; color: rgba(255,255,255,0.75);">
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
            let targetBtn = null;
            if (event) {
                event.preventDefault();
                targetBtn = event.currentTarget || event.target;
                if (targetBtn && targetBtn.tagName !== 'BUTTON' && targetBtn.closest('button')) {
                    targetBtn = targetBtn.closest('button');
                }
            }
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
                    
                    if (targetBtn) {
                        targetBtn.outerHTML = `<a href="{{ route('cart.index') }}" class="btn btn-outline btn-sm" style="color: var(--maroon); border-color: var(--saffron); background: rgba(230,126,34,0.1); width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 4px; padding: 8px 10px;"><i class="fa-solid fa-check"></i> Added</a>`;
                    }
                } else {
                    showToast(data.message || 'Could not add to cart.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Please login to add items to cart.', 'error');
            });
        window.toggleWishlistAjax = function(productId, event = null) {
            let btn = null;
            if (event) {
                event.preventDefault();
                btn = event.currentTarget || event.target;
                if (btn && btn.tagName !== 'BUTTON' && btn.closest('button')) {
                    btn = btn.closest('button');
                }
            }
            fetch("{{ route('account.wishlist.toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                if (data.success) {
                    const badge = document.getElementById('globalWishlistCountBadge');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
                    }
                    if (btn) {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            if (data.added) {
                                icon.className = 'fa-solid fa-heart';
                                icon.style.color = '#e74c3c';
                            } else {
                                icon.className = 'fa-regular fa-heart';
                                icon.style.color = '';
                            }
                        }
                    }
                    showToast(data.message || (data.added ? 'Added to wishlist!' : 'Removed from wishlist.'));
                } else {
                    showToast(data.message || 'Could not update wishlist.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Please login to update wishlist.', 'error');
            });
        };
    </script>
    @yield('scripts')
</body>
</html>
