<?php

use App\Helpers\ImageHelper;

if (!function_exists('product_image_url')) {
    /**
     * Generate the correct URL for product images
     */
    function product_image_url($imagePath)
    {
        return ImageHelper::getImageUrl($imagePath);
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format amount according to site currency settings
     */
    function format_currency($amount, $decimals = null)
    {
        $cfg = config('currency', []);

        $symbol = $cfg['symbol'] ?? '₹';
        $position = $cfg['position'] ?? 'left';
        $thousand = $cfg['thousands_separator'] ?? ',';
        $decimal = $cfg['decimal_separator'] ?? '.';

        if ($decimals === null) {
            $decimals = $cfg['decimals'] ?? 0;
        }

        $formatted = number_format((float)$amount, (int)$decimals, $decimal, $thousand);

        return $position === 'left' ? $symbol . $formatted : $formatted . ' ' . $symbol;
    }
}

if (!function_exists('product_image_url_or_default')) {
    /**
     * Get image URL or default placeholder
     */
    function product_image_url_or_default($imagePath, $default = null)
    {
        return ImageHelper::getImageUrlWithFallback($imagePath, $default);
    }
}