@extends('layouts.app')

@section('title', 'My Profile | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1320px; margin: 40px auto; padding: 0 24px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 32px;">Profile Details</h1>

    <div class="catalog-layout">
        <!-- Account Sidebar Navigation -->
        <div class="account-nav-wrapper">
            <a href="{{ route('account.dashboard') }}" class="account-nav-pill">
                <i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('account.orders') }}" class="account-nav-pill">
                <i class="fa-solid fa-box"></i> <span>My Grocery Orders</span>
            </a>
            <a href="{{ route('account.recurring') }}" class="account-nav-pill">
                <i class="fa-solid fa-repeat"></i> <span>Next-Month Orders</span>
            </a>
            <a href="{{ route('account.profile') }}" class="account-nav-pill active">
                <i class="fa-solid fa-user-gear"></i> <span>Profile Details</span>
            </a>
            <a href="{{ route('account.wishlist') }}" class="account-nav-pill">
                <i class="fa-solid fa-heart"></i> <span>Wishlist</span>
            </a>
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
