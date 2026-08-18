@extends('layouts.admin')

@section('title', 'Best-Selling Products Report')
@section('page-title', 'Best-Selling Products Report')

@section('content')
<div style="display: flex; flex-direction: column; gap: 28px;">

    <!-- Top Bar Navigation -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 18px 24px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin-bottom: 4px;">Top Performing Groceries & Products</h2>
            <p style="color: var(--charcoal-light); font-size: 0.9rem; margin: 0;">Ranked by sales volume, units sold, and revenue generated.</p>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary" style="border-radius: 30px; font-weight: 600;">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Main Reports
            </a>
            <a href="{{ route('admin.reports.top-customers') }}" class="btn btn-outline-primary" style="border-radius: 30px; font-weight: 600;">
                <i class="fa-solid fa-user-group me-1" style="color: var(--saffron);"></i> Top Customers
            </a>
        </div>
    </div>

    <!-- Top Products Units Sold Chart -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px;">Top Products Volume (Units Sold)</h3>
        <div style="position: relative; height: 300px;">
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>

    <!-- Table 1: Ranked by Units Sold -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        <div style="padding: 20px 24px; background: var(--cream); border-bottom: 1px solid var(--cream-dark);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--maroon); margin: 0;">
                <i class="fa-solid fa-trophy me-2" style="color: var(--gold);"></i> Most Popular Products (By Units Dispatched)
            </h3>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Product Info</th>
                        <th>Units Sold</th>
                        <th>Orders Containing Item</th>
                        <th>Total Revenue (£ GBP)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topByUnits as $index => $item)
                        @php 
                            $product = $item->product;
                            $img = $product && $product->primaryImage ? $product->primaryImage->image_path : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800';
                        @endphp
                        <tr>
                            <td style="font-weight: 800; color: var(--maroon); font-size: 1.1rem;">#{{ $index + 1 }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 14px;">
                                    <img src="{{ $img }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px; border: 1px solid var(--cream-dark);">
                                    <div>
                                        <div style="font-weight: 700; color: var(--maroon);">{{ $product ? $product->name : 'Product #' . $item->product_id }}</div>
                                        <div style="font-size: 0.8rem; color: var(--saffron-deep);">SKU: {{ $product ? $product->sku : 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight: 800; color: var(--maroon); font-size: 1.1rem;">{{ number_format($item->total_units) }} pcs</td>
                            <td style="font-weight: 600;">{{ number_format($item->total_orders) }} orders</td>
                            <td style="font-weight: 800; color: #2E7D32; font-size: 1.1rem;">£{{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--muted);">No sales data logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Units Sold',
                        data: {!! json_encode($chartUnitsData) !!},
                        backgroundColor: '#890F14',
                        borderRadius: 8
                    },
                    {
                        label: 'Revenue (£ GBP)',
                        data: {!! json_encode($chartRevenueData) !!},
                        backgroundColor: '#E67E22',
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection
