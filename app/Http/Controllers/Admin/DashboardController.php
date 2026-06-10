<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // Get detailed earnings metrics
        $earnings = [
            'today' => Order::where('payment_status', 'paid')
                           ->whereDate('created_at', Carbon::today())
                           ->sum('total'),
            'this_month' => Order::where('payment_status', 'paid')
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->sum('total'),
            'this_year' => Order::where('payment_status', 'paid')
                               ->whereYear('created_at', now()->year)
                               ->sum('total'),
            'total' => Order::where('payment_status', 'paid')->sum('total'),
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

        // Get top products by revenue
        $top_products = Order::with('items.product')
                            ->where('payment_status', 'paid')
                            ->get()
                            ->flatMap(function ($order) {
                                return $order->items;
                            })
                            ->groupBy('product_id')
                            ->map(function ($items) {
                                return [
                                    'product' => $items->first()->product,
                                    'quantity' => $items->sum('quantity'),
                                    'revenue' => $items->sum(fn($item) => $item->price * $item->quantity)
                                ];
                            })
                            ->sortByDesc('revenue')
                            ->take(5);

        return view('admin.dashboard', compact('stats', 'earnings', 'recent_orders', 'low_stock_products', 'top_products'));
    }
}
