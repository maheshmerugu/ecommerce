<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $electronics = Category::where('slug', 'electronics')->first();
        $clothing = Category::where('slug', 'clothing')->first();
        $homeGarden = Category::where('slug', 'home-garden')->first();
        $sports = Category::where('slug', 'sports-fitness')->first();
        $books = Category::where('slug', 'books')->first();
        $beauty = Category::where('slug', 'beauty-health')->first();

        $products = [
            // Electronics
            [
                'name' => 'Smartphone Pro Max 128GB',
                'description' => 'Latest smartphone with advanced camera system and powerful processor. Features include wireless charging, water resistance, and premium design.',
                'short_description' => 'Latest smartphone with advanced features',
                'price' => 999.99,
                'special_price' => 899.99,
                'quantity' => 50,
                'featured' => true,
                'category_id' => $electronics?->id,
                'weight' => 0.2,
            ],
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'Premium wireless headphones with noise cancellation and superior sound quality. Long battery life and comfortable fit.',
                'short_description' => 'Premium wireless headphones with noise cancellation',
                'price' => 199.99,
                'quantity' => 75,
                'featured' => true,
                'category_id' => $electronics?->id,
                'weight' => 0.3,
            ],
            [
                'name' => '4K Smart TV 55 inch',
                'description' => 'Ultra HD 4K Smart TV with streaming apps built-in. HDR support and sleek design perfect for any living room.',
                'short_description' => 'Ultra HD 4K Smart TV with streaming capabilities',
                'price' => 799.99,
                'quantity' => 25,
                'featured' => false,
                'category_id' => $electronics?->id,
                'weight' => 15.5,
            ],

            // Clothing
            [
                'name' => 'Premium Cotton T-Shirt',
                'description' => 'Comfortable and stylish cotton t-shirt made from 100% organic cotton. Available in multiple colors and sizes.',
                'short_description' => '100% organic cotton t-shirt',
                'price' => 29.99,
                'quantity' => 100,
                'featured' => true,
                'category_id' => $clothing?->id,
                'weight' => 0.2,
            ],
            [
                'name' => 'Denim Jeans Classic Fit',
                'description' => 'Classic fit denim jeans made from high-quality denim fabric. Comfortable and durable for everyday wear.',
                'short_description' => 'Classic fit denim jeans',
                'price' => 79.99,
                'quantity' => 60,
                'featured' => false,
                'category_id' => $clothing?->id,
                'weight' => 0.6,
            ],

            // Home & Garden
            [
                'name' => 'Coffee Maker Deluxe',
                'description' => 'Premium coffee maker with programmable settings and thermal carafe. Makes perfect coffee every time.',
                'short_description' => 'Premium programmable coffee maker',
                'price' => 149.99,
                'quantity' => 40,
                'featured' => true,
                'category_id' => $homeGarden?->id,
                'weight' => 2.5,
            ],

            // Sports & Fitness
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'High-quality yoga mat with excellent grip and cushioning. Perfect for yoga, pilates, and other exercises.',
                'short_description' => 'Premium yoga mat with excellent grip',
                'price' => 49.99,
                'quantity' => 80,
                'featured' => true,
                'category_id' => $sports?->id,
                'weight' => 1.2,
            ],
            [
                'name' => 'Adjustable Dumbbells Set',
                'description' => 'Complete adjustable dumbbells set ranging from 5-50 lbs per dumbbell. Space-saving design for home workouts.',
                'short_description' => 'Adjustable dumbbells set 5-50 lbs',
                'price' => 299.99,
                'quantity' => 20,
                'featured' => false,
                'category_id' => $sports?->id,
                'weight' => 25.0,
            ],

            // Books
            [
                'name' => 'Web Development Complete Guide',
                'description' => 'Comprehensive guide to modern web development covering HTML, CSS, JavaScript, and popular frameworks.',
                'short_description' => 'Complete guide to web development',
                'price' => 39.99,
                'quantity' => 50,
                'featured' => false,
                'category_id' => $books?->id,
                'weight' => 0.8,
            ],

            // Beauty & Health
            [
                'name' => 'Vitamin C Serum',
                'description' => 'Anti-aging vitamin C serum with hyaluronic acid. Brightens skin and reduces fine lines.',
                'short_description' => 'Anti-aging vitamin C serum',
                'price' => 24.99,
                'quantity' => 100,
                'featured' => true,
                'category_id' => $beauty?->id,
                'weight' => 0.1,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'short_description' => $productData['short_description'],
                'sku' => 'SKU' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'price' => $productData['price'],
                'special_price' => $productData['special_price'] ?? null,
                'quantity' => $productData['quantity'],
                'min_quantity' => 1,
                'track_quantity' => true,
                'status' => true, // Use boolean instead of string
                'weight' => $productData['weight'],
                'featured' => $productData['featured'],
                'sort_order' => 0,
                'meta_title' => $productData['name'],
                'meta_description' => $productData['short_description'],
                // Add main image path for fallback
                'image' => 'products/' . Str::slug($productData['name']) . '-1.jpg',
            ]);

            // Attach product to category if category exists
            if ($productData['category_id']) {
                $product->categories()->attach($productData['category_id']);
            }
        }
    }
}
