<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PincodeShippingRate;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'pincode' => 'required|string|regex:/^\d{2,6}$/',
            'match_type' => 'required|in:exact,prefix',
            'shipping_charge' => 'required|numeric|min:0',
            'label' => 'nullable|string|max:100',
        ]);

        if ($data['match_type'] === 'exact' && strlen($data['pincode']) !== 6) {
            return redirect()->back()
                ->withErrors(['pincode' => 'Exact match requires a 6-digit pincode.'])
                ->withInput()
                ->with('active_tab', 'shipping');
        }

        PincodeShippingRate::updateOrCreate(
            ['pincode' => $data['pincode'], 'match_type' => $data['match_type']],
            [
                'shipping_charge' => $data['shipping_charge'],
                'label' => $data['label'],
                'is_active' => true,
            ]
        );

        return redirect()->route('admin.settings.index', ['#shipping'])
            ->with('success', 'Pincode shipping rate saved.')
            ->with('active_tab', 'shipping');
    }

    public function destroy(PincodeShippingRate $shippingRate)
    {
        $shippingRate->delete();

        return redirect()->route('admin.settings.index', ['#shipping'])
            ->with('success', 'Pincode shipping rate removed.')
            ->with('active_tab', 'shipping');
    }
}
