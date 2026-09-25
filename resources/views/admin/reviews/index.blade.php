@extends('layouts.admin')

@section('title', 'Customer Reviews Moderation | Admin')
@section('page-title', 'Customer Reviews Moderation')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <form action="{{ route('admin.reviews.index') }}" method="GET" style="display: flex; gap: 12px;">
        <select name="status" onchange="this.form.submit()" style="padding: 8px 14px; border: 1px solid var(--line); border-radius: var(--radius);">
            <option value="">All Review Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </form>
</div>

<table class="custom-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Customer</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reviews as $r)
            <tr>
                <td style="font-weight: 600; color: var(--ink);">{{ $r->product ? $r->product->name : 'N/A' }}</td>
                <td>{{ $r->user ? $r->user->name : 'Anonymous' }}</td>
                <td style="color: var(--brass); font-weight: 700;">★ {{ $r->rating }} / 5</td>
                <td style="font-size: 0.88rem; color: var(--ink-soft); max-width: 320px;">{{ $r->comment }}</td>
                <td>
                    <span class="badge-status {{ $r->status == 'approved' ? 'badge-success' : ($r->status == 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                        {{ ucfirst($r->status) }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 6px;">
                        @if($r->status !== 'approved')
                            <form action="{{ route('admin.reviews.update-status', $r->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 4px 8px;">Approve</button>
                            </form>
                        @endif
                        @if($r->status !== 'rejected')
                            <form action="{{ route('admin.reviews.update-status', $r->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--clay); padding: 4px 8px;">Reject</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.reviews.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Delete this review?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm" style="color: var(--clay); padding: 4px 8px;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top: 24px;">{{ $reviews->links('vendor.pagination.custom') }}</div>

@endsection
