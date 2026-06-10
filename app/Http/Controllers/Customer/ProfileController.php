<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
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
        $addresses = $customer->addresses()->orderByDesc('is_default')->orderBy('created_at')->get();
        return view('customer.profile.addresses', compact('addresses'));
    }

    /**
     * Show orders
     */
    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        $orders = $customer->orders()
                          ->with(['items.product.images'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        return view('customer.profile.orders', compact('orders'));
    }

    /**
     * Show specific order
     */
    public function orderShow(Order $order)
    {
        $customer = Auth::guard('customer')->user();
        if ($order->customer_id !== $customer->id) {
            abort(403);
        }
        $order->load(['items.product.images']);
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
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'postal_code'    => 'required|string|max:20',
            'type'           => 'nullable|in:shipping,billing,home,work,other',
        ]);

        $isFirst = $customer->addresses()->count() === 0;

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default') || $isFirst) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $customer->addresses()->create([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'phone'          => $request->phone,
            'company'        => $request->company,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city'           => $request->city,
            'state'          => $request->state,
            'postal_code'    => $request->postal_code,
            'country'        => $request->input('country', 'India'),
            'type'           => $request->input('type', 'shipping'),
            'is_default'     => $request->boolean('is_default') || $isFirst,
        ]);

        return redirect()->route('customer.addresses.index')->with('success', 'Address added successfully!');
    }

    /**
     * Edit address
     */
    public function editAddress(Address $address)
    {
        $customer = Auth::guard('customer')->user();
        if ($address->customer_id !== $customer->id) {
            abort(403);
        }
        return view('customer.profile.edit-address', compact('address'));
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, Address $address)
    {
        $customer = Auth::guard('customer')->user();
        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'postal_code'    => 'required|string|max:20',
            'type'           => 'nullable|in:shipping,billing,home,work,other',
        ]);

        if ($request->boolean('is_default')) {
            $customer->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'phone'          => $request->phone,
            'company'        => $request->company,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city'           => $request->city,
            'state'          => $request->state,
            'postal_code'    => $request->postal_code,
            'country'        => $request->input('country', 'India'),
            'type'           => $request->input('type', 'shipping'),
            'is_default'     => $request->boolean('is_default'),
        ]);

        return redirect()->route('customer.addresses.index')->with('success', 'Address updated successfully!');
    }

    /**
     * Set address as default
     */
    public function setDefaultAddress(Address $address)
    {
        $customer = Auth::guard('customer')->user();
        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $customer->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default address updated!');
    }

    /**
     * Delete address
     */
    public function destroyAddress(Address $address)
    {
        $customer = Auth::guard('customer')->user();
        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $address->delete();

        // If deleted address was default, make newest the default
        if ($address->is_default) {
            $customer->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return redirect()->route('customer.addresses.index')->with('success', 'Address deleted successfully!');
    }
}
