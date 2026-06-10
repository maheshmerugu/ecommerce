<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primary test account
        Customer::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'phone' => null,
                'is_active' => true,
            ]
        );

        // Additional sample accounts
        Customer::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'date_of_birth' => '1990-01-01',
                'gender' => 'male',
                'is_active' => true,
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567891',
                'date_of_birth' => '1992-05-15',
                'gender' => 'female',
                'is_active' => true,
            ]
        );
    }
}
