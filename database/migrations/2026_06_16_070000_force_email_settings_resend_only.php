<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Force Resend mailer — admin form previously reset mailer back to smtp on every save.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_settings')
            ->where('id', 1)
            ->update([
                'mailer'     => 'resend',
                'host'       => null,
                'port'       => null,
                'encryption' => null,
            ]);
    }

    public function down(): void
    {
        // no-op
    }
};
