@extends('customer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order History</h1>
                    <p class="text-gray-600 mt-1">View and track all your orders</p>
                </div>
                <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Orders List -->
        <div class="bg-white rounded-lg shadow-sm">
            @if($orders && $orders->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Order #{{ $order->order_number ?? $order->id }}</h3>
                                        <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-semibold text-gray-900">₹{{ number_format($order->total ?? 0, 0) }}</div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ ($order->status ?? 'pending') === 'delivered' ? 'bg-green-100 text-green-800' : 
                                               (($order->status ?? 'pending') === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                                (($order->status ?? 'pending') === 'shipped' ? 'bg-yellow-100 text-yellow-800' : 
                                                 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($order->status ?? 'Pending') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items Preview -->
                            @if(isset($order->items) && $order->items->count() > 0)
                                <div class="flex items-center space-x-4 mb-4">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="flex items-center space-x-2">
                                            @if($item->product && $item->product->images && $item->product->images->count() > 0)
                                                <img src="{{ asset('public/storage/' . $item->product->images->first()->image_path) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="w-12 h-12 object-cover rounded">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Product' }}</p>
                                                <p class="text-xs text-gray-600">Qty: {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 3)
                                        <div class="text-sm text-gray-600">
                                            +{{ $order->items->count() - 3 }} more items
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Order Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('customer.orders.show', $order->id) }}" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View Details
                                    </a>
                                    @if(($order->status ?? 'pending') === 'delivered')
                                        <button class="text-green-600 hover:text-green-700 text-sm font-medium">
                                            Download Invoice
                                        </button>
                                    @endif
                                    @if(in_array($order->status ?? 'pending', ['pending', 'processing']))
                                        <button class="text-red-600 hover:text-red-700 text-sm font-medium">
                                            Cancel Order
                                        </button>
                                    @endif
                                </div>
                                @if(in_array($order->status ?? 'pending', ['shipped', 'processing']))
                                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        Track Order
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if(method_exists($orders, 'links'))
                    <div class="p-6 border-t border-gray-200">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <i class="fas fa-shopping-bag text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No orders yet</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        You haven't placed any orders yet. Start shopping to see your order history here.
                    </p>
                    <a href="{{ route('products.index') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection