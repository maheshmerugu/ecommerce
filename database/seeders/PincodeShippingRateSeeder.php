<?php

namespace Database\Seeders;

use App\Models\PincodeShippingRate;
use Illuminate\Database\Seeder;

class PincodeShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['pincode' => '560', 'match_type' => 'prefix', 'shipping_charge' => 60, 'label' => 'Bengaluru zone'],
            ['pincode' => '400', 'match_type' => 'prefix', 'shipping_charge' => 60, 'label' => 'Mumbai zone'],
            ['pincode' => '401', 'match_type' => 'prefix', 'shipping_charge' => 60, 'label' => 'Mumbai suburbs'],
            ['pincode' => '110', 'match_type' => 'prefix', 'shipping_charge' => 60, 'label' => 'Delhi NCR'],
            ['pincode' => '600', 'match_type' => 'prefix', 'shipping_charge' => 70, 'label' => 'Chennai zone'],
            ['pincode' => '411', 'match_type' => 'prefix', 'shipping_charge' => 80, 'label' => 'Pune zone'],
            ['pincode' => '500001', 'match_type' => 'exact', 'shipping_charge' => 80, 'label' => 'Hyderabad — Secunderabad'],
            ['pincode' => '570001', 'match_type' => 'exact', 'shipping_charge' => 100, 'label' => 'Mysuru'],
        ];

        foreach ($rates as $rate) {
            PincodeShippingRate::updateOrCreate(
                ['pincode' => $rate['pincode'], 'match_type' => $rate['match_type']],
                [
                    'shipping_charge' => $rate['shipping_charge'],
                    'label' => $rate['label'],
                    'is_active' => true,
                ]
            );
        }
    }
}
