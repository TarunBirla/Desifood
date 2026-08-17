@extends('layouts.app')

@section('title', 'Sign In | Desi Foods Hounslow')

@section('content')

<div style="max-width: 520px; margin: 30px auto; padding: 0 20px;" x-data="{ showPassword: false }">
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 32px 36px; box-shadow: var(--shadow-md);">
        
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('images/logo.svg') }}" alt="Desi Foods Logo" style="height: 48px; margin-bottom: 8px;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.7rem; color: var(--maroon); margin-bottom: 4px;">Welcome Back</h2>
            <p style="color: var(--charcoal-light); font-size: 0.88rem;">Log in to manage your grocery orders & account</p>
        </div>

        @if(session('error'))
            <div style="background: #FDEDEC; border: 1px solid #FADBD8; color: #78281F; padding: 10px 14px; border-radius: 12px; font-size: 0.85rem; margin-bottom: 16px;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <!-- Email Field -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 4px;">Email Address</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. admin@desifoods.com" 
                           style="width: 100%; padding: 10px 14px 10px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">
                </div>
                @error('email') 
                    <div style="color: #c0392b; font-size: 0.8rem; margin-top: 4px; font-weight: 500;">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                    </div> 
                @enderror
            </div>

            <!-- Password Field with Eye Icon Toggle -->
            <div style="margin-bottom: 16px;">
                <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 4px;">Password</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-lock"></i></span>
                    
                    <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="••••••••" 
                           style="width: 100%; padding: 10px 46px 10px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">

                    <!-- Eye Toggle Button -->
                    <button type="button" @click="showPassword = !showPassword" 
                            style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--saffron-deep); cursor: pointer; font-size: 1rem; padding: 4px;"
                            title="Toggle Password Visibility">
                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password') 
                    <div style="color: #c0392b; font-size: 0.8rem; margin-top: 4px; font-weight: 500;">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                    </div> 
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; font-size: 0.85rem;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--charcoal-light);">
                    <input type="checkbox" name="remember" style="accent-color: var(--maroon);">
                    <span>Remember Me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In to Account
            </button>
        </form>

       

        <div style="text-align: center; margin-top: 18px; font-size: 0.88rem; color: var(--muted);">
            New customer in Hounslow? <a href="{{ route('register') }}" style="color: var(--saffron-deep); font-weight: 700; text-decoration: underline;">Create New Account</a>
        </div>
    </div>
</div>

@endsection
