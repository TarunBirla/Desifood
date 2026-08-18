<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard | Desi Foods Hounslow')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts & FontAwesome CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <div class="brand" style="padding: 20px 24px;">
                <img src="{{ asset('images/logo-light.svg') }}" alt="Desi Foods Logo" style="height: 46px; width: auto; object-fit: contain;">
            </div>
            <ul class="admin-menu">
                <li class="admin-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-chart-line" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}">
                        <i class="fa-solid fa-box-archive" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Grocery Orders</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                    <a href="{{ route('admin.products.index') }}">
                        <i class="fa-solid fa-utensils" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Food Catalog</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reviews.index') }}">
                        <i class="fa-solid fa-star" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Customer Reviews</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                    <a href="{{ route('admin.inventory.index') }}">
                        <i class="fa-solid fa-warehouse" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Stock Inventory</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}">
                    <a href="{{ route('admin.coupons.index') }}">
                        <i class="fa-solid fa-ticket" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Coupons & Offers</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                    <a href="{{ route('admin.customers.index') }}">
                        <i class="fa-solid fa-users" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Shopper Directory</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.subscribers*') ? 'active' : '' }}">
                    <a href="{{ route('admin.subscribers.index') }}">
                        <i class="fa-solid fa-envelope-open-text" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Subscribers</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                    <a href="{{ route('admin.profile') }}">
                        <i class="fa-solid fa-user-gear" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Admin Profile</span>
                    </a>
                </li>
                <li class="admin-menu-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <i class="fa-solid fa-gear" style="font-size: 1.1rem; width: 22px;"></i>
                        <span>Store Settings</span>
                    </a>
                </li>
            </ul>

            <div style="margin-top: auto; padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1); display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-outline btn-block btn-sm" style="color: var(--gold); border-color: var(--gold); display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-globe"></i> View Storefront
                </a>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
                    @csrf
                    <button type="submit" class="btn btn-sm" style="width: 100%; background: rgba(192,57,43,0.18); color: #FFF; border: 1px solid rgba(192,57,43,0.3); font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 12px; border-radius: 10px; cursor: pointer;">
                        <i class="fa-solid fa-right-from-bracket"></i> Sign Out / Log Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Admin Main Content Area -->
        <main class="admin-main">
            <!-- Header Bar -->
            <header class="admin-header">
                <div>
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--maroon);">@yield('page-title', 'Overview & Revenue Analytics')</h2>
                </div>
                
                <!-- Admin Profile Dropdown Navigation -->
                <div style="position: relative;" x-data="{ open: false }">
                    <button @click="open = !open" style="background: var(--white); border: 1.5px solid var(--cream-dark); padding: 6px 14px; border-radius: 30px; cursor: pointer; display: flex; align-items: center; gap: 10px; box-shadow: var(--shadow-sm); outline: none;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: var(--maroon); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div style="text-align: left; display: flex; flex-direction: column;">
                            <span style="font-weight: 700; font-size: 0.88rem; color: var(--maroon); line-height: 1.2;">{{ auth()->user()->name }}</span>
                            <span style="font-size: 0.72rem; color: var(--saffron-deep); font-weight: 600;">Administrator</span>
                        </div>
                        <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem; color: var(--muted); margin-left: 4px;"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" 
                         style="position: absolute; right: 0; top: 120%; background: var(--white); border: 1px solid var(--cream-dark); border-radius: 16px; box-shadow: var(--shadow-md); width: 220px; padding: 8px 0; z-index: 1000;" x-cloak>
                        <div style="padding: 12px 18px; border-bottom: 1px solid var(--cream-dark);">
                            <div style="font-weight: 700; font-size: 0.9rem; color: var(--maroon);">{{ auth()->user()->name }}</div>
                            <div style="font-size: 0.8rem; color: var(--muted); word-break: break-all;">{{ auth()->user()->email }}</div>
                        </div>
                        <a href="{{ route('admin.profile') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 18px; color: var(--charcoal-light); font-size: 0.9rem; font-weight: 500; text-decoration: none;" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                            <i class="fa-solid fa-user-gear" style="color: var(--saffron);"></i> My Admin Profile
                        </a>
                        <a href="{{ route('admin.settings.index') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 18px; color: var(--charcoal-light); font-size: 0.9rem; font-weight: 500; text-decoration: none;" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                            <i class="fa-solid fa-gear" style="color: var(--saffron);"></i> Store Settings
                        </a>
                        <hr style="margin: 6px 0; border: none; border-top: 1px solid var(--cream-dark);">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 18px; color: #c0392b; font-size: 0.9rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <i class="fa-solid fa-right-from-bracket"></i> Sign Out / Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                @if(session('success'))
                    <div style="background: rgba(46,125,50,0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 4px solid #2E7D32; margin-bottom: 24px; font-weight: 600;">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: rgba(192,57,43,0.1); color: #c0392b; padding: 14px 20px; border-radius: 12px; border-left: 4px solid #c0392b; margin-bottom: 24px; font-weight: 600;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @yield('scripts')
</body>
</html>
