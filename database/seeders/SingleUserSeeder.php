<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SingleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create one new admin user
        Admin::create([
            'name' => 'Main Admin',
            'email' => 'mainadmin@example.com',
            'password' => Hash::make('admin123'),
            'is_active' => true,
        ]);

        // Create one new customer user
        Customer::create([
            'first_name' => 'David',
            'last_name' => 'Wilson',
            'email' => 'david@example.com',
            'password' => Hash::make('user123'),
            'phone' => '+1234567800',
            'date_of_birth' => '1990-05-15',
            'gender' => 'male',
            'is_active' => true,
        ]);
    }
}
