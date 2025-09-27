<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdditionalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create additional admin users
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        Admin::create([
            'name' => 'Manager Admin',
            'email' => 'manager@example.com',
            'password' => Hash::make('manager123'),
            'is_active' => true,
        ]);

        Admin::create([
            'name' => 'Staff Admin',
            'email' => 'staff@example.com',
            'password' => Hash::make('staff123'),
            'is_active' => true,
        ]);

        // Create additional customer users
        Customer::create([
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'email' => 'alice@example.com',
            'password' => Hash::make('customer123'),
            'phone' => '+1234567892',
            'date_of_birth' => '1988-03-22',
            'gender' => 'female',
            'is_active' => true,
        ]);

        Customer::create([
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'bob@example.com',
            'password' => Hash::make('customer123'),
            'phone' => '+1234567893',
            'date_of_birth' => '1985-07-10',
            'gender' => 'male',
            'is_active' => true,
        ]);

        Customer::create([
            'first_name' => 'Emily',
            'last_name' => 'Davis',
            'email' => 'emily@example.com',
            'password' => Hash::make('customer123'),
            'phone' => '+1234567894',
            'date_of_birth' => '1993-11-28',
            'gender' => 'female',
            'is_active' => true,
        ]);

        Customer::create([
            'first_name' => 'Michael',
            'last_name' => 'Brown',
            'email' => 'michael@example.com',
            'password' => Hash::make('customer123'),
            'phone' => '+1234567895',
            'date_of_birth' => '1987-09-14',
            'gender' => 'male',
            'is_active' => true,
        ]);

        Customer::create([
            'first_name' => 'Sarah',
            'last_name' => 'Miller',
            'email' => 'sarah@example.com',
            'password' => Hash::make('customer123'),
            'phone' => '+1234567896',
            'date_of_birth' => '1991-12-05',
            'gender' => 'female',
            'is_active' => true,
        ]);
    }
}
