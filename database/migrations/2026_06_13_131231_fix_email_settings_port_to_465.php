<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * cPanel / shared hosting servers block outgoing port 587 (STARTTLS).
 * Switch the active SMTP row to port 465 (SMTPS / SSL) which hosts allow.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_settings')
            ->where('id', 1)
            ->whereIn('port', [587, 2525, 25])   // only touch rows still on blocked ports
            ->update([
                'port'       => 465,
                'encryption' => 'ssl',
            ]);
    }

    public function down(): void
    {
        DB::table('email_settings')
            ->where('id', 1)
            ->where('port', 465)
            ->update([
                'port'       => 587,
                'encryption' => 'tls',
            ]);
    }
};
