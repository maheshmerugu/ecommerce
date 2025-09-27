<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SimpleProductImageSeeder extends Seeder
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
        
        foreach ($products as $product) {
            for ($i = 1; $i <= 4; $i++) {
                $filename = $product->slug . '-' . $i . '.txt';
                $imagePath = "products/{$filename}";
                
                // Create a simple text file as placeholder
                $content = "Product Image Placeholder\n";
                $content .= "Product: {$product->name}\n";
                $content .= "Image: {$i}\n";
                $content .= "This is a placeholder image file.";
                
                // Save the file
                Storage::disk('public')->put($imagePath, $content);
                
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
            
            $this->command->info("Created placeholder files for: {$product->name}");
        }
        
        $this->command->info("All product placeholder files created successfully!");
    }
}