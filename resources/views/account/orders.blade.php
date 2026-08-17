@extends('layouts.app')

@section('title', 'My Grocery Orders | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">Order History</h1>

    <div class="catalog-layout">
        <!-- Account Sidebar -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 20px; height: fit-content; box-shadow: var(--shadow-sm);">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 6px;">
                <li><a href="{{ route('account.dashboard') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Dashboard</a></li>
                <li><a href="{{ route('account.orders') }}" style="display: block; padding: 12px 16px; font-weight: 700; color: var(--maroon); background: rgba(137, 15, 20, 0.08); border-radius: 12px;">My Grocery Orders</a></li>
                <li><a href="{{ route('account.profile') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Profile Details</a></li>
                <li><a href="{{ route('account.addresses') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Saved Delivery Addresses</a></li>
                <li><a href="{{ route('account.wishlist') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Wishlist</a></li>
            </ul>
        </div>

        <div>
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td style="font-weight: 700; color: var(--maroon);">{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td><span class="badge-status badge-info">{{ ucfirst($order->order_status) }}</span></td>
                                <td><span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ strtoupper($order->payment_status) }}</span></td>
                                <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($order->grand_total, 2) }}</td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <a href="{{ route('account.orders.details', $order->order_number) }}" class="btn btn-outline btn-sm" style="padding: 4px 10px;">
                                            Track & Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div style="margin-top: 24px;">{{ $orders->links() }}</div>
            @else
                <div style="background: var(--white); border: 1px solid var(--cream-dark); padding: 40px; text-align: center; border-radius: 16px;">
                    <p style="color: var(--charcoal-light); margin-bottom: 16px;">No orders found in your history.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Browse Grocery Catalog</a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
