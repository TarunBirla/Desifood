@extends('layouts.app')

@section('title', 'Create Account | Desi Foods Hounslow')

@section('content')

<div style="max-width: 880px; margin: 24px auto; padding: 0 20px;" 
     x-data="{ showPassword: false, showConfirmPassword: false, passwordVal: '' }">
    
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 32px 40px; box-shadow: var(--shadow-md);">
        
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('images/logo.svg') }}" alt="Desi Foods Logo" style="height: 48px; margin-bottom: 8px;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--maroon); margin-bottom: 4px;">Create New Account</h2>
            <p style="color: var(--charcoal-light); font-size: 0.88rem;">Join Desi Foods Hounslow for special offers & fast doorstep express delivery</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <!-- 2-Column Fields Layout (2 Fields Per Line) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px 20px; margin-bottom: 20px;">
                
                <!-- Line 1 - Field 1: Full Name -->
                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 4px;">Full Name</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rahul Sharma" 
                               style="width: 100%; padding: 10px 14px 10px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">
                    </div>
                    @error('name') <div style="color: #c0392b; font-size: 0.78rem; margin-top: 2px;">{{ $message }}</div> @enderror
                </div>

                <!-- Line 1 - Field 2: Email Address -->
                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 4px;">Email Address</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. rahul@example.com" 
                               style="width: 100%; padding: 10px 14px 10px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">
                    </div>
                    @error('email') <div style="color: #c0392b; font-size: 0.78rem; margin-top: 2px;">{{ $message }}</div> @enderror
                </div>

                <!-- Line 2 - Field 1: Phone Number -->
                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 4px;">Phone Number (UK)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-phone"></i></span>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 07700 900123" 
                               style="width: 100%; padding: 10px 14px 10px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">
                    </div>
                    @error('phone') <div style="color: #c0392b; font-size: 0.78rem; margin-top: 2px;">{{ $message }}</div> @enderror
                </div>

                <!-- Line 2 - Field 2: Password with Eye Toggle -->
                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 4px;">Password</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-lock"></i></span>
                        
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="passwordVal" required placeholder="Min 8 chars (A-Z, a-z, 0-9, @#$)" 
                               style="width: 100%; padding: 10px 44px 10px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">

                        <button type="button" @click="showPassword = !showPassword" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--saffron-deep); cursor: pointer; font-size: 0.95rem; padding: 4px;"
                                title="Toggle Password Visibility">
                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password') <div style="color: #c0392b; font-size: 0.78rem; margin-top: 2px;">{{ $message }}</div> @enderror
                </div>

                <!-- Line 3 - Field 1: Confirm Password with Eye Toggle -->
                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 4px;">Confirm Password</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"><i class="fa-solid fa-lock"></i></span>
                        
                        <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required placeholder="Re-enter password" 
                               style="width: 100%; padding: 10px 44px 10px 42px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">

                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--saffron-deep); cursor: pointer; font-size: 0.95rem; padding: 4px;"
                                title="Toggle Password Visibility">
                            <i class="fa-solid" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Line 3 - Field 2: Password Rules Checklist -->
                <div style="background: var(--cream); border: 1px solid var(--cream-dark); border-radius: 12px; padding: 10px 14px; font-size: 0.78rem;">
                    <div style="font-weight: 700; color: var(--maroon); margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-shield-halved" style="color: var(--saffron);"></i> Password Requirements:
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2px 8px; color: var(--charcoal-light);">
                        <span :style="passwordVal.length >= 8 ? 'color: #2E7D32; font-weight: 600;' : 'color: #888;'"><i class="fa-solid" :class="passwordVal.length >= 8 ? 'fa-check' : 'fa-circle-dot'"></i> Min 8 Chars</span>
                        <span :style="/[A-Z]/.test(passwordVal) ? 'color: #2E7D32; font-weight: 600;' : 'color: #888;'"><i class="fa-solid" :class="/[A-Z]/.test(passwordVal) ? 'fa-check' : 'fa-circle-dot'"></i> Capital (A-Z)</span>
                        <span :style="/[a-z]/.test(passwordVal) ? 'color: #2E7D32; font-weight: 600;' : 'color: #888;'"><i class="fa-solid" :class="/[a-z]/.test(passwordVal) ? 'fa-check' : 'fa-circle-dot'"></i> Small (a-z)</span>
                        <span :style="/[0-9]/.test(passwordVal) ? 'color: #2E7D32; font-weight: 600;' : 'color: #888;'"><i class="fa-solid" :class="/[0-9]/.test(passwordVal) ? 'fa-check' : 'fa-circle-dot'"></i> Number (0-9)</span>
                        <span style="grid-column: span 2;" :style="/[@$!%*#?&]/.test(passwordVal) ? 'color: #2E7D32; font-weight: 600;' : 'color: #888;'"><i class="fa-solid" :class="/[@$!%*#?&]/.test(passwordVal) ? 'fa-check' : 'fa-circle-dot'"></i> Symbol (@, $, !, %, *, #, ?, &)</span>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fa-solid fa-user-plus"></i> Create New Account
            </button>
        </form>

        <div style="text-align: center; margin-top: 16px; font-size: 0.88rem; color: var(--muted);">
            Already registered with Desi Foods? <a href="{{ route('login') }}" style="color: var(--saffron-deep); font-weight: 700; text-decoration: underline;">Sign In Here</a>
        </div>
    </div>
</div>

@endsection
