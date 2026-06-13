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
     * Load mail credentials from the email_settings table and override
     * Laravel's runtime config. Supports smtp, resend, and log drivers.
     */
    private function bootMailConfig(): void
    {
        try {
            $cfg = Cache::remember('email_settings:active', 3600, function () {
                return EmailSetting::where('is_active', true)->first();
            });

            if (!$cfg || !$cfg->password) {
                return;
            }

            $mailer = strtolower(trim($cfg->mailer ?: 'smtp'));

            Config::set('mail.default',      $mailer);
            Config::set('mail.from.address', trim($cfg->from_address ?: $cfg->username ?? ''));
            Config::set('mail.from.name',    trim($cfg->from_name    ?: config('app.name')));

            if ($mailer === 'resend') {
                // HTTP API — no SMTP ports, works on GoDaddy / any shared host
                Config::set('resend.api_key', trim($cfg->password));
                return;
            }

            // ── SMTP driver ───────────────────────────────────────────────────
            if (!$cfg->host || !$cfg->username) {
                return;
            }

            // Laravel 11 uses 'scheme' not 'encryption'
            // port 465 SSL → smtps  |  port 587 STARTTLS → null
            $encryption = strtolower(trim($cfg->encryption ?? ''));
            $scheme = match ($encryption) {
                'ssl', 'smtps' => 'smtps',
                default        => null,
            };

            Config::set('mail.mailers.smtp.host',     trim($cfg->host));
            Config::set('mail.mailers.smtp.port',     (int) ($cfg->port ?: 465));
            Config::set('mail.mailers.smtp.scheme',   $scheme);
            Config::set('mail.mailers.smtp.username', trim($cfg->username));
            Config::set('mail.mailers.smtp.password', trim($cfg->password));
        } catch (\Exception) {
            // DB not ready yet — fall back to .env values
        }
    }
}
