<?php

use App\Models\EmailSetting;
use App\Support\MailFrom;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!EmailSetting::query()->exists()) {
            return;
        }

        $fallback = MailFrom::resolve(
            null,
            env('MAIL_FROM_ADDRESS'),
            'support@fourwheels.co.in'
        );

        EmailSetting::query()->each(function (EmailSetting $setting) use ($fallback) {
            $from = $setting->from_address;

            if (!$from || MailFrom::isBlocked($from)) {
                $from = $fallback;
            }

            $setting->update([
                'from_address' => $from,
                'username'     => $from,
                'mailer'       => 'resend',
            ]);
        });
    }

    public function down(): void
    {
        // Non-reversible data cleanup.
    }
};
