<?php

use App\Support\MailFrom;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('email_settings')) {
            return;
        }

        $fallback = MailFrom::resolve(
            env('MAIL_FROM_ADDRESS'),
            'support@fourwheels.co.in'
        );

        foreach (DB::table('email_settings')->orderBy('id')->get() as $setting) {
            $from = $setting->from_address;

            if (!$from || MailFrom::isBlocked($from)) {
                $from = $fallback;
            }

            DB::table('email_settings')->where('id', $setting->id)->update([
                'from_address' => $from,
                'username'     => $from,
                'mailer'       => 'resend',
                'updated_at'   => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Non-reversible data cleanup.
    }
};
