@extends('layouts.app')

@section('title', 'Saved Addresses | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;" x-data="{ showModal: false }">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon);">Saved Delivery Addresses</h1>
        <button type="button" @click="showModal = true" class="btn btn-primary btn-sm">+ Add New Address</button>
    </div>

    <div class="catalog-layout">
        <!-- Sidebar -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 20px; height: fit-content; box-shadow: var(--shadow-sm);">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 6px;">
                <li><a href="{{ route('account.dashboard') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Dashboard</a></li>
                <li><a href="{{ route('account.orders') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">My Grocery Orders</a></li>
                <li><a href="{{ route('account.profile') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Profile Details</a></li>
                <li><a href="{{ route('account.addresses') }}" style="display: block; padding: 12px 16px; font-weight: 700; color: var(--maroon); background: rgba(137, 15, 20, 0.08); border-radius: 12px;">Saved Delivery Addresses</a></li>
                <li><a href="{{ route('account.wishlist') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Wishlist</a></li>
            </ul>
        </div>

        <!-- Addresses List -->
        <div>
            @if($addresses->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    @foreach($addresses as $addr)
                        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm); position: relative;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <span class="badge-status badge-info" style="text-transform: uppercase;">{{ $addr->address_type }}</span>
                                @if($addr->is_default)
                                    <span class="badge-status badge-success">Default</span>
                                @endif
                            </div>
                            <h4 style="font-size: 1.1rem; color: var(--maroon); margin-bottom: 8px;">{{ $addr->name }}</h4>
                            <p style="font-size: 0.9rem; color: var(--charcoal-light); line-height: 1.6; margin-bottom: 16px;">
                                {{ $addr->address_line_1 }}<br>
                                @if($addr->address_line_2){{ $addr->address_line_2 }}<br>@endif
                                {{ $addr->city }}, {{ $addr->state }} - <strong>{{ $addr->pincode }}</strong><br>
                                Phone: {{ $addr->phone }}
                            </p>
                            <form action="{{ route('account.addresses.delete', $addr->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: var(--saffron-deep); cursor: pointer; font-size: 0.85rem; font-weight: 600;">Delete Address</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background: var(--white); border: 1px solid var(--cream-dark); padding: 40px; text-align: center; border-radius: 16px;">
                    <p style="color: var(--charcoal-light); margin-bottom: 16px;">No saved delivery addresses found.</p>
                    <button type="button" @click="showModal = true" class="btn btn-primary btn-sm">+ Add Delivery Address</button>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal for adding new address -->
    <template x-teleport="body">
        <div x-show="showModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 20px;" x-cloak>
            <div @click.away="showModal = false" style="background: var(--white); border-radius: 20px; padding: 32px; width: 100%; max-width: 540px; box-shadow: var(--shadow-lg);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon);">Add Delivery Address</h3>
                    <button type="button" @click="showModal = false" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
                </div>
                <form action="{{ route('account.addresses.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Jyoshna Patel" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="07700 900123" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" name="address_line_1" class="form-control" placeholder="House / Flat #, Street" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address Line 2 (Optional)</label>
                        <input type="text" name="address_line_2" class="form-control" placeholder="Locality / Area">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label class="form-label">Town / City</label>
                            <input type="text" name="city" class="form-control" value="Hounslow" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Postcode</label>
                            <input type="text" name="pincode" class="form-control" placeholder="TW3 2EN" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">County / State</label>
                        <input type="text" name="state" class="form-control" value="Greater London" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address Type</label>
                        <select name="address_type" class="form-control">
                            <option value="home">Home</option>
                            <option value="work">Work</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
                        <button type="button" @click="showModal = false" class="btn btn-outline btn-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

@endsection
