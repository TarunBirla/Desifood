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

        // 1. Admin Users
        User::updateOrCreate(['email' => 'admin@desifoods.com'], [
            'name' => 'Desi Foods Admin',
            'phone' => '020 8570 8899',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole ? $adminRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'admin@eccommers.com'], [
            'name' => 'Eccommers Admin',
            'phone' => '020 8570 8898',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole ? $adminRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 2. Staff Users
        User::updateOrCreate(['email' => 'staff@desifoods.com'], [
            'name' => 'Store Manager',
            'phone' => '020 8570 8890',
            'password' => Hash::make('password123'),
            'role_id' => $staffRole ? $staffRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'staff@eccommers.com'], [
            'name' => 'Eccommers Staff',
            'phone' => '020 8570 8891',
            'password' => Hash::make('password123'),
            'role_id' => $staffRole ? $staffRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 3. Customer Users
        $customer1 = User::updateOrCreate(['email' => 'customer@desifoods.com'], [
            'name' => 'Jyoshna Patel',
            'phone' => '07700 900123',
            'password' => Hash::make('password123'),
            'role_id' => $customerRole ? $customerRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $customer2 = User::updateOrCreate(['email' => 'customer@eccommers.com'], [
            'name' => 'Jyoshna Patel',
            'phone' => '07700 900456',
            'password' => Hash::make('password123'),
            'role_id' => $customerRole ? $customerRole->id : null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Sample address
        Address::firstOrCreate(['user_id' => $customer1->id, 'name' => 'Jyoshna Patel (Home)'], [
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
