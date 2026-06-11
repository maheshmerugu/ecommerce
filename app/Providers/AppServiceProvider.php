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

    private function bootMailConfig(): void
    {
        try {
            $host     = Setting::where('key', 'mail_host')->value('value');
            $username = Setting::where('key', 'mail_username')->value('value');
            $password = Setting::where('key', 'mail_password')->value('value');

            if ($host && $username && $password) {
                $port       = Setting::where('key', 'mail_port')->value('value') ?: 587;
                $encryption = Setting::where('key', 'mail_encryption')->value('value') ?: 'tls';
                $fromAddr   = Setting::where('key', 'mail_from_address')->value('value') ?: $username;
                $fromName   = Setting::where('key', 'mail_from_name')->value('value') ?: config('app.name');

                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.host', $host);
                Config::set('mail.mailers.smtp.port', (int) $port);
                Config::set('mail.mailers.smtp.username', $username);
                Config::set('mail.mailers.smtp.password', $password);
                Config::set('mail.mailers.smtp.encryption', $encryption);
                Config::set('mail.from.address', $fromAddr);
                Config::set('mail.from.name', $fromName);
            }
        } catch (\Exception $e) {
            // DB not ready yet (migrations), fall back to .env config
        }
    }
}
