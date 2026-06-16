<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Generate the correct public URL for a file stored on the public disk.
     */
    public static function getImageUrl(?string $imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        $relative = self::normalizeStoragePath($imagePath);
        $base     = self::publicBaseUrl();

        return $base . '/storage/' . $relative;
    }

    /**
     * Get image URL or default placeholder.
     */
    public static function getImageUrlWithFallback(?string $imagePath, ?string $default = null): ?string
    {
        return self::getImageUrl($imagePath)
            ?? $default
            ?? self::getImageUrl('images/placeholder.jpg');
    }

    /**
     * Strip legacy / duplicate prefixes from stored paths.
     * e.g. "public/storage/products/x.jpg" → "products/x.jpg"
     */
    public static function normalizeStoragePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        // Remove repeated public/storage prefixes
        while (preg_match('#^(public/)+?(storage/)?#', $path)) {
            $path = preg_replace('#^(public/)+?(storage/)?#', '', $path, 1);
        }

        return $path;
    }

    /**
     * Absolute web root for publicly served assets (the Laravel public/ directory).
     */
    private static function publicBaseUrl(): string
    {
        $base = rtrim((string) (config('app.asset_url') ?: config('app.url')), '/');

        // Avoid trailing /public/public
        $base = preg_replace('#(/public)+$#', '/public', $base);

        // XAMPP subfolder: APP_URL=http://localhost → serve from /ecommerce/public
        if (in_array($base, ['http://localhost', 'http://127.0.0.1'], true)) {
            $base .= '/ecommerce/public';
        }

        return $base;
    }
}
