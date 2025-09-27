<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get customer statistics
        $stats = [
            'total_orders' => $customer->orders()->count(),
            'pending_orders' => $customer->orders()->where('status', 'pending')->count(),
            'completed_orders' => $customer->orders()->where('status', 'delivered')->count(),
            'total_spent' => $customer->orders()->where('payment_status', 'paid')->sum('total'),
        ];

        // Get recent orders
        $recent_orders = $customer->orders()
                                ->with(['items.product'])
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

        return view('customer.dashboard', compact('customer', 'stats', 'recent_orders'));
    }
}
