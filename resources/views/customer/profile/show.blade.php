@extends('customer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
                    <p class="text-gray-600 mt-1">Manage your account settings and personal information</p>
                </div>
                <a href="{{ route('customer.profile.edit') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <div class="text-gray-900">{{ $customer->first_name ?? 'Not provided' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <div class="text-gray-900">{{ $customer->last_name ?? 'Not provided' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="text-gray-900">{{ $customer->email }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <div class="text-gray-900">{{ $customer->phone ?? 'Not provided' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <div class="text-gray-900">{{ $customer->date_of_birth ? $customer->date_of_birth->format('M j, Y') : 'Not provided' }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <div class="text-gray-900">{{ $customer->gender ? ucfirst($customer->gender) : 'Not provided' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                        <a href="{{ route('customer.orders.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">
                            View All Orders <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    @if($recentOrders && $recentOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentOrders as $order)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-gray-900">Order #{{ $order->order_number }}</div>
                                            <div class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('M j, Y g:i A') }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold text-gray-900">${{ number_format($order->total, 2) }}</div>
                                            <div class="text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                                        'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-bag text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                            <p class="text-gray-600 mb-4">Start shopping to see your orders here</p>
                            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Browse Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Account Stats -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Stats</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Orders</span>
                            <span class="font-semibold text-gray-900">{{ $recentOrders ? $customer->orders()->count() : 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Addresses</span>
                            <span class="font-semibold text-gray-900">{{ $addresses ? $addresses->count() : 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Member Since</span>
                            <span class="font-semibold text-gray-900">{{ $customer->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('customer.profile.edit') }}" class="flex items-center text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit w-5"></i>
                            <span class="ml-3">Edit Profile</span>
                        </a>
                        <a href="{{ route('customer.addresses.index') }}" class="flex items-center text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-map-marker-alt w-5"></i>
                            <span class="ml-3">Manage Addresses</span>
                        </a>
                        <a href="{{ route('customer.orders.index') }}" class="flex items-center text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-shopping-bag w-5"></i>
                            <span class="ml-3">Order History</span>
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="flex items-center text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-heart w-5"></i>
                            <span class="ml-3">Wishlist</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection