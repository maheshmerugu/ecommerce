<?php

namespace App\Providers;

use App\Models\EmailSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->bootMailConfig();

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
     * Load mail config — always use Resend (HTTP API) on shared hosting.
     * GoDaddy blocks SMTP ports 587 and 465, so SMTP must never be used in production.
     */
    private function bootMailConfig(): void
    {
        try {
            if (!Schema::hasTable('email_settings')) {
                return;
            }

            $fromAddress = trim((string) env('MAIL_FROM_ADDRESS', config('mail.from.address')));
            $fromName    = trim((string) env('MAIL_FROM_NAME', config('mail.from.name')));

            $cfg = Cache::remember('email_settings:active', 3600, function () {
                return EmailSetting::where('is_active', true)->first();
            });

            if ($cfg) {
                if ($cfg->from_address) {
                    $fromAddress = trim($cfg->from_address);
                } elseif ($cfg->username) {
                    $fromAddress = trim($cfg->username);
                }
                if ($cfg->from_name) {
                    $fromName = trim($cfg->from_name);
                }
            }

            // Priority: .env RESEND_API_KEY → DB password (re_…)
            $apiKey = trim((string) env('RESEND_API_KEY', env('RESEND_KEY', '')));
            if ($apiKey === '' && $cfg && $cfg->password) {
                $dbKey = trim($cfg->password);
                if (str_starts_with($dbKey, 're_')) {
                    $apiKey = $dbKey;
                }
            }

            if ($apiKey !== '') {
                Config::set('mail.default', 'resend');
                Config::set('services.resend.key', $apiKey);
                Config::set('mail.from.address', $fromAddress);
                Config::set('mail.from.name', $fromName);
                return;
            }

            // No API key — use log driver to avoid SMTP timeout errors on shared hosting
            if (!app()->environment('local')) {
                Config::set('mail.default', 'log');
            }
        } catch (\Exception) {
            // DB not ready — fall back to .env values
        }
    }
}
