<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;

class ShowStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'show:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show statistics about the e-commerce application data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 E-commerce Application Statistics');
        $this->info('=====================================');
        $this->info('');
        
        // Admin statistics
        $totalAdmins = Admin::count();
        $activeAdmins = Admin::where('is_active', true)->count();
        $this->info("👥 Admins: {$totalAdmins} total ({$activeAdmins} active)");
        
        // Category statistics
        $totalCategories = Category::count();
        $activeCategories = Category::where('status', true)->count();
        $this->info("📁 Categories: {$totalCategories} total ({$activeCategories} active)");
        
        // Product statistics
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', true)->count();
        $featuredProducts = Product::where('featured', true)->count();
        $this->info("📦 Products: {$totalProducts} total ({$activeProducts} active, {$featuredProducts} featured)");
        
        // Customer statistics
        $totalCustomers = Customer::count();
        $this->info("👤 Customers: {$totalCustomers} registered");
        
        // Order statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $this->info("🛒 Orders: {$totalOrders} total ({$pendingOrders} pending, {$processingOrders} processing)");
        
        $this->info('');
        
        if ($totalProducts > 0) {
            $this->info('💰 Product Price Range:');
            $minPrice = Product::min('price');
            $maxPrice = Product::max('price');
            $avgPrice = Product::avg('price');
            $this->info("   Min: $" . number_format($minPrice, 2));
            $this->info("   Max: $" . number_format($maxPrice, 2));
            $this->info("   Avg: $" . number_format($avgPrice, 2));
        }
        
        $this->info('');
        $this->info('Recent Activity:');
        
        // Recent products
        $recentProducts = Product::orderBy('created_at', 'desc')->limit(3)->get(['name', 'created_at']);
        if ($recentProducts->count() > 0) {
            $this->info('📦 Latest Products:');
            foreach ($recentProducts as $product) {
                $this->info("   • {$product->name} ({$product->created_at->diffForHumans()})");
            }
        }
        
        // Recent orders
        $recentOrders = Order::orderBy('created_at', 'desc')->limit(3)->get(['id', 'total', 'status', 'created_at']);
        if ($recentOrders->count() > 0) {
            $this->info('🛒 Latest Orders:');
            foreach ($recentOrders as $order) {
                $this->info("   • Order #{$order->id} - $" . number_format($order->total, 2) . " ({$order->status}) - {$order->created_at->diffForHumans()}");
            }
        }
    }
}
