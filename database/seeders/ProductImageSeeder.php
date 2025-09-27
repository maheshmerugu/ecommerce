<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample image data for different products
        $productImages = [
            'Smartphone Pro Max 128GB' => [
                [
                    'filename' => 'smartphone-pro-max-128gb-1.svg',
                    'alt_text' => 'Smartphone Pro Max - Front view',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Smartphone Pro Max - Front', '#1E40AF')
                ],
                [
                    'filename' => 'smartphone-pro-max-128gb-2.svg',
                    'alt_text' => 'Smartphone Pro Max - Back view',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Smartphone Pro Max - Back', '#1E3A8A')
                ],
                [
                    'filename' => 'smartphone-pro-max-128gb-3.svg',
                    'alt_text' => 'Smartphone Pro Max - Side view',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Smartphone Pro Max - Side', '#1E293B')
                ],
                [
                    'filename' => 'smartphone-pro-max-128gb-4.svg',
                    'alt_text' => 'Smartphone Pro Max - Features',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Smartphone Pro Max - Features', '#0F172A')
                ]
            ],
            'Wireless Bluetooth Headphones' => [
                [
                    'filename' => 'wireless-bluetooth-headphones-1.jpg',
                    'alt_text' => 'Wireless Bluetooth Headphones - Main view',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Bluetooth Headphones', '#DC2626')
                ],
                [
                    'filename' => 'wireless-bluetooth-headphones-2.jpg',
                    'alt_text' => 'Wireless Bluetooth Headphones - Side view',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Headphones - Side', '#B91C1C')
                ],
                [
                    'filename' => 'wireless-bluetooth-headphones-3.jpg',
                    'alt_text' => 'Wireless Bluetooth Headphones - Features',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Headphones - Features', '#991B1B')
                ],
                [
                    'filename' => 'wireless-bluetooth-headphones-4.jpg',
                    'alt_text' => 'Wireless Bluetooth Headphones - Case',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Headphones - Case', '#7F1D1D')
                ]
            ],
            '4K Smart TV 55 inch' => [
                [
                    'filename' => '4k-smart-tv-55-inch-1.jpg',
                    'alt_text' => '4K Smart TV - Front view',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('4K Smart TV', '#059669')
                ],
                [
                    'filename' => '4k-smart-tv-55-inch-2.jpg',
                    'alt_text' => '4K Smart TV - Side view',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Smart TV - Side', '#047857')
                ],
                [
                    'filename' => '4k-smart-tv-55-inch-3.jpg',
                    'alt_text' => '4K Smart TV - Remote control',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Smart TV - Remote', '#065F46')
                ],
                [
                    'filename' => '4k-smart-tv-55-inch-4.jpg',
                    'alt_text' => '4K Smart TV - Display quality',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Smart TV - Display', '#064E3B')
                ]
            ],
            'Premium Cotton T-Shirt' => [
                [
                    'filename' => 'premium-cotton-t-shirt-1.jpg',
                    'alt_text' => 'Premium Cotton T-Shirt - Front',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Cotton T-Shirt', '#7C3AED')
                ],
                [
                    'filename' => 'premium-cotton-t-shirt-2.jpg',
                    'alt_text' => 'Premium Cotton T-Shirt - Back',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('T-Shirt - Back', '#6D28D9')
                ],
                [
                    'filename' => 'premium-cotton-t-shirt-3.jpg',
                    'alt_text' => 'Premium Cotton T-Shirt - Side',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('T-Shirt - Side', '#5B21B6')
                ],
                [
                    'filename' => 'premium-cotton-t-shirt-4.jpg',
                    'alt_text' => 'Premium Cotton T-Shirt - Detail',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('T-Shirt - Detail', '#4C1D95')
                ]
            ],
            'Denim Jeans Classic Fit' => [
                [
                    'filename' => 'denim-jeans-classic-fit-1.jpg',
                    'alt_text' => 'Denim Jeans - Front view',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Denim Jeans', '#1F2937')
                ],
                [
                    'filename' => 'denim-jeans-classic-fit-2.jpg',
                    'alt_text' => 'Denim Jeans - Back view',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Jeans - Back', '#111827')
                ],
                [
                    'filename' => 'denim-jeans-classic-fit-3.jpg',
                    'alt_text' => 'Denim Jeans - Side view',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Jeans - Side', '#374151')
                ],
                [
                    'filename' => 'denim-jeans-classic-fit-4.jpg',
                    'alt_text' => 'Denim Jeans - Details',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Jeans - Details', '#4B5563')
                ]
            ],
            'Coffee Maker Deluxe' => [
                [
                    'filename' => 'coffee-maker-deluxe-1.jpg',
                    'alt_text' => 'Coffee Maker Deluxe - Main view',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Coffee Maker', '#D97706')
                ],
                [
                    'filename' => 'coffee-maker-deluxe-2.jpg',
                    'alt_text' => 'Coffee Maker Deluxe - Controls',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Coffee Maker - Controls', '#B45309')
                ],
                [
                    'filename' => 'coffee-maker-deluxe-3.jpg',
                    'alt_text' => 'Coffee Maker Deluxe - Carafe',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Coffee Maker - Carafe', '#92400E')
                ],
                [
                    'filename' => 'coffee-maker-deluxe-4.jpg',
                    'alt_text' => 'Coffee Maker Deluxe - In use',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Coffee Maker - In Use', '#78350F')
                ]
            ],
            'Yoga Mat Premium' => [
                [
                    'filename' => 'yoga-mat-premium-1.jpg',
                    'alt_text' => 'Premium Yoga Mat - Full view',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Yoga Mat', '#EC4899')
                ],
                [
                    'filename' => 'yoga-mat-premium-2.jpg',
                    'alt_text' => 'Premium Yoga Mat - Texture',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Yoga Mat - Texture', '#DB2777')
                ],
                [
                    'filename' => 'yoga-mat-premium-3.jpg',
                    'alt_text' => 'Premium Yoga Mat - Rolled',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Yoga Mat - Rolled', '#BE185D')
                ],
                [
                    'filename' => 'yoga-mat-premium-4.jpg',
                    'alt_text' => 'Premium Yoga Mat - In use',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Yoga Mat - In Use', '#9D174D')
                ]
            ],
            'Adjustable Dumbbells Set' => [
                [
                    'filename' => 'adjustable-dumbbells-set-1.jpg',
                    'alt_text' => 'Adjustable Dumbbells Set - Main view',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Dumbbells Set', '#6B7280')
                ],
                [
                    'filename' => 'adjustable-dumbbells-set-2.jpg',
                    'alt_text' => 'Adjustable Dumbbells Set - Adjustment',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Dumbbells - Adjustment', '#4B5563')
                ],
                [
                    'filename' => 'adjustable-dumbbells-set-3.jpg',
                    'alt_text' => 'Adjustable Dumbbells Set - Storage',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Dumbbells - Storage', '#374151')
                ],
                [
                    'filename' => 'adjustable-dumbbells-set-4.jpg',
                    'alt_text' => 'Adjustable Dumbbells Set - In use',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Dumbbells - In Use', '#1F2937')
                ]
            ],
            'Web Development Complete Guide' => [
                [
                    'filename' => 'web-development-complete-guide-1.jpg',
                    'alt_text' => 'Web Development Guide - Cover',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Web Dev Guide', '#0891B2')
                ],
                [
                    'filename' => 'web-development-complete-guide-2.jpg',
                    'alt_text' => 'Web Development Guide - Back cover',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Book - Back Cover', '#0E7490')
                ],
                [
                    'filename' => 'web-development-complete-guide-3.jpg',
                    'alt_text' => 'Web Development Guide - Contents',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Book - Contents', '#155E75')
                ],
                [
                    'filename' => 'web-development-complete-guide-4.jpg',
                    'alt_text' => 'Web Development Guide - Sample pages',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Book - Pages', '#164E63')
                ]
            ],
            'Vitamin C Serum' => [
                [
                    'filename' => 'vitamin-c-serum-1.jpg',
                    'alt_text' => 'Vitamin C Serum - Main bottle',
                    'is_main' => true,
                    'sort_order' => 1,
                    'content' => $this->createPlaceholderImage('Vitamin C Serum', '#F59E0B')
                ],
                [
                    'filename' => 'vitamin-c-serum-2.jpg',
                    'alt_text' => 'Vitamin C Serum - Ingredients',
                    'is_main' => false,
                    'sort_order' => 2,
                    'content' => $this->createPlaceholderImage('Serum - Ingredients', '#D97706')
                ],
                [
                    'filename' => 'vitamin-c-serum-3.jpg',
                    'alt_text' => 'Vitamin C Serum - Application',
                    'is_main' => false,
                    'sort_order' => 3,
                    'content' => $this->createPlaceholderImage('Serum - Application', '#B45309')
                ],
                [
                    'filename' => 'vitamin-c-serum-4.jpg',
                    'alt_text' => 'Vitamin C Serum - Packaging',
                    'is_main' => false,
                    'sort_order' => 4,
                    'content' => $this->createPlaceholderImage('Serum - Package', '#92400E')
                ]
            ]
        ];

        foreach ($productImages as $productName => $images) {
            $product = Product::where('name', $productName)->first();
            
            if ($product) {
                foreach ($images as $imageData) {
                    // Create the image file
                    $imagePath = "products/{$imageData['filename']}";
                    Storage::disk('public')->put($imagePath, $imageData['content']);
                    
                    // Create the ProductImage record
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'alt_text' => $imageData['alt_text'],
                        'is_main' => $imageData['is_main'],
                        'sort_order' => $imageData['sort_order']
                    ]);
                    
                    // Update product's main image field with the first image
                    if ($imageData['is_main']) {
                        $product->update(['image' => $imagePath]);
                    }
                }
                
                $this->command->info("Created images for product: {$productName}");
            }
        }
    }

    /**
     * Create a placeholder image with text and color.
     */
    private function createPlaceholderImage(string $text, string $color): string
    {
        // Create a proper SVG image with correct headers
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="800" height="600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600">
    <rect width="100%" height="100%" fill="' . $color . '"/>
    <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars($text) . '</text>
    <circle cx="400" cy="200" r="50" fill="rgba(255,255,255,0.1)"/>
    <circle cx="300" cy="400" r="30" fill="rgba(255,255,255,0.1)"/>
    <circle cx="500" cy="450" r="40" fill="rgba(255,255,255,0.1)"/>
</svg>';
        
        return $svg;
    }
}