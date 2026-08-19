<?php

use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartWishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductCatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage & Catalog
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');
Route::get('/categories', [ProductCatalogController::class, 'allCategories'])->name('categories.index');
Route::get('/products', [ProductCatalogController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductCatalogController::class, 'liveSearch'])->name('products.search');
Route::get('/products/{slug}', [ProductCatalogController::class, 'show'])->name('products.show');

// Blog Routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Cart Routes
Route::get('/cart', [CartWishlistController::class, 'viewCart'])->name('cart.index');
Route::post('/cart/add', [CartWishlistController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update/{id}', [CartWishlistController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartWishlistController::class, 'removeCartItem'])->name('cart.remove');
Route::post('/cart/recurring-toggle', [CartWishlistController::class, 'toggleRecurringPreference'])->name('cart.recurring.toggle');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer Protected Routes
Route::middleware(['auth'])->group(function () {
    // Checkout Flow
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/coupon/verify', [CheckoutController::class, 'verifyCoupon'])->name('checkout.coupon.verify');
    Route::post('/checkout/place', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
    Route::post('/checkout/payment/verify', [CheckoutController::class, 'verifyPayment'])->name('checkout.payment.verify');
    Route::get('/checkout/confirmation/{order_number}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // Customer Account & History
    Route::get('/account', [CustomerAccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/account/orders', [CustomerAccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{order_number}', [CustomerAccountController::class, 'orderDetails'])->name('account.orders.details');
    Route::post('/account/orders/{order_number}/repeat', [CustomerAccountController::class, 'repeatOrder'])->name('account.orders.repeat');
    Route::get('/account/recurring', [CustomerAccountController::class, 'recurringOrders'])->name('account.recurring');
    Route::post('/account/recurring/{id}/update', [CustomerAccountController::class, 'updateRecurringOrder'])->name('account.recurring.update');
    Route::delete('/account/recurring/{id}', [CustomerAccountController::class, 'deleteRecurringOrder'])->name('account.recurring.delete');
    Route::post('/account/recurring/checkout', [CustomerAccountController::class, 'checkoutRecurringOrder'])->name('account.recurring.checkout');
    Route::post('/account/orders/{order_number}/return', [CustomerAccountController::class, 'requestReturn'])->name('account.orders.return');
    Route::get('/account/invoice/{order_number}', [CustomerAccountController::class, 'downloadInvoice'])->name('account.invoice.download');
    Route::get('/account/profile', [CustomerAccountController::class, 'profile'])->name('account.profile');
    Route::post('/account/profile', [CustomerAccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::get('/account/addresses', [CustomerAccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/account/addresses', [CustomerAccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::post('/account/addresses/{id}/delete', [CustomerAccountController::class, 'deleteAddress'])->name('account.addresses.delete');
    Route::get('/account/wishlist', [CartWishlistController::class, 'viewWishlist'])->name('account.wishlist');
    Route::post('/account/wishlist/toggle', [CartWishlistController::class, 'toggleWishlist'])->name('account.wishlist.toggle');
    Route::post('/account/review', [CustomerAccountController::class, 'submitReview'])->name('account.review.submit');
});

// Public Content & CMS Pages
Route::get('/faqs', [\App\Http\Controllers\PageController::class, 'faqs'])->name('faqs');
Route::get('/terms-and-conditions', [\App\Http\Controllers\PageController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [\App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');

// Admin Protected Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminDashboardController::class, 'updateProfile'])->name('profile.update');

    // Admin Products & Inventory
    Route::resource('products', AdminProductController::class);
    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');

    // Admin Orders & Status Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Admin Reviews Moderation
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/update-status', [AdminReviewController::class, 'updateStatus'])->name('reviews.update-status');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Admin Coupons & Discounts
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    // Admin Customers, Subscribers & Settings
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers/{id}/block', [AdminCustomerController::class, 'toggleBlock'])->name('customers.block');
    Route::post('/customers/{id}/send-notification', [AdminCustomerController::class, 'sendNotification'])->name('customers.notify');

    Route::get('/subscribers', [AdminCustomerController::class, 'subscribers'])->name('subscribers.index');
    Route::delete('/subscribers/{id}', [AdminCustomerController::class, 'deleteSubscriber'])->name('subscribers.destroy');

    // Admin FAQs & CMS Pages
    Route::get('/faqs', [\App\Http\Controllers\Admin\AdminFaqController::class, 'index'])->name('faqs.index');
    Route::post('/faqs', [\App\Http\Controllers\Admin\AdminFaqController::class, 'store'])->name('faqs.store');
    Route::put('/faqs/{id}', [\App\Http\Controllers\Admin\AdminFaqController::class, 'update'])->name('faqs.update');
    Route::delete('/faqs/{id}', [\App\Http\Controllers\Admin\AdminFaqController::class, 'destroy'])->name('faqs.destroy');

    Route::get('/pages', [\App\Http\Controllers\Admin\AdminPageController::class, 'index'])->name('pages.index');
    Route::post('/pages', [\App\Http\Controllers\Admin\AdminPageController::class, 'update'])->name('pages.update');

    // Admin Customer Testimonials Management
    Route::resource('testimonials', \App\Http\Controllers\Admin\AdminTestimonialController::class);

    // Admin Notification Center
    Route::get('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'destroy'])->name('notifications.destroy');

    // Admin Reports & Analytics Suite
    Route::get('/reports', [\App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/top-products', [\App\Http\Controllers\Admin\AdminReportController::class, 'topProducts'])->name('reports.top-products');
    Route::get('/reports/top-customers', [\App\Http\Controllers\Admin\AdminReportController::class, 'topCustomers'])->name('reports.top-customers');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\AdminReportController::class, 'exportCsv'])->name('reports.export');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Admin Blogs
    Route::resource('blogs', AdminBlogController::class);
});
