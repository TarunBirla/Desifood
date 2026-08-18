@extends('layouts.admin')

@section('title', 'Admin Notifications | Desi Foods Admin')

@section('content')

<div style="padding: 24px;">
    <!-- Page Title Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--maroon); margin-bottom: 4px;">Order & System Notifications</h1>
            <p style="color: #666; font-size: 0.95rem; margin: 0;">Live notifications for new orders placed by customers across the store.</p>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" class="btn {{ request('filter') === 'unread' ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 30px; font-weight: 600;">
                <i class="fa-solid fa-filter me-1"></i> Unread Only ({{ $unreadCount }})
            </a>
            <a href="{{ route('admin.notifications.index') }}" class="btn {{ !request('filter') ? 'btn-primary' : 'btn-outline-primary' }}" style="border-radius: 30px; font-weight: 600;">
                All Notifications
            </a>

            @if($unreadCount > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="border-radius: 30px; font-weight: 600; background: #fff;">
                        <i class="fa-solid fa-check-double me-1"></i> Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(46,125,50,0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 4px solid #2E7D32; margin-bottom: 24px; font-weight: 600;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Notifications List Card -->
    <div style="background: #fff; border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        @if($notifications->count() > 0)
            <div style="display: flex; flex-direction: column; divide-y: 1px solid var(--cream-dark);">
                @foreach($notifications as $noti)
                    <div style="padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; background: {{ $noti->is_read ? '#fff' : 'rgba(230,126,34,0.04)' }}; border-bottom: 1px solid var(--cream-dark); transition: background 0.2s ease;">
                        <!-- Icon & Main Details -->
                        <div style="display: flex; gap: 16px; align-items: flex-start; flex: 1; min-width: 280px;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: {{ $noti->is_read ? 'rgba(0,0,0,0.05)' : 'rgba(137,15,20,0.1)' }}; color: {{ $noti->is_read ? '#666' : 'var(--maroon)' }}; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                                <i class="fa-solid {{ $noti->is_read ? 'fa-bell' : 'fa-bell-concierge' }}"></i>
                            </div>

                            <div>
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                                    <strong style="font-size: 1.05rem; color: var(--maroon);">{{ $noti->title }}</strong>
                                    @if(!$noti->is_read)
                                        <span class="badge-status badge-danger" style="font-size: 0.72rem; padding: 2px 8px; border-radius: 12px;">NEW / UNREAD</span>
                                    @endif
                                </div>
                                <p style="margin: 0 0 6px 0; color: #444; font-size: 0.93rem; line-height: 1.4;">{{ $noti->message }}</p>

                                <div style="display: flex; gap: 16px; font-size: 0.82rem; color: #777; flex-wrap: wrap;">
                                    <span><i class="fa-solid fa-user me-1"></i> Customer: <strong>{{ $noti->user_name ?? 'N/A' }}</strong> ({{ $noti->user_email ?? 'N/A' }})</span>
                                    <span><i class="fa-solid fa-sterling-sign me-1"></i> Order Total: <strong style="color: var(--maroon);">£{{ number_format($noti->grand_total, 2) }}</strong></span>
                                    <span><i class="fa-solid fa-clock me-1"></i> {{ $noti->created_at->format('d M Y, h:i A') }} ({{ $noti->created_at->diffForHumans() }})</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 10px; align-items: center;">
                            @if($noti->order_id || $noti->order_number)
                                <a href="{{ route('admin.orders.show', $noti->order_id ?? 1) }}" class="btn btn-primary btn-sm" style="border-radius: 20px; font-weight: 600; padding: 8px 16px;">
                                    <i class="fa-solid fa-eye me-1"></i> View Order Details
                                </a>
                            @endif

                            @if(!$noti->is_read)
                                <form action="{{ route('admin.notifications.read', $noti->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm" style="border-radius: 20px; font-weight: 600; padding: 8px 14px;" title="Mark notification as seen">
                                        <i class="fa-solid fa-check me-1"></i> Mark as Seen
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.notifications.destroy', $noti->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Delete this notification?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #C0392B; cursor: pointer; padding: 8px; font-size: 1.1rem;" title="Delete notification">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="padding: 16px 24px;">
                {{ $notifications->links() }}
            </div>
        @else
            <div style="padding: 60px 20px; text-align: center;">
                <i class="fa-solid fa-bell-slash" style="font-size: 3.5rem; color: #ccc; margin-bottom: 16px; display: block;"></i>
                <h3 style="font-family: 'Playfair Display', serif; color: var(--maroon); margin-bottom: 8px;">No Notifications Found</h3>
                <p style="color: #777; margin: 0;">You have no {{ request('filter') === 'unread' ? 'unread' : '' }} notifications at this time.</p>
            </div>
        @endif
    </div>
</div>

@endsection
