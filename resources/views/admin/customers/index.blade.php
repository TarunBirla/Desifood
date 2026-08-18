@extends('layouts.admin')

@section('title', 'Shopper Directory & Customer Notifications | Admin')
@section('page-title', 'Shopper Directory & Customer Notifications')

@section('content')

<div x-data="customerListApp()">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <form action="{{ route('admin.customers.index') }}" method="GET" style="display: flex; gap: 12px; margin: 0;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..." style="padding: 10px 18px; border: 1px solid var(--cream-dark); border-radius: 30px; width: 320px; outline: none; font-size: 0.92rem;">
            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 30px; padding: 0 20px;">Search</button>
        </form>
    </div>

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

    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Customer Info</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th>Account Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td style="font-weight: 700; color: var(--maroon);">{{ $c->name }}</td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->phone ?: 'N/A' }}</td>
                            <td>
                                <span class="badge-status badge-info" style="font-size: 0.85rem; font-weight: 700;">
                                    {{ $c->orders_count }} Orders
                                </span>
                            </td>
                            <td>
                                <span class="badge-status {{ $c->status == 'blocked' ? 'badge-danger' : 'badge-success' }}">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center; justify-content: flex-end; flex-wrap: wrap;">
                                    <!-- Button 1: View User Particular Orders -->
                                    <a href="{{ route('admin.orders.index', ['search' => $c->email]) }}" class="btn btn-outline btn-sm" style="padding: 6px 14px; font-weight: 600; border-radius: 20px; font-size: 0.85rem;" title="View all orders placed by {{ $c->name }}">
                                        <i class="fa-solid fa-box me-1" style="color: var(--saffron);"></i> View User Orders
                                    </a>

                                    <!-- Button 2: Send Direct Email Notification Modal Trigger -->
                                    <button type="button" @click="openModal({{ json_encode(['id' => $c->id, 'name' => $c->name, 'email' => $c->email]) }})" class="btn btn-sm btn-primary" style="padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 0.85rem;" title="Send direct notification email to {{ $c->name }}">
                                        <i class="fa-solid fa-paper-plane me-1"></i> Send Email
                                    </button>

                                    <!-- Button 3: Block/Unblock Account -->
                                    <form action="{{ route('admin.customers.block', $c->id) }}" method="POST" style="display: inline-block; margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm" style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; {{ $c->status == 'blocked' ? 'color: #2E7D32; border-color: #2E7D32;' : 'color: #C0392B; border-color: #C0392B;' }}">
                                            {{ $c->status == 'blocked' ? 'Unblock' : 'Block' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted);">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 24px; border-top: 1px solid var(--cream-dark);">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Send Email Notification Modal Box -->
    <div x-show="showModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.55); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 999999; padding: 20px;" x-cloak>
        <div @click.away="showModal = false" style="background: var(--white); border-radius: 24px; width: 100%; max-width: 540px; padding: 32px; box-shadow: var(--shadow-lg); border: 1px solid var(--cream-dark); margin: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;">
                    <i class="fa-solid fa-paper-plane me-2" style="color: var(--saffron);"></i> Send Direct Email Notification
                </h3>
                <button type="button" @click="showModal = false" style="background: none; border: none; font-size: 1.4rem; color: var(--muted); cursor: pointer;">&times;</button>
            </div>

            <form :action="'/admin/customers/' + activeUser.id + '/send-notification'" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Recipient Customer</label>
                    <div style="background: var(--cream); padding: 10px 14px; border-radius: 10px; font-weight: 600; color: var(--maroon); border: 1px solid var(--cream-dark);">
                        <span x-text="activeUser.name"></span> (<span x-text="activeUser.email" style="color: var(--saffron-deep);"></span>)
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Email Subject</label>
                    <input type="text" name="subject" required placeholder="e.g. Special Discount Offer for Desi Foods Hounslow" style="width: 100%; padding: 12px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Notification Message</label>
                    <textarea name="message" rows="5" required placeholder="Write your notification message to the user..." style="width: 100%; padding: 12px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none; resize: vertical;"></textarea>
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
    function customerListApp() {
        return {
            showModal: false,
            activeUser: { id: '', name: '', email: '' },
            openModal(user) {
                this.activeUser = user;
                this.showModal = true;
            }
        }
    }
</script>
@endsection
