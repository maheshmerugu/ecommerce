<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_customers' => Customer::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
        ];

        // Get recent orders
        $recent_orders = Order::with('customer')
                             ->orderBy('created_at', 'desc')
                             ->limit(10)
                             ->get();

        // Get low stock products
        $low_stock_products = Product::where('track_quantity', true)
                                   ->where('quantity', '<=', 10)
                                   ->limit(10)
                                   ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'low_stock_products'));
    }
}
