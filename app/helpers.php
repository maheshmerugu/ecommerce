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

if (!function_exists('product_image_url_or_default')) {
    /**
     * Get image URL or default placeholder
     */
    function product_image_url_or_default($imagePath, $default = null)
    {
        return ImageHelper::getImageUrlWithFallback($imagePath, $default);
    }
}