<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('customer.profile') }}" class="text-sm text-gray-600 hover:text-blue-600">
                            My Account
                        </a>
                    @endauth
                    <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-blue-600">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
            <p class="text-gray-600">Thank you for your purchase. Your order has been confirmed.</p>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-1">Order #{{ $order->order_number }}</h2>
                    <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="grid md:grid-cols-2 gap-6 mb-6 pb-6 border-b">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Payment Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="capitalize">{{ $order->payment_method }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Status:</span>
                            <span class="font-medium text-green-600">{{ ucfirst($order->payment_status) }}</span>
                        </div>
                        @if($order->razorpay_payment_id)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment ID:</span>
                            <span class="font-mono text-xs">{{ $order->razorpay_payment_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Delivery Information</h3>
                    <div class="text-sm">
                        <p class="font-medium">{{ $order->customer_name }}</p>
                        <p class="text-gray-600">{{ $order->customer_phone }}</p>
                        @php
                            $shipping = json_decode($order->shipping_address, true);
                        @endphp
                        <div class="mt-2 text-gray-600">
                            <p>{{ $shipping['address'] }}</p>
                            <p>{{ $shipping['city'] }}, {{ $shipping['state'] }} - {{ $shipping['pincode'] }}</p>
                            <p>{{ $shipping['country'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Items Ordered</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            @if($item->product && $item->product->images && $item->product->images->count() > 0)
                                <img src="{{ asset('public/storage/' . $item->product->images->first()->image_path) }}" 
                                    alt="{{ $item->product_name }}" 
                                    class="w-16 h-16 object-cover rounded border">
                            @else
                                <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded border">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $item->product_name }}</h4>
                            @if($item->product && $item->product->short_description)
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($item->product->short_description, 100) }}</p>
                            @endif
                            <div class="flex items-center space-x-4 mt-2 text-sm">
                                <span class="text-gray-600">Quantity: {{ $item->quantity }}</span>
                                <span class="text-gray-600">Price: ₹{{ number_format($item->price, 0) }}</span>
                                @if($item->product_sku)
                                <span class="text-gray-600">SKU: {{ $item->product_sku }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">₹{{ number_format($item->total, 0) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Total -->
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>₹{{ number_format($order->subtotal, 0) }}</span>
                            </div>
                            @if($order->shipping_amount > 0)
                            <div class="flex justify-between">
                                <span>Shipping:</span>
                                <span>₹{{ number_format($order->shipping_amount, 0) }}</span>
                            </div>
                            @endif
                            @if($order->tax_amount > 0)
                            <div class="flex justify-between">
                                <span>Tax:</span>
                                <span>₹{{ number_format($order->tax_amount, 0) }}</span>
                            </div>
                            @endif
                            @if($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount:</span>
                                <span>-₹{{ number_format($order->discount_amount, 0) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-lg font-semibold pt-2 border-t">
                                <span>Total:</span>
                                <span>₹{{ number_format($order->total, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>

        <!-- Next Steps -->
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        <strong>What's next?</strong>
                        You will receive an order confirmation email shortly. We'll notify you when your order is shipped.
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('home') }}" 
                class="flex-1 bg-blue-600 text-white text-center py-3 px-6 rounded-md font-medium hover:bg-blue-700 transition-colors">
                Continue Shopping
            </a>
            <a href="{{ route('customer.profile') }}" 
                class="flex-1 bg-gray-200 text-gray-800 text-center py-3 px-6 rounded-md font-medium hover:bg-gray-300 transition-colors">
                View My Orders
            </a>
        </div>
    </div>
</body>
</html>