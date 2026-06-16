<?php

namespace App\Http\Controllers;

use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function calculate(Request $request)
    {
        $request->validate([
            'pincode' => 'required|string|max:10',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        $result = ShippingService::calculate(
            $request->input('pincode'),
            (float) $request->input('subtotal', 0)
        );

        return response()->json([
            'success' => true,
            'shipping_charge' => $result['charge'],
            'is_free' => $result['is_free'],
            'label' => $result['label'],
            'pincode' => $result['pincode'],
            'formatted_charge' => format_currency($result['charge']),
        ]);
    }
}
