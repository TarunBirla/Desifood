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

    <!-- Centered Modal for adding new address with proper scrollable container -->
    <template x-teleport="body">
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="position: fixed; inset: 0; background: rgba(44, 24, 16, 0.75); backdrop-filter: blur(8px); z-index: 999999; display: grid; place-items: center; padding: 20px; overflow-y: auto;" 
             x-cloak>
            
            <div @click.away="showModal = false" 
                 style="background: var(--white); border-radius: 24px; padding: 32px; width: 100%; max-width: 540px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); border: 1px solid var(--cream-dark); margin: auto; max-height: 85vh; overflow-y: auto;">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;">Add Delivery Address</h3>
                    <button type="button" @click="showModal = false" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--maroon); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--cream);">&times;</button>
                </div>
                
                <form action="{{ route('account.addresses.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. Jyoshna Patel" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                    </div>
                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Phone Number (UK)</label>
                        <input type="text" name="phone" required placeholder="07700 900123" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                    </div>
                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Address Line 1</label>
                        <input type="text" name="address_line_1" required placeholder="House / Flat #, Street" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                    </div>
                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Address Line 2 (Optional)</label>
                        <input type="text" name="address_line_2" placeholder="Locality / Area" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Town / City</label>
                            <input type="text" name="city" value="Hounslow" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Postcode</label>
                            <input type="text" name="pincode" required placeholder="TW3 2EN" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                        </div>
                    </div>
                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">County / State</label>
                        <input type="text" name="state" value="Greater London" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 4px;">Address Type</label>
                        <select name="address_type" style="width: 100%; padding: 10px 14px; border: 1px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; background: var(--cream);">
                            <option value="home">Home</option>
                            <option value="work">Work</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 10px; border-top: 1px solid var(--cream-dark);">
                        <button type="button" @click="showModal = false" class="btn btn-outline btn-sm" style="padding: 10px 20px;">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 24px;">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

@endsection
