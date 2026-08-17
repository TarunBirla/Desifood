@extends('layouts.app')

@section('title', 'Create Account | Desi Foods Hounslow')

@section('content')

<div style="max-width: 500px; margin: 60px auto; padding: 0 24px;" x-data="{ showPassword: false, showConfirmPassword: false }">
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 40px; box-shadow: var(--shadow-md);">
        
        <div style="text-align: center; margin-bottom: 28px;">
            <img src="{{ asset('images/logo.svg') }}" alt="Desi Foods Logo" style="height: 52px; margin-bottom: 12px;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--maroon); margin-bottom: 6px;">Create New Account</h2>
            <p style="color: var(--charcoal-light); font-size: 0.9rem;">Join Desi Foods for special offers & fast order tracking</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <!-- Full Name -->
            <div style="margin-bottom: 18px;">
                <label style="font-weight: 600; font-size: 0.88rem; color: var(--maroon); display: block; margin-bottom: 6px;">Full Name</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-user"></i></span>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rahul Sharma" 
                           style="width: 100%; padding: 12px 14px 12px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none; background: var(--cream);">
                </div>
                @error('name') <div style="color: #c0392b; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <!-- Email Address -->
            <div style="margin-bottom: 18px;">
                <label style="font-weight: 600; font-size: 0.88rem; color: var(--maroon); display: block; margin-bottom: 6px;">Email Address</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. rahul@example.com" 
                           style="width: 100%; padding: 12px 14px 12px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none; background: var(--cream);">
                </div>
                @error('email') <div style="color: #c0392b; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <!-- Phone Number -->
            <div style="margin-bottom: 18px;">
                <label style="font-weight: 600; font-size: 0.88rem; color: var(--maroon); display: block; margin-bottom: 6px;">Phone Number (UK)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-phone"></i></span>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 07700 900123" 
                           style="width: 100%; padding: 12px 14px 12px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none; background: var(--cream);">
                </div>
                @error('phone') <div style="color: #c0392b; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <!-- Password Field with Eye Toggle -->
            <div style="margin-bottom: 18px;">
                <label style="font-weight: 600; font-size: 0.88rem; color: var(--maroon); display: block; margin-bottom: 6px;">Password</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-lock"></i></span>
                    
                    <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="Minimum 8 characters" 
                           style="width: 100%; padding: 12px 46px 12px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none; background: var(--cream);">

                    <button type="button" @click="showPassword = !showPassword" 
                            style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--saffron-deep); cursor: pointer; font-size: 1rem; padding: 4px;"
                            title="Toggle Password Visibility">
                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password') <div style="color: #c0392b; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <!-- Confirm Password Field with Eye Toggle -->
            <div style="margin-bottom: 24px;">
                <label style="font-weight: 600; font-size: 0.88rem; color: var(--maroon); display: block; margin-bottom: 6px;">Confirm Password</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-lock"></i></span>
                    
                    <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required placeholder="Re-enter password" 
                           style="width: 100%; padding: 12px 46px 12px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none; background: var(--cream);">

                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" 
                            style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--saffron-deep); cursor: pointer; font-size: 1rem; padding: 4px;"
                            title="Toggle Password Visibility">
                        <i class="fa-solid" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fa-solid fa-user-plus"></i> Create Account
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--muted);">
            Already registered? <a href="{{ route('login') }}" style="color: var(--saffron-deep); font-weight: 700; text-decoration: underline;">Sign In Here</a>
        </div>
    </div>
</div>

@endsection
