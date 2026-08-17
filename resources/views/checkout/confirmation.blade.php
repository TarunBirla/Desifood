@extends('layouts.app')

@section('title', 'Order Confirmation #' . $order->order_number . ' | Desi Foods Hounslow')

@section('content')

<div style="max-width: 800px; margin: 60px auto; padding: 0 24px; text-align: center;">
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 48px; box-shadow: var(--shadow-md);">
        <div style="width: 76px; height: 76px; background: rgba(46, 125, 50, 0.12); color: #2E7D32; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 20px;">
            <i class="fa-solid fa-check"></i>
        </div>

        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 10px;">Grocery Order Placed Successfully!</h1>
        <p style="color: var(--charcoal-light); font-size: 1.05rem; margin-bottom: 24px;">
            Thank you for shopping at Desi Foods Hounslow. We have received your order and sent a confirmation to <strong>{{ $order->user->email }}</strong>.
        </p>

        <div style="background: var(--cream); border: 1px dashed var(--cream-dark); border-radius: 16px; padding: 20px; margin-bottom: 32px; display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; text-align: left;">
            <div>
                <span style="font-size: 0.78rem; color: var(--muted); text-transform: uppercase;">Order Number</span>
                <div style="font-weight: 700; font-size: 1.1rem; color: var(--maroon);">{{ $order->order_number }}</div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: var(--muted); text-transform: uppercase;">Order Date</span>
                <div style="font-weight: 600; font-size: 0.95rem; color: var(--charcoal);">{{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: var(--muted); text-transform: uppercase;">Payment Status</span>
                <div>
                    <span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}">
                        {{ strtoupper($order->payment_status) }}
                    </span>
                </div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: var(--muted); text-transform: uppercase;">Total Amount</span>
                <div style="font-weight: 700; font-size: 1.1rem; color: var(--maroon);">£{{ number_format($order->grand_total, 2) }}</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
            <a href="{{ route('account.orders.details', $order->order_number) }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-location-dot"></i> Track Order Status
            </a>
            <a href="{{ route('account.invoice.download', $order->order_number) }}" target="_blank" class="btn btn-brass" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-pdf"></i> Download Official Invoice
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

@endsection
