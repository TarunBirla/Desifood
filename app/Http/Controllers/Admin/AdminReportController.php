<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RecurringOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly, custom
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');
        $orderType = $request->get('order_type', 'all');
        $orderStatus = $request->get('order_status', 'all');
        $search = $request->get('search');

        // Determine Date Range
        $now = Carbon::now();
        switch ($period) {
            case 'daily':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'yearly':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'custom':
                $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : $now->copy()->subDays(30)->startOfDay();
                $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : $now->copy()->endOfDay();
                break;
            case 'monthly':
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
        }

        // Base Orders Query
        $query = Order::with(['user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($orderType !== 'all') {
            $query->where('order_type', $orderType);
        }

        if ($orderStatus !== 'all') {
            $query->where('order_status', $orderStatus);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Aggregate KPI Cards
        $totalOrders = (clone $query)->count();
        $totalSales = (clone $query)->where('payment_status', 'paid')->sum('grand_total');
        $avgOrderValue = $totalOrders > 0 ? ($totalSales / $totalOrders) : 0.00;
        
        $orderIds = (clone $query)->pluck('id');
        $totalProductsSold = OrderItem::whereIn('order_id', $orderIds)->sum('quantity');

        $totalCustomers = (clone $query)->distinct('user_id')->count('user_id');
        $repeatOrdersCount = (clone $query)->where('order_type', 'repeat')->count();
        $recurringOrdersCount = (clone $query)->where('order_type', 'recurring')->count();
        $completedOrdersCount = (clone $query)->where('order_status', 'delivered')->count();
        $cancelledOrdersCount = (clone $query)->where('order_status', 'cancelled')->count();

        // Chart 1 & 2: Sales & Orders Trend Over Time
        $salesTrend = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total_sales'),
                DB::raw('COUNT(id) as total_orders')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartLabels = $salesTrend->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $chartSalesData = $salesTrend->pluck('total_sales')->map(fn($v) => (float)$v)->toArray();
        $chartOrdersData = $salesTrend->pluck('total_orders')->map(fn($v) => (int)$v)->toArray();

        // Chart 3: Order Type Distribution
        $normalCount = (clone $query)->where(fn($q) => $q->whereNull('order_type')->orWhere('order_type', 'normal'))->count();
        $repeatCount = (clone $query)->where('order_type', 'repeat')->count();
        $recurringCount = (clone $query)->where('order_type', 'recurring')->count();

        // Paginated Orders Table
        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reports.index', compact(
            'period', 'startDate', 'endDate', 'orderType', 'orderStatus', 'search',
            'totalOrders', 'totalSales', 'avgOrderValue', 'totalProductsSold',
            'totalCustomers', 'repeatOrdersCount', 'recurringOrdersCount',
            'completedOrdersCount', 'cancelledOrdersCount',
            'chartLabels', 'chartSalesData', 'chartOrdersData',
            'normalCount', 'repeatCount', 'recurringCount',
            'orders'
        ));
    }

    public function topProducts(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $topByUnits = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_units'), DB::raw('SUM(subtotal) as total_revenue'), DB::raw('COUNT(DISTINCT order_id) as total_orders'))
            ->with('product.primaryImage')
            ->groupBy('product_id')
            ->orderBy('total_units', 'desc')
            ->take($limit)
            ->get();

        $topByRevenue = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_units'), DB::raw('SUM(subtotal) as total_revenue'), DB::raw('COUNT(DISTINCT order_id) as total_orders'))
            ->with('product.primaryImage')
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->take($limit)
            ->get();

        $chartLabels = $topByUnits->map(fn($item) => $item->product ? Str::limit($item->product->name, 18) : 'Product #' . $item->product_id)->toArray();
        $chartUnitsData = $topByUnits->pluck('total_units')->map(fn($v) => (int)$v)->toArray();
        $chartRevenueData = $topByUnits->pluck('total_revenue')->map(fn($v) => (float)$v)->toArray();

        return view('admin.reports.top_products', compact('topByUnits', 'topByRevenue', 'chartLabels', 'chartUnitsData', 'chartRevenueData'));
    }

    public function topCustomers(Request $request)
    {
        $topCustomers = User::select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.created_at',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.grand_total) as total_spent'),
                DB::raw('AVG(orders.grand_total) as avg_order_val'),
                DB::raw('MAX(orders.created_at) as last_order_date'),
                DB::raw('SUM(CASE WHEN orders.order_type = "repeat" THEN 1 ELSE 0 END) as repeat_count'),
                DB::raw('SUM(CASE WHEN orders.order_type = "recurring" THEN 1 ELSE 0 END) as recurring_count')
            )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone', 'users.created_at')
            ->orderBy('total_spent', 'desc')
            ->paginate(20);

        // Prepare Top 10 chart arrays
        $topTen = $topCustomers->slice(0, 10);
        $chartLabels = $topTen->pluck('name')->map(function($name) { return Str::limit($name, 16); })->toArray();
        $chartSpentData = $topTen->pluck('total_spent')->toArray();
        $chartOrdersData = $topTen->pluck('total_orders')->toArray();

        return view('admin.reports.top_customers', compact('topCustomers', 'chartLabels', 'chartSpentData', 'chartOrdersData'));
    }

    public function exportCsv(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $orderType = $request->get('order_type', 'all');
        $orderStatus = $request->get('order_status', 'all');

        $query = Order::with(['user', 'items']);

        if ($orderType !== 'all') {
            $query->where('order_type', $orderType);
        }

        if ($orderStatus !== 'all') {
            $query->where('order_status', $orderStatus);
        }

        $orders = $query->latest()->get();

        $filename = "desi_foods_sales_report_" . date('Y_m_d_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($file, [
                'Order Number',
                'Order Date',
                'Customer Name',
                'Customer Email',
                'Order Type',
                'Order Status',
                'Payment Status',
                'Payment Method',
                'Items Count',
                'Subtotal (£)',
                'Shipping Fee (£)',
                'Discount (£)',
                'Grand Total (£)'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user ? $order->user->name : 'Guest',
                    $order->user ? $order->user->email : 'N/A',
                    ucfirst($order->order_type ?? 'normal'),
                    ucfirst($order->order_status),
                    strtoupper($order->payment_status),
                    strtoupper($order->payment_method),
                    $order->items->count(),
                    number_format($order->subtotal, 2),
                    number_format($order->shipping_fee, 2),
                    number_format($order->discount_amount, 2),
                    number_format($order->grand_total, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
