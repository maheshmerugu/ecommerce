<?php

namespace App\Support;

class ResendMailHelper
{
    /**
     * Turn Resend API exceptions into actionable admin messages.
     */
    public static function friendlyError(string $rawMessage, ?string $fromAddress = null): string
    {
        $from = $fromAddress ?: config('mail.from.address', '');

        if (str_contains($rawMessage, 'only send testing emails to your own email address')) {
            if (preg_match('/\(([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})\)/', $rawMessage, $m)) {
                $allowedTo = $m[1];
            } else {
                $allowedTo = 'your Resend account email';
            }

            return 'Resend sandbox mode: you can only send test mail to ' . $allowedTo . ' until fourwheels.co.in is verified. '
                . 'Verify your domain at resend.com/domains, set MAIL_FROM_ADDRESS=support@fourwheels.co.in in .env, run config:clear, then you can email any customer. '
                . 'Temporary workaround: MAIL_FROM_ADDRESS=onboarding@resend.dev and send only to ' . $allowedTo . '.';
        }

        if (str_contains($rawMessage, 'domain is not verified')) {
            return 'The sender domain is not verified on Resend. Add and verify fourwheels.co.in at resend.com/domains, '
                . 'then use MAIL_FROM_ADDRESS=support@fourwheels.co.in (not Gmail). Current from: ' . $from . '.';
        }

        if (str_contains($rawMessage, 'gmail.com domain is not verified')) {
            return 'Cannot send from Gmail. Set MAIL_FROM_ADDRESS to an address on your verified domain '
                . '(e.g. support@fourwheels.co.in) in .env and run php artisan config:clear.';
        }

        return $rawMessage . ($from ? ' (From: ' . $from . ')' : '');
    }
}
