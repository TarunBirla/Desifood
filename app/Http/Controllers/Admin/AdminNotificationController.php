<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminNotification::with('order')->orderBy('created_at', 'desc');

        if ($request->has('filter') && $request->filter === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = AdminNotification::where('is_read', false)->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        AdminNotification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted successfully.');
    }
}
