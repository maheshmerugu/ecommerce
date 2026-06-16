<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    public function index()
    {
        $settings     = Setting::all()->pluck('value', 'key')->toArray();
        $emailSetting = EmailSetting::current();

        return view('admin.settings.index', compact('settings', 'emailSetting'));
    }

    // ─────────────────────────────────────────────────
    // Save general / payment / shipping / store settings
    // ─────────────────────────────────────────────────

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

        // Bust relevant caches
        $allKeys = array_merge(...array_values($groups));
        $allKeys[] = 'store_name';
        foreach ($allKeys as $key) {
            Cache::forget("setting:{$key}");
        }

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    // ─────────────────────────────────────────────────
    // Save email_settings row (dedicated table)
    // ─────────────────────────────────────────────────

    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_mailer'       => 'nullable|string|max:20',
            'mail_host'         => 'required|string|max:255',
            'mail_port'         => 'required|integer|in:25,465,587,2525',
            'mail_encryption'   => 'required|in:tls,ssl,none',
            'mail_username'     => 'required|email|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
        ]);

        $emailSetting = EmailSetting::current();

        $emailSetting->fill([
            'mailer'       => $request->input('mail_mailer', 'smtp'),
            'host'         => $request->input('mail_host'),
            'port'         => (int) $request->input('mail_port'),
            'encryption'   => $request->input('mail_encryption'),
            'username'     => $request->input('mail_username'),
            'from_address' => $request->input('mail_from_address') ?: $request->input('mail_username'),
            'from_name'    => $request->input('mail_from_name') ?: Setting::get('store_name') ?: config('app.name'),
        ]);

        // Only update the password if a new one was submitted
        if ($request->filled('mail_password')) {
            $emailSetting->password = $request->input('mail_password');
        }

        // Mark active only when essential fields are present
        $emailSetting->is_active = (bool) ($emailSetting->host && $emailSetting->username && $emailSetting->password);

        $emailSetting->save();

        // Bust the email config cache so AppServiceProvider picks up new values
        Cache::forget('email_settings:active');

        return redirect()->back()->with('success', 'Email settings saved successfully!');
    }

    // ─────────────────────────────────────────────────
    // Send test email using the saved email_settings
    // ─────────────────────────────────────────────────

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            $cfg = EmailSetting::current();

            if (!$cfg->password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email settings are not configured. Please save your Resend API key first.',
                ]);
            }

            // AppServiceProvider already applied DB-driven mail config (Resend).
            // No local Config::set() needed — just send directly.
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
                'message' => $e->getMessage(),
            ]);
        }
    }
}
