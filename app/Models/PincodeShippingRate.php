<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PincodeShippingRate extends Model
{
    protected $fillable = [
        'pincode',
        'match_type',
        'shipping_charge',
        'label',
        'is_active',
    ];

    protected $casts = [
        'shipping_charge' => 'float',
        'is_active' => 'boolean',
    ];
}
