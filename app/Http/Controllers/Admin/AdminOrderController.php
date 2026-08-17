<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->has('status') && $request->status) {
            $query->where('order_status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('search') && $request->search) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        $orders = $query->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'items.variant', 'user', 'statusHistories.changedBy', 'returnRequests'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'order_status' => 'required|string',
            'payment_status' => 'nullable|string',
            'notes' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'delivery_partner' => 'nullable|string',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        $updateData = ['order_status' => $newStatus];

        if ($request->filled('payment_status')) {
            $updateData['payment_status'] = $request->payment_status;
        }

        if ($request->filled('tracking_number')) {
            $updateData['tracking_number'] = $request->tracking_number;
        }
        if ($request->filled('delivery_partner')) {
            $updateData['delivery_partner'] = $request->delivery_partner;
        }
        if ($newStatus === 'shipped' && !$order->shipped_at) {
            $updateData['shipped_at'] = now();
        }
        if ($newStatus === 'delivered' && !$order->delivered_at) {
            $updateData['delivered_at'] = now();
        }

        $order->update($updateData);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'notes' => $request->notes ?: "Order status updated to {$newStatus} (Payment: " . strtoupper($order->payment_status) . ").",
            'changed_by' => Auth::id(),
        ]);

        return back()->with('success', "Order #{$order->order_number} status updated successfully.");
    }
}
