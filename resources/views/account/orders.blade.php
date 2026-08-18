@extends('layouts.app')

@section('title', 'My Grocery Orders | Desi Foods Hounslow')

@section('content')

<div class="site-container" style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <h1 class="site-page-title" style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">Order History</h1>

    <div class="catalog-layout">
        <!-- Account Sidebar Navigation -->
        <div class="account-nav-wrapper">
            <a href="{{ route('account.dashboard') }}" class="account-nav-pill">
                <i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('account.orders') }}" class="account-nav-pill active">
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

        <div>
            @if(session('success'))
                <div style="background: rgba(46,125,50,0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 4px solid #2E7D32; margin-bottom: 24px; font-weight: 600;">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if($orders->count() > 0)
                <!-- Desktop View Table (>= 768px) -->
                <div class="desktop-orders-table">
                    <div class="table-responsive">
                        <table class="custom-table" style="width: 100%; min-width: 600px;">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td style="font-weight: 700; color: var(--maroon); white-space: nowrap; font-family: monospace;">{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($order->order_type === 'repeat')
                                                <span class="badge-status badge-info" style="background: rgba(230,126,34,0.15); color: var(--saffron-deep); border: 1px solid rgba(230,126,34,0.3); font-weight: 700;"><i class="fa-solid fa-rotate-right me-1"></i> Repeat</span>
                                            @elseif($order->order_type === 'recurring')
                                                <span class="badge-status badge-info" style="background: rgba(46,125,50,0.15); color: #2E7D32; border: 1px solid rgba(46,125,50,0.3); font-weight: 700;"><i class="fa-solid fa-calendar-check me-1"></i> Recurring</span>
                                            @else
                                                <span style="color: var(--muted); font-size: 0.85rem;">Standard</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($order->order_status)
                                                @case('return_requested')
                                                    <span class="badge-status badge-warning" style="background: #F39C12; color: #fff;">Return Requested</span>
                                                    @break
                                                @case('returned')
                                                    <span class="badge-status badge-danger">Returned</span>
                                                    @break
                                                @case('delivered')
                                                    <span class="badge-status badge-success">Delivered</span>
                                                    @break
                                                @default
                                                    <span class="badge-status badge-info">{{ ucfirst($order->order_status) }}</span>
                                            @endswitch
                                        </td>
                                        <td><span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ strtoupper($order->payment_status) }}</span></td>
                                        <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($order->grand_total, 2) }}</td>
                                        <td style="text-align: right;">
                                            <div style="display: inline-flex; gap: 8px; align-items: center; justify-content: flex-end;">
                                                <a href="{{ route('account.orders.details', $order->order_number) }}" class="btn btn-outline btn-sm" style="padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px; border-radius: 20px;">
                                                    <i class="fa-solid fa-eye"></i> Details
                                                </a>

                                                <form action="{{ route('account.orders.repeat', $order->order_number) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary" style="padding: 6px 14px; display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; font-weight: 600;" title="Re-add these products to cart for repeat checkout">
                                                        <i class="fa-solid fa-rotate-right"></i> Repeat Order
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile View Cards (< 768px) -->
                <div class="mobile-orders-cards" style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach($orders as $order)
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

                            <!-- Middle Row: Date + Type + Payment + Total -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; font-size: 0.88rem;">
                                <div>
                                    <span style="color: var(--muted); font-size: 0.78rem; display: block;">Date:</span>
                                    <strong style="color: var(--charcoal);">{{ $order->created_at->format('d M Y') }}</strong>
                                </div>
                                <div>
                                    <span style="color: var(--muted); font-size: 0.78rem; display: block;">Order Type:</span>
                                    @if($order->order_type === 'repeat')
                                        <span class="badge-status badge-info" style="font-size: 0.75rem;"><i class="fa-solid fa-rotate-right me-1"></i> Repeat</span>
                                    @elseif($order->order_type === 'recurring')
                                        <span class="badge-status badge-info" style="font-size: 0.75rem; background: rgba(46,125,50,0.15); color: #2E7D32;"><i class="fa-solid fa-calendar-check me-1"></i> Recurring</span>
                                    @else
                                        <span style="color: var(--muted); font-size: 0.85rem;">Standard</span>
                                    @endif
                                </div>
                                <div>
                                    <span style="color: var(--muted); font-size: 0.78rem; display: block;">Payment Status:</span>
                                    <span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.75rem;">{{ strtoupper($order->payment_status) }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--muted); font-size: 0.78rem; display: block;">Total Amount:</span>
                                    <strong style="color: var(--maroon); font-size: 1.1rem;">£{{ number_format($order->grand_total, 2) }}</strong>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div style="display: flex; gap: 8px; border-top: 1px solid var(--cream-dark); padding-top: 12px;">
                                <a href="{{ route('account.orders.details', $order->order_number) }}" class="btn btn-outline btn-sm" style="flex: 1; text-align: center; justify-content: center; border-radius: 20px; padding: 8px;">
                                    <i class="fa-solid fa-eye me-1"></i> Details
                                </a>
                                <form action="{{ route('account.orders.repeat', $order->order_number) }}" method="POST" style="flex: 1; margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center; border-radius: 20px; padding: 8px; font-weight: 600;">
                                        <i class="fa-solid fa-rotate-right me-1"></i> Repeat
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
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
