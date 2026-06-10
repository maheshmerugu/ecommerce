<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\City;
use App\Models\Pincode;

class StateCityPincodeSeeder extends Seeder
{
    public function run()
    {
        // Ensure common Indian states exist
        $states = [
            'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Delhi','Goa','Gujarat','Haryana','Himachal Pradesh','Jammu and Kashmir','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Orissa','Punjab','Rajasthan','Sikkim','Tamil Nadu','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Other'
        ];

        foreach ($states as $s) {
            State::firstOrCreate(['name' => $s]);
        }

        // Sample city/pincode data
        $data = [
            'Karnataka' => [
                'Bengaluru' => ['560001','560002','560003'],
                'Mysuru' => ['570001','570002']
            ],
            'Maharashtra' => [
                'Mumbai' => ['400001','400002','400003'],
                'Pune' => ['411001','411002']
            ],
            'Delhi' => [
                'New Delhi' => ['110001','110002']
            ],
            'Tamil Nadu' => [
                'Chennai' => ['600001','600002']
            ]
        ];

        foreach ($data as $stateName => $cities) {
            $state = State::firstOrCreate(['name' => $stateName]);
            foreach ($cities as $cityName => $pincodes) {
                $city = City::firstOrCreate(['state_id' => $state->id, 'name' => $cityName]);
                foreach ($pincodes as $pc) {
                    Pincode::firstOrCreate(['city_id' => $city->id, 'pincode' => $pc]);
                }
            }
        }
    }
}
