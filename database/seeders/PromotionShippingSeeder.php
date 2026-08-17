<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class PromotionShippingSeeder extends Seeder
{
    public function run(): void
    {
        // Update or create Coupons
        Coupon::updateOrCreate(['code' => 'DESIFOOD10'], [
            'type' => 'percentage',
            'value' => 10.00, // 10% OFF
            'min_order_amount' => 10.00,
            'max_discount_amount' => 50.00,
            'expires_at' => now()->addMonths(12),
            'usage_limit' => 10000,
            'per_user_limit' => 100,
            'status' => true,
        ]);

        Coupon::updateOrCreate(['code' => 'DESI5'], [
            'type' => 'fixed',
            'value' => 5.00, // Flat £5 OFF
            'min_order_amount' => 25.00,
            'expires_at' => now()->addMonths(12),
            'usage_limit' => 5000,
            'per_user_limit' => 100,
            'status' => true,
        ]);

        // Shipping Methods
        ShippingMethod::updateOrCreate(['name' => 'Standard Doorstep Delivery (Hounslow & UK)'], [
            'description' => 'Delivered to your doorstep in 1 to 2 business days.',
            'cost' => 2.99,
            'free_shipping_threshold' => 30.00, // Free above £30
            'estimated_days' => '1-2 Business Days',
            'is_active' => true,
        ]);

        ShippingMethod::updateOrCreate(['name' => 'Express Same Day / Next Day Priority'], [
            'description' => 'Fast delivery guaranteed within 24 hours.',
            'cost' => 4.99,
            'free_shipping_threshold' => 60.00,
            'estimated_days' => 'Same Day / 24 Hours',
            'is_active' => true,
        ]);

        ShippingMethod::updateOrCreate(['name' => 'Click & Collect (Store Pickup - Whitton Road)'], [
            'description' => 'Pick up your prepared grocery order directly at 3-4 Green Parade.',
            'cost' => 0.00,
            'free_shipping_threshold' => 0.00,
            'estimated_days' => 'Ready in 2 Hours',
            'is_active' => true,
        ]);

        // Shipping Zone
        ShippingZone::updateOrCreate(['name' => 'Greater London & Hounslow Zone'], [
            'pincodes' => ['TW3 2EN', 'TW3', 'TW4', 'TW5', 'TW7', 'UB1', 'UB2', 'W7'],
            'charge' => 0.00,
            'status' => true,
        ]);
    }
}
