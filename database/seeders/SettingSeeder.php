<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'store_name', 'value' => 'Desi Foods Hounslow', 'group' => 'general'],
            ['key' => 'store_email', 'value' => 'info@desifoodshounslow.co.uk', 'group' => 'general'],
            ['key' => 'store_phone', 'value' => '020 8570 8899', 'group' => 'general'],
            ['key' => 'store_address', 'value' => '3-4 Green Parade, Whitton Road, Hounslow, Greater London, TW3 2EN', 'group' => 'general'],
            
            // Currency & Tax
            ['key' => 'currency_code', 'value' => 'GBP', 'group' => 'currency'],
            ['key' => 'currency_symbol', 'value' => '£', 'group' => 'currency'],
            ['key' => 'tax_rate_percent', 'value' => '0', 'group' => 'tax'],

            // Payment Gateway
            ['key' => 'razorpay_enabled', 'value' => '1', 'group' => 'payment'],
            ['key' => 'razorpay_key_id', 'value' => 'rzp_test_desifood123', 'group' => 'payment'],
            ['key' => 'razorpay_key_secret', 'value' => 'sample_desifood_secret_456', 'group' => 'payment'],
            ['key' => 'cod_enabled', 'value' => '1', 'group' => 'payment'],

            // SMTP
            ['key' => 'smtp_host', 'value' => 'mail.desifoodshounslow.co.uk', 'group' => 'mail'],
            ['key' => 'smtp_port', 'value' => '465', 'group' => 'mail'],
            ['key' => 'smtp_username', 'value' => 'info@desifoodshounslow.co.uk', 'group' => 'mail'],
            ['key' => 'smtp_encryption', 'value' => 'ssl', 'group' => 'mail'],
        ];

        foreach ($settings as $setting) {
            Setting::set($setting['key'], $setting['value'], $setting['group']);
        }
    }
}
