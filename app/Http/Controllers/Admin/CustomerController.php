<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.items', 'addresses']);
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total');
        return view('admin.customers.show', compact('customer', 'totalSpent'));
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);
        $label = $customer->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Customer account {$label} successfully!");
    }
}
