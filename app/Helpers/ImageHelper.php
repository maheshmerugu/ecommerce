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

        return rtrim(self::publicBaseUrl(), '/') . '/storage/' . $relative;
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
     */
    public static function normalizeStoragePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        while (preg_match('#^(public/)+?(storage/)?#', $path)) {
            $path = preg_replace('#^(public/)+?(storage/)?#', '', $path, 1);
        }

        return $path;
    }

    /**
     * Web-accessible base URL for the Laravel public/ directory.
     */
    public static function publicBaseUrl(): string
    {
        if ($assetUrl = config('app.asset_url')) {
            return rtrim($assetUrl, '/');
        }

        $base = rtrim((string) config('app.url'), '/');
        $base = preg_replace('#(/public)+$#', '/public', $base);

        // Local XAMPP: project under /ecommerce/public
        if (in_array($base, ['http://localhost', 'http://127.0.0.1'], true)) {
            return $base . '/ecommerce/public';
        }

        // Production cPanel: repo root is docroot (index.php in project root).
        // Files live in public/storage — URLs stay as /storage/... (see root .htaccess rewrite).
        return $base;
    }
}
