<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - {{ $storeName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <!-- Header Navigation -->
    <div class="bg-white shadow-sm border-b mb-4">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl md:text-2xl font-bold text-blue-600 mr-4">
                        <span class="hidden sm:inline">{{ $storeName }}</span>
                        <span class="sm:hidden">{{ substr($storeName, 0, 2) }}</span>
                    </a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-2 md:space-x-6">
                    <!-- Back Button -->
                    <a href="{{ route('products.index') }}" class="flex flex-col items-center text-gray-700 hover:text-blue-600">
                        <i class="fas fa-arrow-left text-lg"></i>
                        <span class="text-xs hidden sm:inline">Back</span>
                    </a>

                    <a href="{{ route('cart.index') }}" class="flex flex-col items-center text-gray-700 hover:text-blue-600 relative">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span class="text-xs hidden sm:inline">Cart</span>
                        <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center cart-count">0</span>
                    </a>

                    <a href="{{ route('wishlist.index') }}" class="flex flex-col items-center text-gray-700 hover:text-blue-600 relative">
                        <i class="fas fa-heart text-lg text-red-500"></i>
                        <span class="text-xs hidden sm:inline">Wishlist</span>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center wishlist-count">{{ $wishlistItems->count() }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 mb-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
            <i class="fas fa-chevron-right mx-2"></i>
            <span class="text-gray-800 font-medium">Wishlist</span>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 pb-8">
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Header -->
            <div class="border-b border-gray-200 p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">My Wishlist</h1>
                        <p class="text-gray-600 mt-1">{{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }}</p>
                    </div>
                    @if($wishlistItems->count() > 0)
                    <button onclick="clearWishlist()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">
                        <i class="fas fa-trash mr-2"></i>Clear All
                    </button>
                    @endif
                </div>
            </div>

            <!-- Wishlist Items -->
            <div class="p-6">
                @if($wishlistItems->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($wishlistItems as $item)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow group">
                            <!-- Product Image -->
                            <div class="relative aspect-square bg-gray-100 overflow-hidden">
                                @if($item->product->images && $item->product->images->count() > 0)
                                    <img src="{{ asset('public/storage/' . $item->product->images->first()->image_path) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                    </div>
                                @endif

                                <!-- Remove Button -->
                                <button onclick="removeFromWishlist({{ $item->id }})" 
                                        class="absolute top-2 right-2 bg-white text-red-500 p-2 rounded-full shadow-md hover:bg-red-50 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- Discount Badge -->
                                @if($item->product->special_price && $item->product->special_price < $item->product->price)
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">
                                            {{ round((($item->product->price - $item->product->special_price) / $item->product->price) * 100) }}% OFF
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2 line-clamp-2">
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-blue-600">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-2">{{ $item->product->categories->first()?->name ?? 'Uncategorized' }}</p>
                                
                                <div class="flex items-center justify-between mb-4">
                                    @if($item->product->special_price && $item->product->special_price < $item->product->price)
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xl font-bold text-red-600">{{ format_currency($item->product->special_price) }}</span>
                                            <span class="text-sm text-gray-500 line-through">{{ format_currency($item->product->price) }}</span>
                                        </div>
                                    @else
                                        <span class="text-xl font-bold text-blue-600">{{ format_currency($item->product->price) }}</span>
                                    @endif
                                </div>

                                <div class="flex space-x-2">
                                    <button onclick="addToCart({{ $item->product->id }})" 
                                            class="flex-1 bg-blue-600 text-white py-2 px-3 rounded-lg hover:bg-blue-700 transition duration-300 text-sm">
                                        <i class="fas fa-shopping-cart mr-1"></i>Add to Cart
                                    </button>
                                    <a href="{{ route('products.show', $item->product->slug) }}" 
                                       class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition duration-300 text-sm text-center">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty Wishlist -->
                    <div class="text-center py-12">
                        <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Your wishlist is empty</h3>
                        <p class="text-gray-500 mb-6">Save items that you're interested in to your wishlist</p>
                        <a href="{{ route('products.index') }}" 
                           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">
                            <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Set up CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function addToCart(productId) {
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Adding...';

            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Update cart count
                    const cartCountElements = document.querySelectorAll('.cart-count');
                    cartCountElements.forEach(el => {
                        el.textContent = data.cart_count || 0;
                    });
                } else {
                    showMessage(data.message || 'Failed to add product to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred', 'error');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = originalContent;
            });
        }

        function removeFromWishlist(wishlistId) {
            if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
                return;
            }

            fetch(`{{ url('/wishlist/remove') }}/${wishlistId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Remove the item from the page
                    event.target.closest('.group').remove();
                    // Update wishlist count
                    const wishlistCountElements = document.querySelectorAll('.wishlist-count');
                    wishlistCountElements.forEach(el => {
                        el.textContent = data.wishlist_count || 0;
                    });
                    // Reload page if no items left
                    if (data.wishlist_count === 0) {
                        setTimeout(() => location.reload(), 1500);
                    }
                } else {
                    showMessage(data.message || 'Failed to remove item', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred', 'error');
            });
        }

        function clearWishlist() {
            if (!confirm('Are you sure you want to clear your entire wishlist?')) {
                return;
            }

            fetch('{{ route("wishlist.clear") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(data.message || 'Failed to clear wishlist', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred', 'error');
            });
        }

        function showMessage(message, type = 'success') {
            let container = document.getElementById('message-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'message-container';
                container.className = 'fixed top-4 right-4 z-50';
                document.body.appendChild(container);
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = `alert bg-${type === 'success' ? 'green' : 'red'}-100 border border-${type === 'success' ? 'green' : 'red'}-400 text-${type === 'success' ? 'green' : 'red'}-700 px-4 py-3 rounded mb-2 shadow-lg max-w-sm`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                    <span class="text-sm">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-${type === 'success' ? 'green' : 'red'}-700 hover:text-${type === 'success' ? 'green' : 'red'}-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(messageDiv);
            
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>