@extends('layouts.app')

@section('title', 'Order Details #' . $order->order_number . ' | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1100px; margin: 40px auto; padding: 0 24px;" x-data="{ openReviewModal: false, activeProductId: null, activeProductName: '' }">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--maroon);">Grocery Order Details #{{ $order->order_number }}</h1>
            <p style="color: var(--charcoal-light); font-size: 0.9rem;">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <a href="{{ route('account.invoice.download', $order->order_number) }}" target="_blank" class="btn btn-brass btn-sm">
            📄 Print Invoice
        </a>
    </div>

    @if(session('success'))
        <div style="background-color: rgba(46, 125, 50, 0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 5px solid #2E7D32; margin-bottom: 24px; font-weight: 600;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Stepper Status Timeline -->
    @php
        $statuses = ['pending' => 'Order Placed', 'confirmed' => 'Confirmed', 'packed' => 'Packed', 'shipped' => 'Out for Delivery', 'delivered' => 'Delivered'];
        $currentStatusKey = array_search($order->order_status, array_keys($statuses));
        if ($currentStatusKey === false) $currentStatusKey = 1;
    @endphp
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 32px; margin-bottom: 32px; box-shadow: var(--shadow-sm);">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 24px;">Delivery Tracking Timeline</h3>
        <div style="display: flex; justify-content: space-between; position: relative;">
            @php $idx = 0; @endphp
            @foreach($statuses as $stKey => $stLabel)
                <div style="text-align: center; position: relative; z-index: 2; flex: 1;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 0 auto 8px; {{ $idx <= $currentStatusKey ? 'background: var(--maroon); color: var(--white);' : 'background: var(--cream-dark); color: var(--muted);' }}">
                        {{ $idx + 1 }}
                    </div>
                    <div style="font-size: 0.85rem; font-weight: 600; {{ $idx <= $currentStatusKey ? 'color: var(--maroon);' : 'color: var(--muted);' }}">{{ $stLabel }}</div>
                </div>
                @php $idx++; @endphp
            @endforeach
        </div>

        @if($order->tracking_number)
            <div style="background: var(--cream); border: 1px solid var(--cream-dark); border-radius: 12px; padding: 14px 20px; margin-top: 24px; display: flex; justify-content: space-between; font-size: 0.9rem;">
                <span>Delivery Option: <strong>{{ $order->delivery_partner ?: 'Local Hounslow Doorstep Express' }}</strong></span>
                <span>Tracking Ref #: <strong style="color: var(--maroon);">{{ $order->tracking_number }}</strong></span>
            </div>
        @endif
    </div>

    <!-- Items Table -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; margin-bottom: 32px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px;">Ordered Groceries & Items</h3>
        <div class="table-responsive">
            <table class="custom-table">
            <thead>
                <tr>
                    <th>Grocery Item</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td style="font-weight: 600; color: var(--maroon);">
                            {{ $item->product_name }} {{ $item->variant_name ? "({$item->variant_name})" : '' }}
                        </td>
                        <td style="font-size: 0.85rem; color: var(--muted);">{{ $item->sku }}</td>
                        <td>£{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($item->subtotal, 2) }}</td>
                        <td>
                            @if(in_array($order->order_status, ['confirmed', 'packed', 'shipped', 'delivered']))
                                <button type="button" 
                                        @click="openReviewModal = true; activeProductId = {{ $item->product_id }}; activeProductName = '{{ addslashes($item->product_name) }}'"
                                        class="btn btn-outline btn-sm" style="color: var(--saffron-deep); border-color: var(--saffron); padding: 6px 14px; font-weight: 600;">
                                    ★ Rate & Review
                                </button>
                            @else
                                <span style="font-size: 0.8rem; color: var(--muted);">Available upon confirmation</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

@endsection
