<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    private function mergeGuestCart(Request $request, User $user)
    {
        $sessionId = $request->session()->getId();
        $guestCart = Cart::where('session_id', $sessionId)->first();
        
        if ($guestCart && $guestCart->items->count() > 0) {
            $userCart = Cart::firstOrCreate(['user_id' => $user->id]);
            
            foreach ($guestCart->items as $item) {
                $existingItem = CartItem::where('cart_id', $userCart->id)
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if (!$existingItem) {
                    CartItem::create([
                        'cart_id' => $userCart->id,
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                    ]);
                }
            }

            $guestCart->items()->delete();
            $guestCart->delete();
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            if ($user->status === 'blocked') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been blocked by administrator.']);
            }

            // Merge any guest cart items into user's permanent cart
            $this->mergeGuestCart($request, $user);

            $request->session()->regenerate();

            if ($user->isStaff()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed'
            ],
        ], [
            'password.min' => 'Password minimum 8 characters ka hona zaroori hai.',
            'password.regex' => 'Password me kam se kam 1 Capital letter (A-Z), 1 Small letter (a-z), 1 Number (0-9), aur 1 Special symbol (@, $, !, %, *, #, ?, &) hona zaroori hai.',
            'password.confirmed' => 'Password aur Confirm Password match nahi kar rahe hain.',
        ]);

        $customerRole = Role::where('name', 'customer')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $customerRole ? $customerRole->id : null,
            'status' => 'active',
        ]);

        // Merge any guest cart items into user's permanent cart
        $this->mergeGuestCart($request, $user);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Account created successfully! Welcome to Desi Foods Hounslow.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}
