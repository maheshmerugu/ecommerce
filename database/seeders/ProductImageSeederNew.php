<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductImageSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing images first
        ProductImage::truncate();
        Storage::disk('public')->deleteDirectory('products');
        Storage::disk('public')->makeDirectory('products');
        
        // Get all products
        $products = Product::all();
        
        $colors = [
            '#1E40AF', '#DC2626', '#059669', '#7C3AED', '#1F2937',
            '#D97706', '#EC4899', '#6B7280', '#0891B2', '#F59E0B'
        ];
        
        foreach ($products as $index => $product) {
            $color = $colors[$index % count($colors)];
            $productSlug = $product->slug;
            
            // Create 4 images for each product
            for ($i = 1; $i <= 4; $i++) {
                $filename = $productSlug . '-' . $i . '.svg';
                $imagePath = "products/{$filename}";
                
                // Create the SVG content
                $svgContent = $this->createPlaceholderImage(
                    $product->name . ($i > 1 ? " - View {$i}" : ''),
                    $color,
                    $i
                );
                
                // Save the image file
                Storage::disk('public')->put($imagePath, $svgContent);
                
                // Create the ProductImage record
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'alt_text' => $product->name . ' - Image ' . $i,
                    'is_main' => $i === 1,
                    'sort_order' => $i
                ]);
                
                // Update product's main image field with the first image
                if ($i === 1) {
                    $product->update(['image' => $imagePath]);
                }
            }
            
            $this->command->info("Created images for product: {$product->name}");
        }
    }

    /**
     * Create a placeholder SVG image with text and color.
     */
    private function createPlaceholderImage(string $text, string $color, int $variant = 1): string
    {
        // Create different visual variants for each image
        $patterns = [
            1 => '<circle cx="400" cy="300" r="100" fill="rgba(255,255,255,0.1)"/>',
            2 => '<rect x="200" y="200" width="400" height="200" fill="rgba(255,255,255,0.1)" rx="20"/>',
            3 => '<polygon points="400,150 500,350 300,350" fill="rgba(255,255,255,0.1)"/>',
            4 => '<ellipse cx="400" cy="300" rx="150" ry="80" fill="rgba(255,255,255,0.1)"/>'
        ];
        
        $pattern = $patterns[$variant] ?? $patterns[1];
        
        // Adjust color brightness for variants
        $shades = [
            1 => $color,
            2 => $this->adjustColor($color, -20),
            3 => $this->adjustColor($color, -40),
            4 => $this->adjustColor($color, -60)
        ];
        
        $finalColor = $shades[$variant] ?? $color;
        
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="800" height="600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600">
    <defs>
        <linearGradient id="grad' . $variant . '" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:' . $finalColor . ';stop-opacity:1" />
            <stop offset="100%" style="stop-color:' . $this->adjustColor($finalColor, -30) . ';stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect width="100%" height="100%" fill="url(#grad' . $variant . ')"/>
    ' . $pattern . '
    <text x="50%" y="45%" font-family="Arial, sans-serif" font-size="20" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars($text) . '</text>
    <text x="50%" y="55%" font-family="Arial, sans-serif" font-size="16" fill="rgba(255,255,255,0.8)" text-anchor="middle" dominant-baseline="middle">View ' . $variant . '</text>
</svg>';
        
        return $svg;
    }
    
    /**
     * Adjust color brightness
     */
    private function adjustColor(string $hex, int $amount): string
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            return $hex;
        }
        
        $r = max(0, min(255, $r + $amount));
        $g = max(0, min(255, $g + $amount));
        $b = max(0, min(255, $b + $amount));
        
        return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
    }
}