<?php

namespace App\Providers;

use App\Models\Setting;
use App\Support\MailFrom;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
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
     * All outbound mail uses Resend via .env — welcome, password reset, admin test, etc.
     */
    private function bootMailConfig(): void
    {
        $fromAddress = MailFrom::resolve(
            env('MAIL_FROM_ADDRESS'),
            config('mail.from.address')
        );
        $fromName = trim((string) env('MAIL_FROM_NAME', config('mail.from.name')));
        $mailer = trim((string) env('MAIL_MAILER', 'resend'));
        $apiKey = trim((string) env('RESEND_API_KEY', env('RESEND_KEY', '')));

        Config::set('mail.default', $mailer !== '' ? $mailer : 'resend');
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);

        if ($apiKey !== '') {
            Config::set('services.resend.key', $apiKey);
        }

        Mail::alwaysFrom($fromAddress, $fromName);
    }
}
