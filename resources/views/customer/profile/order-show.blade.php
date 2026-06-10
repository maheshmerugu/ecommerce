@extends('customer.layouts.app')

@section(                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('public/storage/' . $item->product->image) }}" 
                                             alt="{{ $item->product_name }}" 
                                             class="w-20 h-20 object-cover rounded border mr-4">
                                    @elseent')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('customer.orders.index') }}" 
                       class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number ?? $order->id }}</h1>
                        <p class="text-gray-600 mt-1">Placed on {{ $order->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ format_currency($order->total ?? 0, 2) }}</div>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            {{ ($order->status ?? 'pending') === 'delivered' ? 'bg-green-100 text-green-800' : 
                               (($order->status ?? 'pending') === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                (($order->status ?? 'pending') === 'shipped' ? 'bg-yellow-100 text-yellow-800' : 
                                 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($order->status ?? 'Pending') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @if(isset($order->items) && $order->items->count() > 0)
                            @foreach($order->items as $item)
                                <div class="p-6 flex items-center space-x-4">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-20 h-20 object-cover rounded-lg">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->product->name ?? 'Product' }}</h3>
                                        <p class="text-gray-600 mt-1">{{ $item->product->description ?? '' }}</p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</span>
                                            <span class="text-sm text-gray-500">Price: {{ format_currency($item->price ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ format_currency(($item->price ?? 0) * $item->quantity, 2) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-6 text-center text-gray-500">
                                <i class="fas fa-box-open text-4xl mb-4"></i>
                                <p>No items found in this order</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Tracking -->
                @if(($order->status ?? 'pending') !== 'pending')
                    <div class="bg-white rounded-lg shadow-sm mt-6">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Order Tracking</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3 {{ ($order->status ?? 'pending') !== 'pending' ? 'text-green-600' : 'text-gray-400' }}">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Order Confirmed</p>
                                        <p class="text-sm text-gray-600">{{ $order->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 {{ in_array($order->status ?? 'pending', ['processing', 'shipped', 'delivered']) ? 'text-green-600' : 'text-gray-400' }}">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-cog text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Processing</p>
                                        <p class="text-sm text-gray-600">Order is being prepared</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 {{ in_array($order->status ?? 'pending', ['shipped', 'delivered']) ? 'text-green-600' : 'text-gray-400' }}">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-shipping-fast text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Shipped</p>
                                        <p class="text-sm text-gray-600">Order is on the way</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 {{ ($order->status ?? 'pending') === 'delivered' ? 'text-green-600' : 'text-gray-400' }}">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Delivered</p>
                                        <p class="text-sm text-gray-600">Order has been delivered</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Order Summary Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">{{ format_currency(($order->subtotal ?? $order->total ?? 0), 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="text-gray-900">{{ format_currency($order->shipping_cost ?? 0, 2) }}</span>
                        </div>
                        @if(isset($order->tax) && $order->tax > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span class="text-gray-900">{{ format_currency($order->tax, 2) }}</span>
                            </div>
                        @endif
                        @if(isset($order->discount) && $order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-{{ format_currency($order->discount, 2) }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total</span>
                            <span>{{ format_currency($order->total ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                    @if(isset($order->shipping_address))
                        <div class="text-gray-600 space-y-1">
                            <p class="font-medium text-gray-900">{{ $order->shipping_address['name'] ?? 'N/A' }}</p>
                            <p>{{ $order->shipping_address['address'] ?? 'Address not available' }}</p>
                            <p>{{ $order->shipping_address['city'] ?? '' }} {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['zip'] ?? '' }}</p>
                            <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                        </div>
                    @else
                        <p class="text-gray-600">Shipping address not available</p>
                    @endif
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                    <div class="text-gray-600">
                        @if(isset($order->payment_method))
                            <p class="font-medium text-gray-900">{{ ucfirst($order->payment_method) }}</p>
                            @if($order->payment_method === 'card')
                                <p>**** **** **** {{ $order->card_last_four ?? 'XXXX' }}</p>
                            @endif
                        @else
                            <p>Payment method not available</p>
                        @endif
                    </div>
                </div>

                <!-- Order Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        @if(($order->status ?? 'pending') === 'delivered')
                            <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-download mr-2"></i>Download Invoice
                            </button>
                        @endif
                        
                        @if(in_array($order->status ?? 'pending', ['pending', 'processing']))
                            <button class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancel Order
                            </button>
                        @endif
                        
                        <button class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-headset mr-2"></i>Contact Support
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection