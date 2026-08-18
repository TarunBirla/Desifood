@extends('layouts.app')

@section('title', 'My Customer Account | Desi Foods Hounslow')

@section('content')

<div class="site-container" style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <h1 class="site-page-title" style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">My Customer Account</h1>

    <div class="catalog-layout">
        <!-- Account Sidebar Navigation -->
        <div class="account-nav-wrapper">
            <a href="{{ route('account.dashboard') }}" class="account-nav-pill active">
                <i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('account.orders') }}" class="account-nav-pill">
                <i class="fa-solid fa-box"></i> <span>My Grocery Orders</span>
            </a>
            <a href="{{ route('account.recurring') }}" class="account-nav-pill">
                <i class="fa-solid fa-repeat"></i> <span>Next-Month Orders</span>
            </a>
            <a href="{{ route('account.profile') }}" class="account-nav-pill">
                <i class="fa-solid fa-user-gear"></i> <span>Profile Details</span>
            </a>
            <a href="{{ route('account.wishlist') }}" class="account-nav-pill">
                <i class="fa-solid fa-heart"></i> <span>Wishlist</span>
            </a>
        </div>

        <!-- Dashboard Content -->
        <div>
            <!-- Stats -->
            <div class="account-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px;">
                <div class="stat-card">
                    <span class="label">Total Orders Placed</span>
                    <div class="value">{{ $totalOrdersCount }}</div>
                </div>
                <div class="stat-card">
                    <span class="label">Total Spent</span>
                    <div class="value">£{{ number_format($totalSpent, 2) }}</div>
                </div>
            </div>

            <!-- Recent Orders -->
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--maroon); margin-bottom: 20px;">Recent Grocery Orders</h3>
            @if($recentOrders->count() > 0)
                <!-- Desktop View Table (>= 768px) -->
                <div class="desktop-orders-table">
                    <div class="table-responsive">
                        <table class="custom-table" style="width: 100%; min-width: 600px;">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td style="font-weight: 700; color: var(--maroon); white-space: nowrap; font-family: monospace;">{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td><span class="badge-status badge-info">{{ ucfirst($order->order_status) }}</span></td>
                                        <td><span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ strtoupper($order->payment_status) }}</span></td>
                                        <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($order->grand_total, 2) }}</td>
                                        <td>
                                            <a href="{{ route('account.orders.details', $order->order_number) }}" class="btn btn-outline btn-sm">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile View Cards (< 768px) -->
                <div class="mobile-orders-cards" style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach($recentOrders as $order)
                        <div style="background: var(--white); border: 1.5px solid var(--cream-dark); border-radius: 18px; padding: 16px; box-shadow: var(--shadow-sm);">
                            <!-- Top Row: Order # + Status Badge -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--cream-dark);">
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--muted); display: block;">ORDER NUMBER</span>
                                    <strong style="color: var(--maroon); font-family: monospace; font-size: 0.95rem;">{{ $order->order_number }}</strong>
                                </div>
                                <div>
                                    <span class="badge-status badge-success" style="font-size: 0.78rem; padding: 4px 10px;">{{ ucfirst($order->order_status) }}</span>
                                </div>
                            </div>

                            <!-- Middle Row: Date + Payment + Total -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; font-size: 0.88rem;">
                                <div>
                                    <span style="color: var(--muted); font-size: 0.78rem; display: block;">Date:</span>
                                    <strong style="color: var(--charcoal);">{{ $order->created_at->format('d M Y') }}</strong>
                                </div>
                                <div>
                                    <span style="color: var(--muted); font-size: 0.78rem; display: block;">Payment Status:</span>
                                    <span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.75rem;">{{ strtoupper($order->payment_status) }}</span>
                                </div>
                                <div style="grid-column: span 2;">
                                    <span style="color: var(--muted); font-size: 0.78rem; display: block;">Total Amount:</span>
                                    <strong style="color: var(--maroon); font-size: 1.1rem;">£{{ number_format($order->grand_total, 2) }}</strong>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div style="border-top: 1px solid var(--cream-dark); padding-top: 12px;">
                                <a href="{{ route('account.orders.details', $order->order_number) }}" class="btn btn-outline btn-sm" style="width: 100%; text-align: center; justify-content: center; border-radius: 20px; padding: 8px;">
                                    <i class="fa-solid fa-eye me-1"></i> View Order Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background: var(--white); border: 1px solid var(--cream-dark); padding: 40px; text-align: center; border-radius: 16px;">
                    <p style="color: var(--charcoal-light); margin-bottom: 16px;">You haven't placed any grocery orders yet.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Start Shopping</a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
