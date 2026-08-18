<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders')->whereHas('role', function ($q) {
            $q->where('name', 'customer');
        });

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
        }

        $customers = $query->latest()->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        $newStatus = $user->status === 'blocked' ? 'active' : 'blocked';
        $user->update(['status' => $newStatus]);

        return back()->with('success', "Customer {$user->name} is now " . ucfirst($newStatus) . ".");
    }

    public function subscribers(Request $request)
    {
        $query = \App\Models\NewsletterSubscriber::query();
        if ($request->has('search') && $request->search) {
            $query->where('email', 'like', "%{$request->search}%");
        }
        $subscribers = $query->latest()->paginate(20);
        return view('admin.subscribers.index', compact('subscribers'));
    }

    public function deleteSubscriber($id)
    {
        $subscriber = \App\Models\NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();
        return back()->with('success', 'Subscriber deleted successfully.');
    }
}
