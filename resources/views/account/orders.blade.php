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
                <li><a href="{{ route('account.recurring') }}" style="display: block; padding: 12px 16px; color: var(--saffron-deep); font-weight: 600;"><i class="fa-solid fa-repeat me-1"></i> Next-Month Orders</a></li>
                <li><a href="{{ route('account.profile') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Profile Details</a></li>
                <li><a href="{{ route('account.wishlist') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Wishlist</a></li>
            </ul>
        </div>

        <div>
            @if(session('success'))
                <div style="background: rgba(46,125,50,0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 4px solid #2E7D32; margin-bottom: 24px; font-weight: 600;">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="custom-table">
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
                                <td style="font-weight: 700; color: var(--maroon);">{{ $order->order_number }}</td>
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
