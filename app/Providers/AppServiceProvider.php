<?php

namespace App\Providers;

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
        // Apply dynamic settings from DB once per request
        $this->applyDynamicSettings();

        // Share store name globally with every view
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

    private function applyDynamicSettings(): void
    {
        try {
            $mailHost = Setting::get('mail_host');
            if (!$mailHost) return; // No mail settings saved yet, use .env defaults

            $map = [
                'mail.mailers.smtp.host'       => Setting::get('mail_host'),
                'mail.mailers.smtp.port'       => (int) Setting::get('mail_port', 587),
                'mail.mailers.smtp.username'   => Setting::get('mail_username'),
                'mail.mailers.smtp.password'   => Setting::get('mail_password'),
                'mail.mailers.smtp.encryption' => Setting::get('mail_encryption', 'tls') ?: null,
                'mail.from.address'            => Setting::get('mail_from_address'),
                'mail.from.name'               => Setting::get('mail_from_name'),
                'mail.default'                 => Setting::get('mail_mailer', 'smtp'),
            ];

            foreach ($map as $key => $value) {
                if ($value !== null && $value !== '') {
                    Config::set($key, $value);
                }
            }
        } catch (\Exception $e) {
            // DB not ready yet (e.g. first migration), skip silently
        }
    }
}
