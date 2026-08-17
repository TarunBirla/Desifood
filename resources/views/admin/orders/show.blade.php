@extends('layouts.admin')

@section('title', 'Process Order #' . $order->order_number . ' | Admin')
@section('page-title', 'Manage Order #' . $order->order_number)

@section('content')

<div style="display: grid; grid-template-columns: 1fr 380px; gap: 32px;">
    <!-- Left Column: Status & Payment Updater & Order Items -->
    <div>
        <!-- Status & Payment Updater Card -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--maroon); margin-bottom: 16px;">Update Order Status & Payment Status</h3>
            
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                @csrf
                <div>
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Order Status</label>
                    <select name="order_status" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px;">
                        <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="packed" {{ $order->order_status == 'packed' ? 'selected' : '' }}>Packed</option>
                        <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped / Out for Delivery</option>
                        <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="return_requested" {{ $order->order_status == 'return_requested' ? 'selected' : '' }}>Return Requested</option>
                        <option value="returned" {{ $order->order_status == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Payment Status</label>
                    <select name="payment_status" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px; font-weight: 600;" :style="'color: ' + ('{{ $order->payment_status }}' === 'paid' ? '#2E7D32' : '#C0392B')">
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid (Completed)</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Delivery Partner</label>
                    <input type="text" name="delivery_partner" value="{{ $order->delivery_partner }}" placeholder="e.g. Hounslow Doorstep Express / DPD" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px;">
                </div>

                <div>
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Tracking / AWB Number</label>
                    <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="e.g. AWB9876543210" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Internal Order Notes</label>
                    <input type="text" name="notes" placeholder="Notes visible in status history..." style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px;">
                </div>

                <div style="grid-column: span 2;">
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 24px;">Save Order & Payment Status</button>
                </div>
            </form>
        </div>

        <!-- Ordered Items -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; margin-bottom: 24px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--maroon); margin-bottom: 16px;">Order Items</h3>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td><strong>{{ $item->product_name }}</strong> {{ $item->variant_name ? "({$item->variant_name})" : '' }}</td>
                            <td style="font-size: 0.85rem; color: var(--muted);">{{ $item->sku }}</td>
                            <td>£{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Return Requests List if any -->
        @if($order->returnRequests && $order->returnRequests->count() > 0)
            <div style="background: #FDEDEC; border: 1px solid #FADBD8; border-radius: 20px; padding: 24px; margin-bottom: 24px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: #78281F; margin-bottom: 14px;">Customer Return Requests</h3>
                @foreach($order->returnRequests as $rr)
                    <div style="background: var(--white); border-radius: 12px; padding: 16px; margin-bottom: 12px; border: 1px solid #F5B7B1;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 700; color: #78281F; margin-bottom: 6px;">
                            <span>Reason: {{ $rr->reason }}</span>
                            <span>Status: {{ strtoupper($rr->status) }}</span>
                        </div>
                        <p style="font-size: 0.88rem; color: var(--charcoal-light); margin: 0;">{{ $rr->description }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Status Audit History -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--maroon); margin-bottom: 16px;">Status History Log</h3>
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
                @foreach($order->statusHistories as $h)
                    <li style="border-left: 3px solid var(--saffron); padding-left: 12px; font-size: 0.88rem;">
                        <div style="font-weight: 700; color: var(--maroon);">{{ ucfirst($h->status) }}</div>
                        <div style="color: var(--charcoal-light);">{{ $h->notes }}</div>
                        <div style="font-size: 0.78rem; color: var(--muted);">By: {{ $h->changedBy ? $h->changedBy->name : 'System' }} on {{ $h->created_at->format('d M Y H:i') }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Right Summary Card -->
    <div>
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm);">
            <a href="{{ route('account.invoice.download', $order->order_number) }}" target="_blank" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 20px; text-align: center; display: block;">
                📄 Print Tax Invoice
            </a>

            <h4 style="font-family: 'Playfair Display', serif; color: var(--maroon); margin-bottom: 12px;">Customer Details</h4>
            <p style="font-size: 0.88rem; color: var(--charcoal-light); line-height: 1.6; margin-bottom: 20px;">
                <strong>{{ $order->user ? $order->user->name : 'Guest' }}</strong><br>
                Email: {{ $order->user ? $order->user->email : 'N/A' }}<br>
                Phone: {{ $order->user ? $order->user->phone : 'N/A' }}
            </p>

            <h4 style="font-family: 'Playfair Display', serif; color: var(--maroon); margin-bottom: 12px;">Shipping Address</h4>
            <p style="font-size: 0.88rem; color: var(--charcoal-light); line-height: 1.6; margin-bottom: 20px;">
                {{ $order->shipping_address_json['name'] ?? '' }}<br>
                {{ $order->shipping_address_json['address_line_1'] ?? '' }}<br>
                {{ $order->shipping_address_json['city'] ?? '' }}, {{ $order->shipping_address_json['state'] ?? '' }} - {{ $order->shipping_address_json['pincode'] ?? '' }}
            </p>

            <h4 style="font-family: 'Playfair Display', serif; color: var(--maroon); margin-bottom: 12px;">Financial Summary</h4>
            <div style="font-size: 0.9rem; line-height: 1.8;">
                <div style="display: flex; justify-content: space-between;"><span>Payment Status:</span> <strong style="color: {{ $order->payment_status === 'paid' ? '#2E7D32' : '#C0392B' }};">{{ strtoupper($order->payment_status) }}</strong></div>
                <div style="display: flex; justify-content: space-between;"><span>Payment Method:</span> <span>{{ strtoupper($order->payment_method) }}</span></div>
                <div style="display: flex; justify-content: space-between;"><span>Subtotal:</span> <span>£{{ number_format($order->subtotal, 2) }}</span></div>
                <div style="display: flex; justify-content: space-between;"><span>Discount:</span> <span>-£{{ number_format($order->discount_amount, 2) }}</span></div>
                <div style="display: flex; justify-content: space-between;"><span>Shipping:</span> <span>£{{ number_format($order->shipping_fee, 2) }}</span></div>
                <div style="display: flex; justify-content: space-between;"><span>UK Grocery VAT (0%):</span> <span>£0.00</span></div>
                <hr style="border: none; border-top: 1px solid var(--cream-dark); margin: 8px 0;">
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2rem; color: var(--maroon);"><span>Grand Total:</span> <span>£{{ number_format($order->grand_total, 2) }}</span></div>
            </div>
        </div>
    </div>
</div>

@endsection
