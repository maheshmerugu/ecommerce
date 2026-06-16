<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EmailSetting extends Model
{
    protected $fillable = [
        'mailer',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_address',
        'from_name',
        'is_active',
    ];

    protected $casts = [
        'port'      => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Encrypt the password before saving to the database.
     */
    public function setPasswordAttribute(?string $value): void
    {
        // Only encrypt if it looks like a plain-text value (not already encrypted).
        if ($value && !$this->isEncrypted($value)) {
            $this->attributes['password'] = Crypt::encryptString($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Decrypt the password when reading.
     */
    public function getPasswordAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return trim(Crypt::decryptString($value));
        } catch (\Exception) {
            // Already plain-text (legacy / initial state)
            return trim($value);
        }
    }

    /**
     * Return a masked version of the password for display.
     */
    public function getMaskedPasswordAttribute(): string
    {
        $plain = $this->password;
        if (!$plain) {
            return '';
        }
        return str_repeat('•', max(0, strlen($plain) - 4)) . substr($plain, -4);
    }

    /**
     * Fetch the single email configuration row (always row id=1).
     */
    public static function current(): static
    {
        return static::firstOrNew(['id' => 1], [
            'mailer'       => 'resend',
            'host'         => null,
            'port'         => null,
            'encryption'   => null,
            'username'     => null,
            'password'     => null,
            'from_address' => null,
            'from_name'    => null,
            'is_active'    => false,
        ]);
    }

    // ─────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────

    private function isEncrypted(string $value): bool
    {
        // Laravel encrypted strings are base64 payloads; plain App Passwords
        // are 16 characters with no spaces. A quick heuristic:
        return strlen($value) > 50 && base64_decode($value, true) !== false;
    }
}
