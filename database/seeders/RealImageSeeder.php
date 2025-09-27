<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class RealImageSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing product images
        ProductImage::truncate();
        
        // Clear the products directory
        if (Storage::disk('public')->exists('products')) {
            Storage::disk('public')->deleteDirectory('products');
        }
        Storage::disk('public')->makeDirectory('products');
        
        // Get all products
        $products = Product::all();
        
        $colors = [
            '3B82F6', 'EF4444', '10B981', '8B5CF6', '6B7280',
            'F59E0B', 'EC4899', '64748B', '06B6D4', 'EAB308'
        ];
        
        foreach ($products as $index => $product) {
            $color = $colors[$index % count($colors)];
            
            for ($i = 1; $i <= 4; $i++) {
                $filename = $product->slug . '-' . $i . '.jpg';
                $imagePath = "products/{$filename}";
                
                // Create image using placeholder.com or via.placeholder.com
                $width = 800;
                $height = 600;
                $text = urlencode(substr($product->name, 0, 15) . "-" . $i);
                
                // Try multiple placeholder services
                $imageContent = null;
                $urls = [
                    "https://via.placeholder.com/{$width}x{$height}/{$color}/FFFFFF?text={$text}",
                    "https://picsum.photos/{$width}/{$height}?random={$product->id}{$i}",
                    "https://dummyimage.com/{$width}x{$height}/{$color}/fff&text={$text}"
                ];
                
                foreach ($urls as $url) {
                    try {
                        $this->command->info("Trying to fetch: {$url}");
                        $response = Http::timeout(15)->get($url);
                        
                        if ($response->successful() && $response->body()) {
                            $imageContent = $response->body();
                            $this->command->info("Successfully fetched image from: {$url}");
                            break;
                        }
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to fetch from {$url}: " . $e->getMessage());
                        continue;
                    }
                }
                
                // If no image could be downloaded, create a simple base64 encoded placeholder
                if (!$imageContent) {
                    $imageContent = $this->createSimplePlaceholder($product->name, $i, $color);
                }
                
                // Save the image
                Storage::disk('public')->put($imagePath, $imageContent);
                
                // Create database record
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'alt_text' => $product->name . ' - Image ' . $i,
                    'is_main' => $i === 1,
                    'sort_order' => $i
                ]);
                
                // Update product's main image
                if ($i === 1) {
                    $product->update(['image' => $imagePath]);
                }
            }
            
            $this->command->info("Created images for: {$product->name}");
        }
        
        $this->command->info("All product images created successfully!");
    }
    
    private function createSimplePlaceholder(string $productName, int $imageNumber, string $color): string
    {
        // Create a minimal 1x1 pixel JPEG as absolute fallback
        return base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/AJ8A');
    }
}