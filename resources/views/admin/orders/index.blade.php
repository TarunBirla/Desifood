@extends('layouts.admin')

@section('title', 'Order Management | Admin')
@section('page-title', 'Order Management & Fulfillment')

@section('content')

<div x-data="adminOrderApp()">
    @if(session('success'))
        <div style="background: #E8F8F5; border: 1px solid #A3E4D7; color: #117864; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #FDEDEC; border: 1px solid #FADBD8; color: #C0392B; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <form action="{{ route('admin.orders.index') }}" method="GET" style="display: flex; gap: 12px; margin: 0; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Order #, Customer Name, Email..." style="padding: 10px 18px; border: 1px solid var(--cream-dark); border-radius: 30px; width: 320px; outline: none; font-size: 0.92rem;">
            
            <select name="order_type" onchange="this.form.submit()" style="padding: 10px 18px; border: 1px solid var(--cream-dark); border-radius: 30px; outline: none; font-size: 0.92rem; background: var(--white); font-weight: 600; color: var(--maroon);">
                <option value="all">All Order Types</option>
                <option value="normal" {{ request('order_type') == 'normal' ? 'selected' : '' }}>Standard Orders</option>
                <option value="repeat" {{ request('order_type') == 'repeat' ? 'selected' : '' }}>Repeat Orders</option>
                <option value="recurring" {{ request('order_type') == 'recurring' ? 'selected' : '' }}>Next-Month Recurring</option>
            </select>

            <select name="status" onchange="this.form.submit()" style="padding: 10px 18px; border: 1px solid var(--cream-dark); border-radius: 30px; outline: none; font-size: 0.92rem; background: var(--white);">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="packed" {{ request('status') == 'packed' ? 'selected' : '' }}>Packed</option>
                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 30px; padding: 0 20px;">Filter</button>
        </form>
    </div>

    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer Name & Email</th>
                        <th>Order Type</th>
                        <th>Grand Total</th>
                        <th>Order Status</th>
                        <th>Payment</th>
                        <th>Date & Time</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td style="font-weight: 700; color: var(--maroon);">{{ $order->order_number }}</td>
                            <td>
                                @if($order->user)
                                    <div style="font-weight: 700; color: var(--maroon);">{{ $order->user->name }}</div>
                                    <div style="font-size: 0.82rem; color: var(--charcoal-light);">{{ $order->user->email }}</div>
                                @else
                                    <span style="color: var(--muted);">Guest User</span>
                                @endif
                            </td>
                            <td>
                                @if($order->order_type === 'repeat')
                                    <span class="badge-status" style="background: rgba(230,126,34,0.15); color: var(--saffron-deep); font-weight: 700; padding: 6px 12px; font-size: 0.82rem;">
                                        <i class="fa-solid fa-rotate-right me-1"></i> Repeat Order
                                    </span>
                                @elseif($order->order_type === 'recurring')
                                    <span class="badge-status" style="background: rgba(46,125,50,0.15); color: #2E7D32; font-weight: 700; padding: 6px 12px; font-size: 0.82rem;">
                                        <i class="fa-solid fa-calendar-check me-1"></i> Next-Month Recurring
                                    </span>
                                @else
                                    <span class="badge-status" style="background: rgba(137,15,20,0.08); color: var(--maroon); font-weight: 700; padding: 6px 12px; font-size: 0.82rem;">
                                        <i class="fa-solid fa-cart-shopping me-1"></i> Standard Order
                                    </span>
                                @endif
                            </td>
                            <td style="font-weight: 800; color: var(--maroon); font-size: 1.05rem;">£{{ number_format($order->grand_total, 2) }}</td>
                            <td><span class="badge-status badge-info">{{ ucfirst($order->order_status) }}</span></td>
                            <td><span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ strtoupper($order->payment_status) }}</span></td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center; justify-content: flex-end;">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary btn-sm" style="padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                        Process Order
                                    </a>

                                    @if($order->user)
                                        <button type="button" @click="openModal({{ json_encode(['id' => $order->user->id, 'name' => $order->user->name, 'email' => $order->user->email, 'order_number' => $order->order_number]) }})" 
                                                class="btn btn-outline btn-sm" 
                                                style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; color: var(--saffron-deep); border-color: var(--saffron);" title="Send direct notification email to {{ $order->user->name }}">
                                            <i class="fa-solid fa-paper-plane me-1"></i> Send Email
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--muted);">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 24px; border-top: 1px solid var(--cream-dark);">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Send Email Notification Modal Box (Centered Layout) -->
    <div x-show="showModal" 
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.55); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 999999; padding: 20px;" x-cloak>
        <div @click.away="showModal = false" 
             style="background: var(--white); border-radius: 24px; width: 100%; max-width: 540px; padding: 32px; box-shadow: var(--shadow-lg); border: 1px solid var(--cream-dark); margin: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;">
                    <i class="fa-solid fa-paper-plane me-2" style="color: var(--saffron);"></i> Direct Email Notification
                </h3>
                <button type="button" @click="showModal = false" style="background: none; border: none; font-size: 1.5rem; color: var(--muted); cursor: pointer;">&times;</button>
            </div>

            <form :action="'/admin/customers/' + activeUser.id + '/send-notification'" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Customer & Order Info</label>
                    <div style="background: var(--cream); padding: 12px 16px; border-radius: 12px; font-weight: 600; color: var(--maroon); border: 1px solid var(--cream-dark); font-size: 0.92rem;">
                        <span x-text="activeUser.name"></span> (<span x-text="activeUser.email" style="color: var(--saffron-deep);"></span>)<br>
                        <span style="font-size: 0.82rem; color: var(--muted);" x-text="'Ref Order: ' + activeUser.order_number"></span>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Email Subject</label>
                    <input type="text" name="subject" required :value="'Update regarding your Order #' + activeUser.order_number + ' - Desi Foods Hounslow'" style="width: 100%; padding: 12px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Notification Message</label>
                    <textarea name="message" rows="5" required placeholder="Write message to customer regarding order pickup, timing, items status..." style="width: 100%; padding: 12px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none; resize: vertical;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" @click="showModal = false" class="btn btn-outline" style="border-radius: 30px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 30px; font-weight: 700; padding: 10px 24px;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Email Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function adminOrderApp() {
        return {
            showModal: false,
            activeUser: { id: '', name: '', email: '', order_number: '' },
            openModal(user) {
                this.activeUser = user;
                this.showModal = true;
            }
        }
    }
</script>
@endsection
