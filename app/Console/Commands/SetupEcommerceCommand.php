<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;

class SetupEcommerceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:ecommerce {--fresh : Drop existing data and create fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup admin user, categories and sample products for the e-commerce application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up e-commerce application...');
        
        if ($this->option('fresh')) {
            $this->warn('This will delete all existing data!');
            if ($this->confirm('Are you sure you want to continue?')) {
                $this->info('Deleting existing data...');
                
                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate tables in proper order
                DB::table('product_categories')->truncate();
                DB::table('cart_items')->truncate();
                DB::table('order_items')->truncate();
                Product::truncate();
                Category::truncate();
                Admin::truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                $this->info('✓ All existing data deleted');
            } else {
                $this->info('Setup cancelled.');
                return;
            }
        }

        // Create admin user
        $this->createAdmin();
        
        // Create categories
        $this->createCategories();
        
        // Create sample products
        $this->createProducts();
        
        $this->info('✅ E-commerce setup completed successfully!');
        $this->info('');
        $this->info('Admin Login Credentials:');
        $this->info('Email: admin@ecommerce.com');
        $this->info('Password: admin123');
    }

    private function createAdmin()
    {
        $this->info('Creating admin user...');
        
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@ecommerce.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@ecommerce.com',
                'password' => Hash::make('admin123'),
                'is_active' => true
            ]
        );
        
        if ($admin->wasRecentlyCreated) {
            $this->info('✓ Admin user created');
        } else {
            $this->warn('✓ Admin user already exists');
        }
    }

    private function createCategories()
    {
        $this->info('Creating categories...');
        
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Latest electronic gadgets and devices',
                'status' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Clothing',
                'description' => 'Fashion and clothing items',
                'status' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Books',
                'description' => 'Educational and entertainment books',
                'status' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement and garden supplies',
                'status' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports equipment and accessories',
                'status' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Beauty & Health',
                'description' => 'Beauty products and health supplements',
                'status' => true,
                'sort_order' => 6
            ]
        ];

        foreach ($categories as $categoryData) {
            $categoryData['slug'] = Str::slug($categoryData['name']);
            
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
            
            if ($category->wasRecentlyCreated) {
                $this->info("✓ Created category: {$categoryData['name']}");
            } else {
                $this->warn("✓ Category already exists: {$categoryData['name']}");
            }
        }
    }

    private function createProducts()
    {
        $this->info('Creating sample products...');
        
        // Get category IDs
        $electronics = Category::where('slug', 'electronics')->first();
        $clothing = Category::where('slug', 'clothing')->first();
        $books = Category::where('slug', 'books')->first();
        $homeGarden = Category::where('slug', 'home-garden')->first();
        $sports = Category::where('slug', 'sports')->first();
        $beauty = Category::where('slug', 'beauty-health')->first();

        $products = [
            // Electronics
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Latest iPhone with A17 Pro chip, 48MP camera system, and titanium design.',
                'short_description' => 'Premium smartphone with advanced features',
                'sku' => 'IPHONE15PM',
                'price' => 1199.99,
                'special_price' => 1099.99,
                'quantity' => 50,
                'status' => true,
                'featured' => true,
                'weight' => 0.221,
                'categories' => [$electronics->id ?? 1]
            ],
            [
                'name' => 'MacBook Air M3',
                'description' => 'Ultra-thin laptop with M3 chip, 13.6-inch Liquid Retina display, and all-day battery life.',
                'short_description' => 'Powerful and portable laptop',
                'sku' => 'MBA-M3-13',
                'price' => 1299.99,
                'quantity' => 25,
                'status' => true,
                'featured' => true,
                'weight' => 1.24,
                'categories' => [$electronics->id ?? 1]
            ],
            [
                'name' => 'Samsung 55" QLED TV',
                'description' => '4K Ultra HD Smart TV with Quantum HDR and Alexa built-in.',
                'short_description' => 'Premium 4K Smart TV',
                'sku' => 'SAM-QLED55',
                'price' => 899.99,
                'special_price' => 799.99,
                'quantity' => 15,
                'status' => true,
                'weight' => 18.5,
                'categories' => [$electronics->id ?? 1]
            ],
            
            // Clothing
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Comfortable running shoes with Air Max cushioning and breathable mesh upper.',
                'short_description' => 'Premium running shoes',
                'sku' => 'NIKE-AM270',
                'price' => 149.99,
                'quantity' => 100,
                'status' => true,
                'featured' => true,
                'weight' => 0.8,
                'categories' => [$clothing->id ?? 2, $sports->id ?? 5]
            ],
            [
                'name' => 'Levi\'s 501 Original Jeans',
                'description' => 'Classic straight-leg jeans made from 100% cotton denim.',
                'short_description' => 'Classic denim jeans',
                'sku' => 'LEVIS-501',
                'price' => 89.99,
                'quantity' => 75,
                'status' => true,
                'weight' => 0.6,
                'categories' => [$clothing->id ?? 2]
            ],
            
            // Books
            [
                'name' => 'The Art of Programming',
                'description' => 'Comprehensive guide to modern programming techniques and best practices.',
                'short_description' => 'Programming guide for developers',
                'sku' => 'BOOK-PROG',
                'price' => 49.99,
                'special_price' => 39.99,
                'quantity' => 200,
                'status' => true,
                'weight' => 0.8,
                'categories' => [$books->id ?? 3]
            ],
            
            // Home & Garden
            [
                'name' => 'Robot Vacuum Cleaner',
                'description' => 'Smart robot vacuum with WiFi connectivity and automatic scheduling.',
                'short_description' => 'Automated cleaning solution',
                'sku' => 'ROBOT-VAC',
                'price' => 299.99,
                'special_price' => 249.99,
                'quantity' => 30,
                'status' => true,
                'featured' => true,
                'weight' => 3.5,
                'categories' => [$homeGarden->id ?? 4]
            ],
            
            // Beauty & Health
            [
                'name' => 'Skincare Essential Kit',
                'description' => 'Complete skincare routine with cleanser, toner, serum, and moisturizer.',
                'short_description' => 'Complete skincare solution',
                'sku' => 'SKIN-KIT',
                'price' => 79.99,
                'quantity' => 60,
                'status' => true,
                'weight' => 0.5,
                'categories' => [$beauty->id ?? 6]
            ],
            
            // Sports
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'Non-slip yoga mat with extra cushioning for comfortable practice.',
                'short_description' => 'Premium yoga mat',
                'sku' => 'YOGA-MAT',
                'price' => 29.99,
                'quantity' => 80,
                'status' => true,
                'weight' => 1.2,
                'categories' => [$sports->id ?? 5]
            ],
            [
                'name' => 'Wireless Bluetooth Earbuds',
                'description' => 'True wireless earbuds with active noise cancellation and 24-hour battery life.',
                'short_description' => 'Premium wireless earbuds',
                'sku' => 'BT-EARBUDS',
                'price' => 199.99,
                'special_price' => 159.99,
                'quantity' => 40,
                'status' => true,
                'featured' => true,
                'weight' => 0.1,
                'categories' => [$electronics->id ?? 1]
            ]
        ];

        foreach ($products as $productData) {
            $categories = $productData['categories'];
            unset($productData['categories']);
            
            $productData['slug'] = Str::slug($productData['name']);
            $productData['track_quantity'] = true;
            $productData['min_quantity'] = 1;
            $productData['sort_order'] = 0;
            
            $product = Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
            
            if ($product->wasRecentlyCreated) {
                // Attach categories to product
                $product->categories()->sync($categories);
                $this->info("✓ Created product: {$productData['name']}");
            } else {
                $this->warn("✓ Product already exists: {$productData['name']}");
            }
        }
    }
}
