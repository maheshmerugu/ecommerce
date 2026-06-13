<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class EmailSettingController extends Controller
{
    public function index()
    {
        $setting = EmailSetting::current();
        return view('admin.email-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mailer'       => 'required|in:smtp,sendmail,log',
            'host'         => 'required_if:mailer,smtp|nullable|string|max:255',
            'port'         => 'required_if:mailer,smtp|nullable|integer',
            'encryption'   => 'nullable|in:tls,ssl,',
            'username'     => 'required_if:mailer,smtp|nullable|string|max:255',
            'password'     => 'nullable|string|max:255',
            'from_address' => 'required|email|max:255',
            'from_name'    => 'required|string|max:255',
            'is_active'    => 'boolean',
        ]);

        $setting = EmailSetting::firstOrNew(['id' => 1]);

        // Keep existing password if field left blank
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $setting->fill($data)->save();

        // Clear cached config so AppServiceProvider picks up new values
        Cache::forget('email_settings:active');

        return back()->with('success', 'Email settings saved successfully.');
    }

    public function test(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        // Clear cache to use latest DB settings
        Cache::forget('email_settings:active');

        try {
            Mail::raw('This is a test email from ' . config('app.name') . '.', function ($msg) use ($request) {
                $msg->to($request->test_email)->subject('SMTP Test — ' . config('app.name'));
            });
            return back()->with('success', 'Test email sent to ' . $request->test_email . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }
}
