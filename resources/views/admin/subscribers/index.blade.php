@extends('layouts.admin')

@section('title', 'Newsletter Subscribers | Admin Panel')

@section('content')
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--maroon); margin-bottom: 4px;">Newsletter Subscribers</h1>
        <p style="color: var(--muted); font-size: 0.9rem;">Manage email subscriptions for special offers and fresh arrival updates.</p>
    </div>
    
    <div style="background: rgba(137,15,20,0.08); color: var(--maroon); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-envelope-open-text"></i> Total Subscribers: {{ $subscribers->total() }}
    </div>
</div>

<div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 16px; padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
    <form action="{{ route('admin.subscribers.index') }}" method="GET" style="display: flex; gap: 12px; max-width: 450px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subscriber email..." class="form-control" style="flex: 1; padding: 10px 16px;">
        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</button>
        @if(request('search'))
            <a href="{{ route('admin.subscribers.index') }}" class="btn btn-outline" style="padding: 10px 16px;">Clear</a>
        @endif
    </form>
</div>

<div class="card" style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm);">
    <div class="table-responsive">
        <table class="custom-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--cream); border-bottom: 2px solid var(--cream-dark); text-align: left;">
                    <th style="padding: 14px 20px; font-size: 0.85rem; text-transform: uppercase; color: var(--maroon);">ID</th>
                    <th style="padding: 14px 20px; font-size: 0.85rem; text-transform: uppercase; color: var(--maroon);">Email Address</th>
                    <th style="padding: 14px 20px; font-size: 0.85rem; text-transform: uppercase; color: var(--maroon);">Subscribed Date</th>
                    <th style="padding: 14px 20px; font-size: 0.85rem; text-transform: uppercase; color: var(--maroon); text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscribers as $sub)
                    <tr style="border-bottom: 1px solid var(--cream-dark);">
                        <td style="padding: 14px 20px; font-weight: 600; color: var(--charcoal);">#{{ $sub->id }}</td>
                        <td style="padding: 14px 20px; font-weight: 600; color: var(--maroon); font-size: 0.95rem;">
                            <i class="fa-regular fa-envelope me-2" style="color: var(--saffron);"></i>{{ $sub->email }}
                        </td>
                        <td style="padding: 14px 20px; color: var(--charcoal-light); font-size: 0.88rem;">
                            {{ $sub->created_at ? $sub->created_at->format('d M Y, h:i A') : 'N/A' }}
                        </td>
                        <td style="padding: 14px 20px; text-align: right;">
                            <form action="{{ route('admin.subscribers.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this subscriber email?');" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: rgba(192,57,43,0.1); color: #c0392b; border: 1px solid rgba(192,57,43,0.2); padding: 6px 12px; border-radius: 8px;" title="Delete Subscriber">
                                    <i class="fa-solid fa-trash-can"></i> Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 40px; text-align: center; color: var(--muted);">
                            <i class="fa-regular fa-envelope-open" style="font-size: 2.5rem; color: var(--cream-dark); margin-bottom: 12px; display: block;"></i>
                            No newsletter subscribers found yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($subscribers->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--cream-dark);">
            {{ $subscribers->links() }}
        </div>
    @endif
</div>
@endsection
