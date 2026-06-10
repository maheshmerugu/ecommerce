<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DummyProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::where('name', 'Toys Cars')->first();
        if (!$category) {
            $category = Category::create(['name' => 'Toys Cars', 'status' => true]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $sku = sprintf('TOY-%03d', $i);
            $name = "Toy Car Model {$i}";
            $price = 9.99 + ($i * 2);

            $product = Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'description' => "Description for {$name}",
                    'short_description' => "Short description for {$name}",
                    'price' => $price,
                    'quantity' => 100,
                    'track_quantity' => true,
                    'status' => true,
                    'featured' => false,
                ]
            );

            // attach category
            $product->categories()->syncWithoutDetaching([$category->id]);

            // create product image (uses storage path 'products/productN.svg')
            $imagePath = "products/product{$i}.svg";

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'image_path' => $imagePath],
                [
                    'alt_text' => $name,
                    'is_main' => true,
                    'sort_order' => 1,
                ]
            );
        }
    }
}
