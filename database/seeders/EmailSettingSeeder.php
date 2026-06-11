<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use Illuminate\Database\Seeder;

class EmailSettingSeeder extends Seeder
{
    /**
     * Insert the default (unconfigured) Gmail SMTP row.
     * Username and password are left null — the admin fills them in the panel.
     */
    public function run(): void
    {
        EmailSetting::updateOrCreate(
            ['id' => 1],
            [
                'mailer'       => 'smtp',
                'host'         => 'smtp.gmail.com',
                'port'         => 587,
                'encryption'   => 'tls',
                'username'     => null,
                'password'     => null,
                'from_address' => null,
                'from_name'    => null,
                'is_active'    => false,
            ]
        );
    }
}
