<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Latest electronic devices and gadgets',
                'sort_order' => 1,
            ],
            [
                'name' => 'Clothing',
                'description' => 'Fashion and apparel for all ages',
                'sort_order' => 2,
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement and garden supplies',
                'sort_order' => 3,
            ],
            [
                'name' => 'Sports & Fitness',
                'description' => 'Sports equipment and fitness gear',
                'sort_order' => 4,
            ],
            [
                'name' => 'Books',
                'description' => 'Books, eBooks and educational materials',
                'sort_order' => 5,
            ],
            [
                'name' => 'Beauty & Health',
                'description' => 'Beauty products and health supplements',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'sort_order' => $categoryData['sort_order'],
                'status' => true, // Use boolean instead of string
                'parent_id' => null,
                'meta_title' => $categoryData['name'],
                'meta_description' => $categoryData['description'],
            ]);
        }
    }
}
