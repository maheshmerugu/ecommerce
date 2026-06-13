<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * GoDaddy shared hosting blocks ALL outgoing SMTP (ports 587 and 465).
 * Switch to Resend (HTTP API) which is never blocked.
 *
 * After this migration runs on production:
 *  1. Sign up at https://resend.com (free: 3,000 emails/month)
 *  2. Add & verify your domain (fourwheels.co.in)
 *  3. Go to Admin → Settings → Email and paste your Resend API key
 *     into the Password field (mailer will already be set to "resend")
 */
return new class extends Migration
{
    public function up(): void
    {
        // Add mailer column if it doesn't exist yet (safety)
        if (!Schema::hasColumn('email_settings', 'mailer')) {
            Schema::table('email_settings', function (Blueprint $table) {
                $table->string('mailer')->default('smtp')->after('id');
            });
        }

        DB::table('email_settings')
            ->where('id', 1)
            ->update(['mailer' => 'resend']);
    }

    public function down(): void
    {
        DB::table('email_settings')
            ->where('id', 1)
            ->update(['mailer' => 'smtp']);
    }
};
