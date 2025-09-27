<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Generate the correct URL for product images
     */
    public static function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return null;
        }

        // Remove any leading slashes
        $imagePath = ltrim($imagePath, '/');
        
        // Get the base URL from config
        $baseUrl = config('app.url');
        
        // Ensure the base URL doesn't end with /public
        $baseUrl = rtrim($baseUrl, '/');
        if (str_ends_with($baseUrl, '/public')) {
            $baseUrl = substr($baseUrl, 0, -7); // Remove '/public'
        }
        
        // Generate the correct storage URL
        return $baseUrl . '/storage/' . $imagePath;
    }

    /**
     * Get image URL or default placeholder
     */
    public static function getImageUrlWithFallback($imagePath, $default = null)
    {
        $url = self::getImageUrl($imagePath);
        
        if ($url) {
            return $url;
        }
        
        // Return a default placeholder image or null
        return $default ?? asset('images/placeholder.jpg');
    }
}