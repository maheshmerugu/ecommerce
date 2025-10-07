<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show customer profile
     */
    public function show()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get customer addresses
        $addresses = $customer->addresses ?? collect();
        
        // Get recent orders (last 3)
        $recentOrders = $customer->orders()
                               ->with(['items.product.images'])
                               ->orderBy('created_at', 'desc')
                               ->limit(3)
                               ->get();
        
        return view('customer.profile.show', compact('customer', 'addresses', 'recentOrders'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.profile.edit', compact('customer'));
    }

    /**
     * Update customer profile
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Update basic info
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;

        // Update password if provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $customer->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $customer->password = Hash::make($request->new_password);
        }

        $customer->save();

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show customer addresses
     */
    public function addresses()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = collect(); // Empty collection for now
        
        return view('customer.profile.addresses', compact('addresses'));
    }

    /**
     * Show orders
     */
    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        
        // Get all orders for the customer with pagination
        $orders = $customer->orders()
                          ->with(['items.product.images'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        return view('customer.profile.orders', compact('orders'));
    }

    /**
     * Show specific order
     */
    public function orderShow($order)
    {
        return view('customer.profile.order-show', compact('order'));
    }

    /**
     * Create address form
     */
    public function createAddress()
    {
        return view('customer.profile.create-address');
    }

    /**
     * Store new address
     */
    public function storeAddress(Request $request)
    {
        return redirect()->route('customer.addresses.index')->with('success', 'Address functionality coming soon!');
    }

    /**
     * Edit address
     */
    public function editAddress($address)
    {
        return view('customer.profile.edit-address', compact('address'));
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, $address)
    {
        return redirect()->route('customer.addresses.index')->with('success', 'Address functionality coming soon!');
    }

    /**
     * Delete address
     */
    public function destroyAddress($address)
    {
        return redirect()->route('customer.addresses.index')->with('success', 'Address functionality coming soon!');
    }
}
