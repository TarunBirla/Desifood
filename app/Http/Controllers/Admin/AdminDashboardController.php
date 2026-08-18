<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalSales = Order::where('payment_status', 'paid')->sum('grand_total');
        $todaySales = Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('grand_total');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $deliveredOrders = Order::where('order_status', 'delivered')->count();
        $totalCustomers = User::whereHas('role', function ($q) { $q->where('name', 'customer'); })->count();
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 5)->count();

        $recentOrders = Order::with('user')->latest()->take(8)->get();

        // Chart data - Sales last 7 days
        $salesByDay = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'))
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'totalSales', 'todaySales', 'totalOrders', 'pendingOrders',
            'deliveredOrders', 'totalCustomers', 'totalProducts', 'lowStockProducts',
            'recentOrders', 'salesByDay'
        ));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->filled('current_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match our records.']);
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success', 'Admin profile details updated successfully!');
    }
}
