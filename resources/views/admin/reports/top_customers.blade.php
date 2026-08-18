@extends('layouts.admin')

@section('title', 'Top Customers & Loyalty Report')
@section('page-title', 'Top Customers & Loyalty Analytics')

@section('content')
<div style="display: flex; flex-direction: column; gap: 28px;">

    <!-- Top Navigation Bar -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 18px 24px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin-bottom: 4px;">Top Purchasing Customers Report</h2>
            <p style="color: var(--charcoal-light); font-size: 0.9rem; margin: 0;">Ranked by total expenditure (£ GBP), order frequency, repeat orders, and recurring scheduled orders.</p>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary" style="border-radius: 30px; font-weight: 600;">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Main Reports
            </a>
            <a href="{{ route('admin.reports.top-products') }}" class="btn btn-outline-primary" style="border-radius: 30px; font-weight: 600;">
                <i class="fa-solid fa-fire me-1" style="color: var(--saffron);"></i> Top Products
            </a>
        </div>
    </div>

    <!-- Visual Interactive Charts Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
        <!-- Chart 1: Customer Expenditure Bar Chart -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--maroon); margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fa-solid fa-chart-column me-2" style="color: var(--saffron);"></i> Top High-Spender Customers (£ Revenue)</span>
                <span style="font-size: 0.8rem; font-family: 'Inter', sans-serif; color: var(--muted); font-weight: 600;">Top 10 Buyers</span>
            </h3>
            <div style="position: relative; height: 280px;">
                <canvas id="topCustomersSpentChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Customer Orders Frequency Chart -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--maroon); margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fa-solid fa-boxes-packing me-2" style="color: var(--maroon);"></i> Customer Order Volume (Total Orders)</span>
                <span style="font-size: 0.8rem; font-family: 'Inter', sans-serif; color: var(--muted); font-weight: 600;">Order Count</span>
            </h3>
            <div style="position: relative; height: 280px;">
                <canvas id="topCustomersOrdersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Table: Top Customers -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        <div style="padding: 20px 24px; background: var(--cream); border-bottom: 1px solid var(--cream-dark); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--maroon); margin: 0;">
                <i class="fa-solid fa-crown me-2" style="color: var(--gold);"></i> High Value Customers Ranking Table (£ GBP Spent)
            </h3>
            <span style="font-size: 0.85rem; font-weight: 700; color: var(--maroon);">Showing {{ $topCustomers->count() }} Shoppers</span>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Customer Details</th>
                        <th>Orders Count</th>
                        <th>Total Spent (£)</th>
                        <th>Average Order (£)</th>
                        <th>Repeat Orders</th>
                        <th>Recurring Orders</th>
                        <th>Last Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCustomers as $index => $customer)
                        <tr>
                            <td style="font-weight: 800; color: var(--maroon); font-size: 1.1rem;">#{{ $topCustomers->firstItem() + $index }}</td>
                            <td>
                                <div style="font-weight: 700; color: var(--maroon);">{{ $customer->name }}</div>
                                <div style="font-size: 0.82rem; color: var(--muted);">{{ $customer->email }}</div>
                                <div style="font-size: 0.75rem; color: var(--saffron-deep);">Phone: {{ $customer->phone ?? 'N/A' }}</div>
                            </td>
                            <td style="font-weight: 700; color: var(--maroon);">{{ number_format($customer->total_orders) }} orders</td>
                            <td style="font-weight: 800; color: #2E7D32; font-size: 1.15rem;">£{{ number_format($customer->total_spent, 2) }}</td>
                            <td style="font-weight: 600; color: var(--maroon);">£{{ number_format($customer->avg_order_val, 2) }}</td>
                            <td>
                                <span class="badge-status badge-info" style="background: rgba(230,126,34,0.15); color: var(--saffron-deep); font-weight: 700;">
                                    {{ $customer->repeat_count }} Repeat
                                </span>
                            </td>
                            <td>
                                <span class="badge-status badge-success" style="background: rgba(46,125,50,0.15); color: #2E7D32; font-weight: 700;">
                                    {{ $customer->recurring_count }} Recurring
                                </span>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--charcoal-light);">
                                {{ $customer->last_order_date ? \Carbon\Carbon::parse($customer->last_order_date)->format('d M Y') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--muted);">No customer purchase history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 24px; border-top: 1px solid var(--cream-dark);">
            {{ $topCustomers->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = {!! json_encode($chartLabels) !!};
    const spentData = {!! json_encode($chartSpentData) !!};
    const ordersData = {!! json_encode($chartOrdersData) !!};

    // Chart 1: Customer Expenditure Bar Chart (£)
    const ctxSpent = document.getElementById('topCustomersSpentChart').getContext('2d');
    new Chart(ctxSpent, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Spent (£ GBP)',
                data: spentData,
                backgroundColor: 'rgba(137, 15, 20, 0.85)',
                borderColor: '#890F14',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Total Spent: £' + context.raw.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(val) { return '£' + val; }
                    }
                }
            }
        }
    });

    // Chart 2: Customer Order Count Line/Bar Chart
    const ctxOrders = document.getElementById('topCustomersOrdersChart').getContext('2d');
    new Chart(ctxOrders, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Orders Count',
                data: ordersData,
                backgroundColor: 'rgba(230, 126, 34, 0.85)',
                borderColor: '#D35400',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
});
</script>
@endsection
