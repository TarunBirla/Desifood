<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // 1. Admin User
        $admin = User::firstOrCreate(['email' => 'admin@desifoods.com'], [
            'name' => 'Desi Foods Admin',
            'phone' => '020 8570 8899',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole ? $adminRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 2. Staff User
        $staff = User::firstOrCreate(['email' => 'staff@desifoods.com'], [
            'name' => 'Store Manager',
            'phone' => '020 8570 8890',
            'password' => Hash::make('password123'),
            'role_id' => $staffRole ? $staffRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 3. Demo Customer User
        $customer = User::firstOrCreate(['email' => 'customer@desifoods.com'], [
            'name' => 'Jyoshna Patel',
            'phone' => '07700 900123',
            'password' => Hash::make('password123'),
            'role_id' => $customerRole ? $customerRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create sample addresses for customer
        Address::firstOrCreate(['user_id' => $customer->id, 'name' => 'Jyoshna Patel (Home)'], [
            'phone' => '07700 900123',
            'address_line_1' => '14 Whitton Road',
            'address_line_2' => 'Green Parade',
            'city' => 'Hounslow',
            'state' => 'Greater London',
            'country' => 'United Kingdom',
            'pincode' => 'TW3 2EN',
            'landmark' => 'Near Hounslow Station',
            'address_type' => 'home',
            'is_default' => true,
        ]);
    }
}
