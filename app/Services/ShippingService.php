<?php

namespace App\Services;

use App\Models\PincodeShippingRate;
use App\Models\Setting;

class ShippingService
{
    public static function defaultFee(): float
    {
        return (float) (Setting::get('shipping_fee') ?: config('shop.shipping_fee', 150));
    }

    /**
     * Calculate shipping charge for a pincode and cart subtotal.
     *
     * @return array{charge: float, is_free: bool, label: string|null, pincode: string}
     */
    public static function calculate(string $pincode, float $subtotal = 0): array
    {
        $pincode = preg_replace('/\D/', '', $pincode);

        if (strlen($pincode) !== 6) {
            return [
                'charge' => self::defaultFee(),
                'is_free' => false,
                'label' => 'Default rate',
                'pincode' => $pincode,
            ];
        }

        $freeAbove = Setting::get('free_shipping_above');
        if ($freeAbove !== null && $freeAbove !== '' && $subtotal >= (float) $freeAbove) {
            return [
                'charge' => 0.0,
                'is_free' => true,
                'label' => 'Free shipping',
                'pincode' => $pincode,
            ];
        }

        $exact = PincodeShippingRate::query()
            ->where('match_type', 'exact')
            ->where('pincode', $pincode)
            ->where('is_active', true)
            ->first();

        if ($exact) {
            return [
                'charge' => (float) $exact->shipping_charge,
                'is_free' => false,
                'label' => $exact->label,
                'pincode' => $pincode,
            ];
        }

        $prefixRates = PincodeShippingRate::query()
            ->where('match_type', 'prefix')
            ->where('is_active', true)
            ->orderByRaw('LENGTH(pincode) DESC')
            ->get();

        foreach ($prefixRates as $rate) {
            if (str_starts_with($pincode, $rate->pincode)) {
                return [
                    'charge' => (float) $rate->shipping_charge,
                    'is_free' => false,
                    'label' => $rate->label,
                    'pincode' => $pincode,
                ];
            }
        }

        return [
            'charge' => self::defaultFee(),
            'is_free' => false,
            'label' => 'Default rate',
            'pincode' => $pincode,
        ];
    }
}
