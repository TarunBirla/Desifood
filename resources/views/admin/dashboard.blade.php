@extends('layouts.admin')

@section('title', 'Admin Dashboard | Desi Foods Hounslow')
@section('page-title', 'Overview & Sales Analytics')

@section('content')

<!-- Key Performance Indicators (KPI Cards) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div class="stat-card">
        <span class="label">Total Grocery Revenue</span>
        <div class="value">£{{ number_format($totalSales, 2) }}</div>
        <div style="font-size: 0.82rem; color: var(--maroon); font-weight: 600;">Today: £{{ number_format($todaySales, 2) }}</div>
    </div>
    <div class="stat-card">
        <span class="label">Total Orders</span>
        <div class="value">{{ $totalOrders }}</div>
        <div style="font-size: 0.82rem; color: var(--muted);">Pending: {{ $pendingOrders }} | Delivered: {{ $deliveredOrders }}</div>
    </div>
    <div class="stat-card">
        <span class="label">Registered Shoppers</span>
        <div class="value">{{ $totalCustomers }}</div>
        <div style="font-size: 0.82rem; color: var(--muted);">Customer accounts</div>
    </div>
    <div class="stat-card">
        <span class="label">Low Stock Alerts</span>
        <div class="value" style="color: var(--saffron-deep);">{{ $lowStockProducts }}</div>
        <div style="font-size: 0.82rem; color: var(--muted);">Products ≤ 5 units</div>
    </div>
</div>

<!-- Sales Revenue Chart Container -->
<div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; margin-bottom: 32px; box-shadow: var(--shadow-sm);">
    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px;">Sales Trend (Last 7 Days)</h3>
    <div style="height: 280px;">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<!-- Recent Orders Table -->
<div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon);">Recent Store Orders</h3>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline btn-sm">View All Orders</a>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Order Status</th>
                <th>Payment</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
                <tr>
                    <td style="font-weight: 700; color: var(--maroon);">{{ $order->order_number }}</td>
                    <td>{{ $order->user ? $order->user->name : 'Guest Shopper' }}</td>
                    <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($order->grand_total, 2) }}</td>
                    <td><span class="badge-status badge-info">{{ ucfirst($order->order_status) }}</span></td>
                    <td><span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ strtoupper($order->payment_status) }}</span></td>
                    <td>{{ $order->created_at->format('d M H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline btn-sm">Manage</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const chartData = @json($salesByDay);
        
        const labels = chartData.map(d => d.date);
        const data = chartData.map(d => d.total);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Daily Revenue (£)',
                    data: data.length ? data : [120, 190, 150, 240, 320, 280, 450],
                    borderColor: '#890F14',
                    backgroundColor: 'rgba(137, 15, 20, 0.08)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endsection
