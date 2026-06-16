<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PincodeShippingRate;
use App\Models\Setting;
use App\Support\ResendMailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $shippingRates = PincodeShippingRate::orderBy('match_type')->orderBy('pincode')->get();

        $mailConfigured = (bool) config('services.resend.key');
        $mailFromAddress = config('mail.from.address');
        $mailFromName = config('mail.from.name');

        return view('admin.settings.index', compact(
            'settings',
            'shippingRates',
            'mailConfigured',
            'mailFromAddress',
            'mailFromName'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'razorpay_key_id'     => 'nullable|string|max:100',
            'razorpay_key_secret' => 'nullable|string|max:100',
            'razorpay_mode'       => 'nullable|in:live,test',
            'shipping_fee'        => 'nullable|numeric|min:0',
            'free_shipping_above' => 'nullable|numeric|min:0',
            'store_name'          => 'nullable|string|max:255',
            'store_email'         => 'nullable|email|max:255',
            'store_phone'         => 'nullable|string|max:30',
            'store_address'       => 'nullable|string|max:500',
            'currency_symbol'     => 'nullable|string|max:10',
        ]);

        $groups = [
            'payment'  => ['razorpay_key_id', 'razorpay_key_secret', 'razorpay_mode'],
            'shipping' => ['shipping_fee', 'free_shipping_above'],
            'store'    => ['store_name', 'store_email', 'store_phone', 'store_address', 'currency_symbol'],
        ];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                if ($request->has($key)) {
                    Setting::set($key, $request->input($key), $group);
                }
            }
        }

        $allKeys = array_merge(...array_values($groups));
        $allKeys[] = 'store_name';
        foreach ($allKeys as $key) {
            Cache::forget("setting:{$key}");
        }

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        if (!config('services.resend.key')) {
            return response()->json([
                'success' => false,
                'message' => 'RESEND_API_KEY is not set in .env. Add your key, set MAIL_MAILER=resend, then run php artisan config:clear.',
            ]);
        }

        if (config('mail.default') !== 'resend') {
            return response()->json([
                'success' => false,
                'message' => 'MAIL_MAILER must be "resend" in .env (current: ' . config('mail.default') . ').',
            ]);
        }

        try {
            $storeName = Setting::get('store_name') ?: config('app.name');

            Mail::raw(
                "This is a test email from {$storeName}.\n\nYour email configuration is working correctly!",
                function ($message) use ($request, $storeName) {
                    $message->to($request->test_email)
                            ->subject("Test Email — {$storeName}");
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Test email sent to ' . $request->test_email . ' successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => ResendMailHelper::friendlyError($e->getMessage()),
            ]);
        }
    }
}
