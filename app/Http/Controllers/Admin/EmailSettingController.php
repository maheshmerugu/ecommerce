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
            'password'     => 'nullable|string|max:255',
            'from_address' => 'required|email|max:255',
            'from_name'    => 'required|string|max:255',
            'is_active'    => 'boolean',
        ]);

        $setting = EmailSetting::firstOrNew(['id' => 1]);

        $setting->fill([
            'mailer'       => 'resend',
            'host'         => null,
            'port'         => null,
            'encryption'   => null,
            'username'     => $data['from_address'],
            'from_address' => $data['from_address'],
            'from_name'    => $data['from_name'],
            'is_active'    => $request->boolean('is_active', true),
        ]);

        if (!empty($data['password'])) {
            $setting->password = $data['password'];
        }

        $setting->save();

        Cache::forget('email_settings:active');

        return back()->with('success', 'Email settings saved successfully.');
    }

    public function test(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        Cache::forget('email_settings:active');

        try {
            Mail::raw('This is a test email from ' . config('app.name') . '.', function ($msg) use ($request) {
                $msg->to($request->test_email)->subject('Resend Test — ' . config('app.name'));
            });
            return back()->with('success', 'Test email sent to ' . $request->test_email . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }
}
