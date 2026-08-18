@extends('layouts.admin')

@section('title', 'Sales & Order Analytics Reports')
@section('page-title', 'Sales & Order Analytics Reports')

@section('content')
<div style="display: flex; flex-direction: column; gap: 28px;">

    <!-- Top Action Bar with Period Filters & Export Button -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 20px 24px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 0;">
            <select name="period" style="padding: 10px 16px; border: 1px solid var(--cream-dark); border-radius: 30px; font-weight: 600; color: var(--maroon); outline: none; background: var(--cream);" onchange="this.form.submit()">
                <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Today (Daily)</option>
                <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>This Week</option>
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>This Year</option>
                <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
            </select>

            @if($period === 'custom')
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" style="padding: 8px 14px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.9rem;">
                <span style="color: var(--muted); font-weight: 600;">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" style="padding: 8px 14px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.9rem;">
                <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 20px; padding: 8px 16px;">Apply</button>
            @endif

            <select name="order_type" style="padding: 10px 16px; border: 1px solid var(--cream-dark); border-radius: 30px; font-weight: 600; color: var(--charcoal); outline: none;" onchange="this.form.submit()">
                <option value="all" {{ $orderType === 'all' ? 'selected' : '' }}>All Order Types</option>
                <option value="normal" {{ $orderType === 'normal' ? 'selected' : '' }}>Normal Orders</option>
                <option value="repeat" {{ $orderType === 'repeat' ? 'selected' : '' }}>Repeat Orders</option>
                <option value="recurring" {{ $orderType === 'recurring' ? 'selected' : '' }}>Recurring Orders</option>
            </select>

            <select name="order_status" style="padding: 10px 16px; border: 1px solid var(--cream-dark); border-radius: 30px; font-weight: 600; color: var(--charcoal); outline: none;" onchange="this.form.submit()">
                <option value="all" {{ $orderStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="pending" {{ $orderStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $orderStatus === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ $orderStatus === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ $orderStatus === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ $orderStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>

        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('admin.reports.top-products') }}" class="btn btn-outline-primary" style="border-radius: 30px; padding: 10px 18px; font-weight: 600;">
                <i class="fa-solid fa-fire me-1" style="color: var(--saffron);"></i> Top Products
            </a>
            <a href="{{ route('admin.reports.top-customers') }}" class="btn btn-outline-primary" style="border-radius: 30px; padding: 10px 18px; font-weight: 600;">
                <i class="fa-solid fa-user-group me-1" style="color: var(--saffron);"></i> Top Customers
            </a>
            <a href="{{ route('admin.reports.export', request()->all()) }}" class="btn btn-primary" style="border-radius: 30px; padding: 10px 20px; font-weight: 700;">
                <i class="fa-solid fa-file-csv me-1"></i> Export Report (CSV)
            </a>
        </div>
    </div>

    <!-- 9 Sales Summary KPI Cards (Strictly GBP £) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Total Gross Revenue</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: var(--maroon);">£{{ number_format($totalSales, 2) }}</div>
            <div style="font-size: 0.78rem; color: #2E7D32; font-weight: 600; margin-top: 4px;"><i class="fa-solid fa-chart-line me-1"></i> Paid completed orders</div>
        </div>

        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Total Orders</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: var(--maroon);">{{ number_format($totalOrders) }}</div>
            <div style="font-size: 0.78rem; color: var(--saffron-deep); font-weight: 600; margin-top: 4px;"><i class="fa-solid fa-box-archive me-1"></i> Orders in date range</div>
        </div>

        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Average Order Value (AOV)</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: var(--maroon);">£{{ number_format($avgOrderValue, 2) }}</div>
            <div style="font-size: 0.78rem; color: var(--charcoal-light); font-weight: 600; margin-top: 4px;">Per transaction avg</div>
        </div>

        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Products Sold</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: var(--maroon);">{{ number_format($totalProductsSold) }}</div>
            <div style="font-size: 0.78rem; color: var(--saffron-deep); font-weight: 600; margin-top: 4px;">Units dispatched</div>
        </div>

        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Active Shoppers</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: var(--maroon);">{{ number_format($totalCustomers) }}</div>
            <div style="font-size: 0.78rem; color: #2E7D32; font-weight: 600; margin-top: 4px;">Unique purchasing users</div>
        </div>

        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Repeat Orders</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: var(--saffron-deep);">{{ number_format($repeatOrdersCount) }}</div>
            <div style="font-size: 0.78rem; color: var(--saffron); font-weight: 600; margin-top: 4px;"><i class="fa-solid fa-rotate-right me-1"></i> Customer re-orders</div>
        </div>

        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Recurring Next-Month Orders</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: #2E7D32;">{{ number_format($recurringOrdersCount) }}</div>
            <div style="font-size: 0.78rem; color: #2E7D32; font-weight: 600; margin-top: 4px;"><i class="fa-solid fa-calendar-check me-1"></i> Monthly scheduled</div>
        </div>

        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Delivered Orders</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: #27AE60;">{{ number_format($completedOrdersCount) }}</div>
            <div style="font-size: 0.78rem; color: #27AE60; font-weight: 600; margin-top: 4px;">Completed & fulfilled</div>
        </div>

        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 18px; padding: 20px; box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600; margin-bottom: 6px;">Cancelled Orders</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: #C0392B;">{{ number_format($cancelledOrdersCount) }}</div>
            <div style="font-size: 0.78rem; color: #C0392B; font-weight: 600; margin-top: 4px;">Voided transactions</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Sales & Order Trend Chart -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px;">Sales & Order Volume Trend (£ GBP)</h3>
            <div style="position: relative; height: 320px;">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Order Type Distribution Doughnut -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px;">Order Type Breakdown</h3>
            <div style="position: relative; height: 260px; display: flex; justify-content: center; align-items: center;">
                <canvas id="orderTypeChart"></canvas>
            </div>
            <div style="margin-top: 16px; display: flex; justify-content: space-around; text-align: center; font-size: 0.85rem;">
                <div><span style="display: inline-block; width: 12px; height: 12px; background: #890F14; border-radius: 50%; margin-right: 4px;"></span> Normal ({{ $normalCount }})</div>
                <div><span style="display: inline-block; width: 12px; height: 12px; background: #E67E22; border-radius: 50%; margin-right: 4px;"></span> Repeat ({{ $repeatCount }})</div>
                <div><span style="display: inline-block; width: 12px; height: 12px; background: #27AE60; border-radius: 50%; margin-right: 4px;"></span> Recurring ({{ $recurringCount }})</div>
            </div>
        </div>
    </div>

    <!-- Detailed Filterable Orders Report Table -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--cream-dark); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin: 0;">Detailed Transactions Report</h3>

            <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; gap: 10px; margin: 0;">
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="order_type" value="{{ $orderType }}">
                <input type="hidden" name="order_status" value="{{ $orderStatus }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by order #, customer..." style="padding: 8px 16px; border: 1px solid var(--cream-dark); border-radius: 20px; font-size: 0.9rem; outline: none; width: 240px;">
                <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 20px; padding: 8px 16px;">Search</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Order Type</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Items</th>
                        <th>Grand Total (£)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td style="font-weight: 700; color: var(--maroon);">
                                <a href="{{ route('admin.orders.show', $order->id) }}" style="color: var(--maroon); text-decoration: none;">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                @if($order->user)
                                    <div style="font-weight: 600; color: var(--maroon);">{{ $order->user->name }}</div>
                                    <div style="font-size: 0.78rem; color: var(--muted);">{{ $order->user->email }}</div>
                                @else
                                    <span style="color: var(--muted);">Guest User</span>
                                @endif
                            </td>
                            <td>
                                @if($order->order_type === 'repeat')
                                    <span class="badge-status badge-info" style="background: rgba(230,126,34,0.15); color: var(--saffron-deep); border: 1px solid rgba(230,126,34,0.3); font-weight: 700;"><i class="fa-solid fa-rotate-right me-1"></i> Repeat</span>
                                @elseif($order->order_type === 'recurring')
                                    <span class="badge-status badge-info" style="background: rgba(46,125,50,0.15); color: #2E7D32; border: 1px solid rgba(46,125,50,0.3); font-weight: 700;"><i class="fa-solid fa-calendar-check me-1"></i> Recurring</span>
                                @else
                                    <span style="color: var(--muted); font-size: 0.85rem;">Normal</span>
                                @endif
                            </td>
                            <td>
                                @switch($order->order_status)
                                    @case('delivered')
                                        <span class="badge-status badge-success">Delivered</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge-status badge-danger">Cancelled</span>
                                        @break
                                    @default
                                        <span class="badge-status badge-warning">{{ ucfirst($order->order_status) }}</span>
                                @endswitch
                            </td>
                            <td><span class="badge-status {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">{{ strtoupper($order->payment_status) }}</span></td>
                            <td style="font-weight: 600;">{{ $order->items->sum('quantity') }} pcs ({{ $order->items->count() }} items)</td>
                            <td style="font-weight: 800; color: var(--maroon); font-size: 1.05rem;">£{{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--muted);">
                                No transaction records found matching the active date and filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 24px; border-top: 1px solid var(--cream-dark);">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<!-- Chart.js Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sales Trend Line Chart
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Gross Sales (£ GBP)',
                        data: {!! json_encode($chartSalesData) !!},
                        borderColor: '#890F14',
                        backgroundColor: 'rgba(137, 15, 20, 0.08)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Order Count',
                        data: {!! json_encode($chartOrdersData) !!},
                        borderColor: '#E67E22',
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
                        borderDash: [5, 5],
                        tension: 0.3,
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: { callback: value => '£' + value }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // Order Type Doughnut Chart
        const typeCtx = document.getElementById('orderTypeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Normal Orders', 'Repeat Orders', 'Recurring Orders'],
                datasets: [{
                    data: [{{ $normalCount }}, {{ $repeatCount }}, {{ $recurringCount }}],
                    backgroundColor: ['#890F14', '#E67E22', '#27AE60'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection
