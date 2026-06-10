<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

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
            'free_shipping_above'  => 'nullable|numeric|min:0',
            'store_name'           => 'nullable|string|max:255',
            'store_email'          => 'nullable|email|max:255',
            'store_phone'          => 'nullable|string|max:30',
            'store_address'        => 'nullable|string|max:500',
            'currency_symbol'      => 'nullable|string|max:10',
            'mail_mailer'          => 'nullable|in:smtp,sendmail,log',
            'mail_host'            => 'nullable|string|max:255',
            'mail_port'            => 'nullable|integer',
            'mail_username'        => 'nullable|string|max:255',
            'mail_password'        => 'nullable|string|max:255',
            'mail_encryption'      => 'nullable|in:tls,ssl,starttls,',
            'mail_from_address'    => 'nullable|email|max:255',
            'mail_from_name'       => 'nullable|string|max:255',
        ]);

        $groups = [
            'payment'  => ['razorpay_key_id', 'razorpay_key_secret', 'razorpay_mode'],
            'shipping' => ['shipping_fee', 'free_shipping_above'],
            'store'    => ['store_name', 'store_email', 'store_phone', 'store_address', 'currency_symbol'],
            'mail'     => ['mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'],
        ];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                if ($request->has($key)) {
                    Setting::set($key, $request->input($key), $group);
                }
            }
        }

        // Clear all relevant caches
        foreach (array_merge(...array_values($groups)) as $key) {
            Cache::forget("setting:{$key}");
        }
        Cache::forget('setting:store_name');

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $this->applyMailConfig();

            Mail::raw('This is a test email from ' . (Setting::get('store_name') ?: config('app.name')) . '. Your email settings are working correctly!', function ($message) use ($request) {
                $fromAddress = Setting::get('mail_from_address') ?: config('mail.from.address', 'noreply@example.com');
                $fromName    = Setting::get('mail_from_name')    ?: config('mail.from.name', config('app.name'));

                $message->to($request->test_email)
                        ->from($fromAddress, $fromName)
                        ->subject('Test Email — ' . (Setting::get('store_name') ?: config('app.name')));
            });

            return response()->json(['success' => true, 'message' => 'Test email sent to ' . $request->test_email]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()]);
        }
    }

    private function applyMailConfig(): void
    {
        $mailer     = Setting::get('mail_mailer', 'smtp');
        $host       = Setting::get('mail_host');
        $port       = Setting::get('mail_port');
        $username   = Setting::get('mail_username');
        $password   = Setting::get('mail_password');
        $encryption = Setting::get('mail_encryption', 'tls');
        $fromAddr   = Setting::get('mail_from_address');
        $fromName   = Setting::get('mail_from_name');

        if ($host) Config::set('mail.mailers.smtp.host', $host);
        if ($port) Config::set('mail.mailers.smtp.port', (int) $port);
        if ($username) Config::set('mail.mailers.smtp.username', $username);
        if ($password) Config::set('mail.mailers.smtp.password', $password);
        if ($encryption !== null) Config::set('mail.mailers.smtp.encryption', $encryption ?: null);
        if ($fromAddr) Config::set('mail.from.address', $fromAddr);
        if ($fromName) Config::set('mail.from.name', $fromName);
        if ($mailer) Config::set('mail.default', $mailer);
    }
}
