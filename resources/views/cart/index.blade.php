<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - {{ $storeName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .mobile-hide { display: none; }
            .mobile-stack { display: block !important; }
            .mobile-full-width { width: 100% !important; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header Navigation -->
    <div class="bg-white shadow-sm border-b mb-4">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo & Back -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <a href="{{ route('home') }}" class="text-lg md:text-2xl font-bold text-blue-600 whitespace-nowrap">
                        {{ $storeName }}
                    </a>
                </div>

                <!-- Cart Title -->
                <h1 class="text-lg md:text-xl font-semibold text-gray-900">Shopping Cart</h1>

                <!-- Right Actions -->
                <div class="flex items-center space-x-4">
                    @auth('customer')
                    <a href="{{ route('customer.dashboard') }}" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-user text-lg"></i>
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Content -->
    <div class="container mx-auto px-3 md:px-4">
        @if($cartItems && $cartItems->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="p-4 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Cart Items ({{ $cart->total_quantity }})
                                </h2>
                                <button onclick="clearCart()" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i>
                                    Clear Cart
                                </button>
                            </div>
                        </div>

                        <div class="divide-y" id="cart-items">
                            @foreach($cartItems as $item)
                            <div class="p-4 cart-item" data-item-id="{{ $item->id }}">
                                <div class="flex items-start space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('products.show', $item->product->slug) }}">
                                            @if($item->product->images && $item->product->images->count() > 0)
                                                <img src="{{ product_image_url( $item->product->images->first()->image_path) }}" 
                                                    alt="{{ $item->product->name }}" 
                                                    class="w-20 h-20 md:w-24 md:h-24 object-contain rounded border">
                                            @else
                                                <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-100 flex items-center justify-center rounded border">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </a>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col md:flex-row md:justify-between">
                                            <!-- Product Info -->
                                            <div class="flex-1">
                                                <a href="{{ route('products.show', $item->product->slug) }}" 
                                                   class="font-medium text-gray-900 hover:text-blue-600 line-clamp-2">
                                                    {{ $item->product->name }}
                                                </a>
                                                @if($item->product->categories->count() > 0)
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ $item->product->categories->first()->name }}
                                                </p>
                                                @endif
                                                
                                                <!-- Mobile Price -->
                                                <div class="md:hidden mt-2">
                                                    <span class="text-lg font-bold text-gray-900 item-price">
                                                        {{ format_currency($item->price * $item->quantity) }}
                                                    </span>
                                                    <span class="text-sm text-gray-500 ml-2">
                                                        ({{ format_currency($item->price) }} each)
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Desktop Price -->
                                            <div class="hidden md:block text-right">
                                                <span class="text-lg font-bold text-gray-900 item-price">
                                                    {{ format_currency($item->price * $item->quantity) }}
                                                </span>
                                                <p class="text-sm text-gray-500">
                                                    {{ format_currency($item->price) }} each
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="flex items-center justify-between mt-3">
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm text-gray-600">Quantity:</span>
                                                <div class="flex items-center border rounded">
                                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                                            class="px-3 py-1 hover:bg-gray-100 {{ $item->quantity <= 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-600' }}"
                                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                        -
                                                    </button>
                                                    <span class="px-3 py-1 border-x text-center min-w-[3rem] quantity-display">{{ $item->quantity }}</span>
                                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                                            class="px-3 py-1 hover:bg-gray-100 text-gray-600"
                                                            {{ $item->quantity >= 10 ? 'disabled' : '' }}>
                                                        +
                                                    </button>
                                                </div>
                                            </div>

                                            <button onclick="removeItem({{ $item->id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-trash mr-1"></i>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-4 sticky top-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium" id="cart-subtotal">{{ format_currency($cart->total_price) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium text-gray-500 text-sm">At checkout (by pincode)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-medium">₹0</span>
                            </div>
                            <hr>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold">Total</span>
                                <span class="text-xl font-bold text-blue-600" id="cart-total">{{ format_currency($cart->total_price) }}</span>
                                <p class="text-xs text-gray-500 mt-1">+ shipping calculated at checkout</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            @auth('customer')
                                <a href="{{ route('checkout.index') }}" class="w-full bg-blue-600 text-white py-3 rounded font-semibold hover:bg-blue-700 transition-colors text-center block">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Proceed to Checkout
                                </a>
                            @else
                                <a href="{{ route('login', ['redirect' => route('checkout.index')]) }}" class="w-full bg-blue-600 text-white py-3 rounded font-semibold hover:bg-blue-700 transition-colors text-center block">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login to Checkout
                                </a>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 mb-2">Don't have an account?</p>
                                    <a href="{{ route('register', ['redirect' => route('checkout.index')]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Create Account for Faster Checkout
                                    </a>
                                </div>
                            @endauth
                            <a href="{{ route('products.index') }}" 
                               class="w-full border border-gray-300 text-gray-700 py-3 rounded font-semibold hover:bg-gray-50 transition-colors text-center block">
                                <i class="fas fa-shopping-bag mr-2"></i>
                                Continue Shopping
                            </a>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="mt-6 space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                Secure checkout
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="text-center py-12">
                <div class="bg-white rounded-lg shadow-sm p-8 max-w-md mx-auto">
                    <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
                    <p class="text-gray-600 mb-6">Start shopping to add items to your cart.</p>
                    <a href="{{ route('products.index') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded font-semibold hover:bg-blue-700 transition-colors inline-block">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Start Shopping
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Success/Error Messages -->
    <div id="message-container" class="fixed top-4 right-4 z-50"></div>

    <script>
        // Set up CSRF token for AJAX requests
        function getCSRFToken() {
            const tokenElement = document.querySelector('meta[name="csrf-token"]');
            if (!tokenElement) {
                console.error('CSRF token meta tag not found');
                return null;
            }
            return tokenElement.getAttribute('content');
        }

        function showMessage(message, type = 'success') {
            const container = document.getElementById('message-container');
            const messageDiv = document.createElement('div');
            messageDiv.className = `alert alert-${type} bg-${type === 'success' ? 'green' : 'red'}-100 border border-${type === 'success' ? 'green' : 'red'}-400 text-${type === 'success' ? 'green' : 'red'}-700 px-4 py-3 rounded mb-2 shadow-lg`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-${type === 'success' ? 'green' : 'red'}-700 hover:text-${type === 'success' ? 'green' : 'red'}-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(messageDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 5000);
        }

        function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1 || newQuantity > 10) return;

            const token = getCSRFToken();
            if (!token) {
                showMessage('Security token error. Please refresh the page.', 'error');
                return;
            }

            fetch(`{{ url('/cart/update') }}/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    quantity: newQuantity
                })
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 419) {
                        throw new Error('Security token mismatch. Please refresh the page.');
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
                        .then(data => {
                if (data.success) {
                    // Update quantity display
                    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
                    if (cartItem) {
                        cartItem.querySelector('.quantity-display').textContent = newQuantity;
                        cartItem.querySelector('.item-price').textContent = `₹${data.item_total}`;
                        
                        // Update cart totals
                        const subtotalElement = document.getElementById('cart-subtotal');
                        const totalElement = document.getElementById('cart-total');
                        if (subtotalElement) subtotalElement.textContent = `₹${data.cart_total}`;
                        // total should include shipping fee when items exist
                        if (totalElement) totalElement.textContent = `₹${data.cart_total_with_shipping}`;
                        
                        // Update cart counter in header if exists
                        const cartCountElement = document.getElementById('cart-count');
                        if (cartCountElement) cartCountElement.textContent = data.cart_count;
                    }
                    
                    showMessage(data.message);
                } else {
                    showMessage(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage(error.message || 'An error occurred. Please try again.', 'error');
            });
        }

        function removeItem(itemId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) return;

            const token = getCSRFToken();
            if (!token) {
                showMessage('Security token error. Please refresh the page.', 'error');
                return;
            }

            fetch(`{{ url('/cart/remove') }}/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 419) {
                        throw new Error('Security token mismatch. Please refresh the page.');
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
                    if (cartItem) {
                        cartItem.remove();
                    }
                    
                    // Update cart totals
                    const subtotalElement = document.getElementById('cart-subtotal');
                    const totalElement = document.getElementById('cart-total');
                    if (subtotalElement) subtotalElement.textContent = `₹${data.cart_total}`;
                    if (totalElement) totalElement.textContent = `₹${data.cart_total_with_shipping}`;
                    
                    // Update cart counter in header if exists
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) cartCountElement.textContent = data.cart_count;
                    
                    // Check if cart is empty
                    const cartItems = document.getElementById('cart-items');
                    if (cartItems && !cartItems.children.length) {
                        location.reload(); // Reload to show empty cart message
                    }
                    
                    showMessage(data.message);
                } else {
                    showMessage(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage(error.message || 'An error occurred. Please try again.', 'error');
            });
        }

        function clearCart() {
            if (!confirm('Are you sure you want to clear your entire cart?')) return;

            const token = getCSRFToken();
            if (!token) {
                showMessage('Security token error. Please refresh the page.', 'error');
                return;
            }

            fetch('{{ route("cart.clear") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 419) {
                        throw new Error('Security token mismatch. Please refresh the page.');
                    }
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        location.reload(); // Reload to show empty cart message
                    }, 1000);
                } else {
                    showMessage(data.message || 'Failed to clear cart', 'error');
                }
            })
            .catch(error => {
                console.error('Clear cart error:', error);
                showMessage('An error occurred while clearing the cart: ' + error.message, 'error');
            });
        }
    </script>
</body>
</html>