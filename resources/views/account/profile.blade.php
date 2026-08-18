@extends('layouts.app')

@section('title', 'My Profile | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">Profile Details</h1>

    <div class="catalog-layout">
        <!-- Sidebar -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 20px; height: fit-content; box-shadow: var(--shadow-sm);">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 6px;" class="account-sidebar-menu">
                <li><a href="{{ route('account.dashboard') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Dashboard</a></li>
                <li><a href="{{ route('account.orders') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">My Grocery Orders</a></li>
                <li><a href="{{ route('account.recurring') }}" style="display: block; padding: 12px 16px; color: var(--saffron-deep); font-weight: 600;"><i class="fa-solid fa-repeat me-1"></i> Next-Month Orders</a></li>
                <li><a href="{{ route('account.profile') }}" style="display: block; padding: 12px 16px; font-weight: 700; color: var(--maroon); background: rgba(137, 15, 20, 0.08); border-radius: 12px;">Profile Details</a></li>
                <li><a href="{{ route('account.wishlist') }}" style="display: block; padding: 12px 16px; color: var(--charcoal-light); font-weight: 500;">Wishlist</a></li>
            </ul>
        </div>

        <!-- Form -->
        <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 32px; box-shadow: var(--shadow-sm);">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin-bottom: 24px;">Personal Information</h3>

            <form action="{{ route('account.profile.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled style="background: var(--cream);">
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 07700 900123">
                </div>

                <hr style="border: none; border-top: 1px solid var(--cream-dark); margin: 28px 0;">

                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin-bottom: 24px;">Change Password</h3>

                <div class="form-group">
                    <label class="form-label">New Password (leave blank if keeping current)</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Save Profile Changes</button>
            </form>
        </div>
    </div>
</div>

@endsection
