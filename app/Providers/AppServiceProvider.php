<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $cartProductIds = [];
            $wishlistProductIds = [];

            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->first();
                if ($cart) {
                    $cartProductIds = $cart->items()->pluck('product_id')->toArray();
                }
                $wishlistProductIds = Wishlist::where('user_id', Auth::id())->pluck('product_id')->toArray();
            }

            $view->with('userCartProductIds', $cartProductIds);
            $view->with('userWishlistProductIds', $wishlistProductIds);
        });
    }
}
