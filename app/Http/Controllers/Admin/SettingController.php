<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'razorpay_key_id'      => 'nullable|string|max:100',
            'razorpay_key_secret'  => 'nullable|string|max:100',
            'razorpay_mode'        => 'nullable|in:live,test',
            'shipping_fee'         => 'nullable|numeric|min:0',
            'store_name'           => 'nullable|string|max:255',
            'store_email'          => 'nullable|email|max:255',
            'store_phone'          => 'nullable|string|max:30',
            'store_address'        => 'nullable|string|max:500',
            'currency_symbol'      => 'nullable|string|max:10',
            'free_shipping_above'  => 'nullable|numeric|min:0',
        ]);

        $paymentSettings = [
            'razorpay_key_id',
            'razorpay_key_secret',
            'razorpay_mode',
        ];

        $shippingSettings = [
            'shipping_fee',
            'free_shipping_above',
        ];

        $storeSettings = [
            'store_name',
            'store_email',
            'store_phone',
            'store_address',
            'currency_symbol',
        ];

        foreach ($paymentSettings as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'payment');
            }
        }

        foreach ($shippingSettings as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'shipping');
            }
        }

        foreach ($storeSettings as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'store');
            }
        }

        // Clear settings cache so changes take effect immediately
        Cache::forget('setting:store_name');
        Cache::forget('setting:razorpay_key_id');
        Cache::forget('setting:razorpay_key_secret');
        Cache::forget('setting:razorpay_mode');
        Cache::forget('setting:shipping_fee');
        Cache::forget('setting:free_shipping_above');
        Cache::forget('setting:store_email');
        Cache::forget('setting:store_phone');
        Cache::forget('setting:store_address');
        Cache::forget('setting:currency_symbol');

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }
}
