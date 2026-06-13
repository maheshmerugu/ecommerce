<?php

namespace App\Providers;

use App\Models\EmailSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Apply DB-stored mail config on every request
        $this->bootMailConfig();

        // Resolve store name once per request and share with every view
        View::composer('*', function ($view) {
            static $storeName = null;

            if ($storeName === null) {
                try {
                    $storeName = Cache::remember('setting:store_name', 3600, function () {
                        return Setting::where('key', 'store_name')->value('value');
                    }) ?: config('app.name');
                } catch (\Exception $e) {
                    $storeName = config('app.name');
                }
            }

            $view->with('storeName', $storeName);
        });
    }

    /**
     * Load SMTP credentials from the dedicated email_settings table and
     * override Laravel's runtime mail configuration so every Mailable and
     * Mail::raw() call in this request uses the admin-configured settings.
     */
    private function bootMailConfig(): void
    {
        try {
            $cfg = Cache::remember('email_settings:active', 3600, function () {
                return EmailSetting::where('is_active', true)->first();
            });

            if (!$cfg || !$cfg->host || !$cfg->username || !$cfg->password) {
                return;
            }

            // Laravel 11 uses 'scheme' (not 'encryption').
            // port 587 STARTTLS → scheme null  |  port 465 SSL → scheme 'smtps'
            $encryption = strtolower(trim($cfg->encryption ?? ''));
            $scheme = match ($encryption) {
                'ssl', 'smtps' => 'smtps',
                default        => null,   // 'tls' / '' / null → STARTTLS on 587
            };

            Config::set('mail.default', $cfg->mailer ?: 'smtp');
            Config::set('mail.mailers.smtp.host',     trim($cfg->host));
            Config::set('mail.mailers.smtp.port',     (int) ($cfg->port ?: 587));
            Config::set('mail.mailers.smtp.scheme',   $scheme);
            Config::set('mail.mailers.smtp.username', trim($cfg->username));
            Config::set('mail.mailers.smtp.password', trim($cfg->password)); // decrypted & trimmed via accessor
            Config::set('mail.from.address',          trim($cfg->from_address ?: $cfg->username));
            Config::set('mail.from.name',             trim($cfg->from_name    ?: config('app.name')));
        } catch (\Exception) {
            // DB not ready yet (migrations / fresh install) — fall back to .env values
        }
    }
}
