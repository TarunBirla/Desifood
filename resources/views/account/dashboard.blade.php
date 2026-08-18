@extends('layouts.app')

@section('title', 'My Customer Account | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">My Customer Account</h1>

    <div class="catalog-layout">
        <!-- Account Sidebar -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 20px; height: fit-content; box-shadow: var(--shadow-sm);">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 6px;" class="account-sidebar-menu">
                <li><a href="{{ route('account.dashboard') }}" style="display: block; padding: 12px 16px; font-weight: 700; color: var(--maroon); background: rgba(137, 15, 20, 0.08); border-radius: 12px;">Dashboard</a></li>
                <li><a href="{{ route('account.orders') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">My Grocery Orders</a></li>
                <li><a href="{{ route('account.recurring') }}" style="display: block; padding: 12px 16px; color: var(--saffron-deep); font-weight: 600;"><i class="fa-solid fa-repeat me-1"></i> Next-Month Orders</a></li>
                <li><a href="{{ route('account.profile') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Profile Details</a></li>
                <li><a href="{{ route('account.wishlist') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Wishlist</a></li>
            </ul>
        </div>

        <!-- Dashboard Content -->
        <div>
            <!-- Stats -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px;">
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
                <div class="table-responsive">
                    <table class="custom-table">
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
                                <td style="font-weight: 700; color: var(--maroon);">{{ $order->order_number }}</td>
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
