<?php

namespace App\Support;

class MailFrom
{
    /** @var list<string> */
    public const BLOCKED_DOMAINS = [
        'gmail.com',
        'googlemail.com',
        'yahoo.com',
        'yahoo.co.in',
        'hotmail.com',
        'outlook.com',
        'live.com',
        'icloud.com',
        'me.com',
        'protonmail.com',
        'zoho.com',
    ];

    public static function domain(string $email): string
    {
        $at = strrchr($email, '@');

        return $at ? strtolower(substr($at, 1)) : '';
    }

    public static function isBlocked(string $email): bool
    {
        return in_array(self::domain($email), self::BLOCKED_DOMAINS, true);
    }

    /**
     * Pick a safe From address from .env, then config default.
     */
    public static function resolve(?string $envFromAddress = null, ?string $configFromAddress = null): string
    {
        $candidates = array_filter([
            $envFromAddress,
            $configFromAddress,
        ], fn ($v) => is_string($v) && trim($v) !== '');

        foreach ($candidates as $candidate) {
            $email = trim($candidate);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            if (!self::isBlocked($email)) {
                return $email;
            }
        }

        return trim((string) ($configFromAddress ?: 'hello@example.com'));
    }
}
