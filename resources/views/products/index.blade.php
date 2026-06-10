<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <!-- Top Bar -->
            <div class="py-2 border-b border-gray-200">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <div></div>
                    <div class="flex items-center space-x-4">
                        @guest('customer')
                            <a href="{{ route('login') }}" class="hover:text-blue-600">Login</a>
                            <a href="{{ route('register') }}" class="hover:text-blue-600">Register</a>
                        @endguest
                        @auth('customer')
                            <div class="flex items-center space-x-4">
                                <span>Welcome, {{ auth('customer')->user()->first_name }}!</span>
                                <a href="{{ route('customer.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                                <form action="{{ route('customer.logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="hover:text-blue-600">Logout</button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Main Navigation -->
            <div class="py-4">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <div class="text-2xl font-bold text-blue-600">
                        <a href="{{ route('home') }}">{{ config('app.name', 'Laravel') }}</a>
                    </div>

                    <!-- Search Bar -->
                    <div class="flex-1 max-w-lg mx-8">
                        <form action="{{ route('products.index') }}" method="GET" class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Cart & Wishlist -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('wishlist.index') }}" class="relative hover:text-blue-600">
                            <i class="fas fa-heart text-xl"></i>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center wishlist-count">0</span>
                        </a>
                        <a href="{{ route('cart.index') }}" class="relative hover:text-blue-600">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <span class="absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center cart-count" style="display: none;">0</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="bg-gray-100 py-4">
        <div class="container mx-auto px-4">
            <nav class="text-sm">
                <ol class="list-none p-0 inline-flex">
                    <li class="flex items-center">
                        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Home</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.476 239.03c9.373 9.372 9.373 24.568 0 33.941z"/>
                        </svg>
                    </li>
                    <li>
                        <span class="text-gray-500">Products</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-wrap -mx-4">
            <!-- Sidebar Filters -->
            <div class="w-full lg:w-1/4 px-4 mb-8">
                <!-- Categories Filter -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('products.index') }}" 
                               class="flex items-center justify-between py-2 px-3 rounded hover:bg-gray-100 {{ !request('category') ? 'bg-blue-100 text-blue-600' : 'text-gray-700' }}">
                                All Products
                                <span class="text-sm text-gray-500">{{ $products->total() }}</span>
                            </a>
                        </li>
                        @foreach($categories as $category)
                        <li>
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                               class="flex items-center justify-between py-2 px-3 rounded hover:bg-gray-100 {{ request('category') == $category->slug ? 'bg-blue-100 text-blue-600' : 'text-gray-700' }}">
                                {{ $category->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Price Filter -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Price Range</h3>
                    
                    <!-- Predefined Price Ranges -->
                    <form id="priceFilterForm" method="GET" action="{{ route('products.index') }}">
                        <!-- Preserve other query parameters -->
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        
                        <div class="space-y-2 mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="price_ranges[]" value="under_50" class="mr-2 price-range-checkbox" 
                                       {{ in_array('under_50', request('price_ranges', [])) ? 'checked' : '' }}> 
                                Under ₹50
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="price_ranges[]" value="50_100" class="mr-2 price-range-checkbox"
                                       {{ in_array('50_100', request('price_ranges', [])) ? 'checked' : '' }}> 
                                ₹50 - ₹100
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="price_ranges[]" value="100_200" class="mr-2 price-range-checkbox"
                                       {{ in_array('100_200', request('price_ranges', [])) ? 'checked' : '' }}> 
                                ₹100 - ₹200
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="price_ranges[]" value="over_200" class="mr-2 price-range-checkbox"
                                       {{ in_array('over_200', request('price_ranges', [])) ? 'checked' : '' }}> 
                                Over ₹200
                            </label>
                        </div>
                        
                        <!-- Custom Price Range -->
                        <div class="border-t pt-4">
                            <h4 class="font-medium mb-2">Custom Range</h4>
                            <div class="flex space-x-2">
                                <div class="flex-1">
                                    <input type="number" name="min_price" placeholder="Min" 
                                           value="{{ request('min_price') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="flex-1">
                                    <input type="number" name="max_price" placeholder="Max" 
                                           value="{{ request('max_price') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filter Actions -->
                        <div class="mt-4 space-y-2">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">
                                Apply Filters
                            </button>
                            @if(request()->hasAny(['price_ranges', 'min_price', 'max_price']))
                                <a href="{{ route('products.index', array_filter(request()->except(['price_ranges', 'min_price', 'max_price']))) }}" 
                                   class="block w-full bg-gray-200 text-gray-700 py-2 rounded-md hover:bg-gray-300 transition duration-300 text-center">
                                    Clear Price Filters
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="w-full lg:w-3/4 px-4">
                <!-- Toolbar -->
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <div class="mb-4 lg:mb-0">
                        <h1 class="text-2xl font-bold">
                            @if(request('search'))
                                Search results for "{{ request('search') }}"
                            @elseif(request('category'))
                                {{ $categories->where('slug', request('category'))->first()->name ?? 'Products' }}
                            @else
                                All Products
                            @endif
                        </h1>
                        <p class="text-gray-600">{{ $products->total() }} products found</p>
                        
                        <!-- Active Filters -->
                        @if(request()->hasAny(['price_ranges', 'min_price', 'max_price', 'category', 'search']))
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="text-sm text-gray-500">Active filters:</span>
                                
                                @if(request('search'))
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                        Search: "{{ request('search') }}"
                                    </span>
                                @endif
                                
                                @if(request('category'))
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                        Category: {{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}
                                    </span>
                                @endif
                                
                                @if(request('price_ranges'))
                                    @foreach(request('price_ranges') as $range)
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">
                                            @switch($range)
                                                @case('under_50') Under ₹50 @break
                                                @case('50_100') ₹50 - ₹100 @break
                                                @case('100_200') ₹100 - ₹200 @break
                                                @case('over_200') Over ₹200 @break
                                            @endswitch
                                        </span>
                                    @endforeach
                                @endif
                                
                                @if(request('min_price') || request('max_price'))
                                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">
                                        Price: ₹{{ request('min_price', '0') }} - ₹{{ request('max_price', '∞') }}
                                    </span>
                                @endif
                                
                                <a href="{{ route('products.index') }}" class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs hover:bg-red-200 transition-colors">
                                    Clear all
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Sort Options -->
                    <div class="flex items-center space-x-4">
                        <label for="sort" class="text-sm text-gray-700">Sort by:</label>
                        <select id="sort" name="sort" onchange="updateSort(this.value)" 
                                class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition duration-300">
                        <div class="relative">
                            @if($product->images->count() > 0)
                                <img src="{{ asset('public/storage/' . $product->images->first()->image_path) }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            
                            @if($product->featured)
                                <div class="absolute top-2 left-2">
                                    <span class="bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">Featured</span>
                                </div>
                            @endif
                            
                            @if($product->special_price && $product->special_price < $product->price)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold">
                                        {{ round((($product->price - $product->special_price) / $product->price) * 100) }}% OFF
                                    </span>
                                </div>
                            @endif

                            <!-- Quick Actions -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition duration-300 flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition duration-300 space-x-2">
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="bg-white text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">
                                        <i class="fas fa-eye mr-2"></i>View
                                    </a>
                                    <button onclick="addToCart({{ $product->id }})" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                        <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mb-2">{{ $product->categories->first()?->name ?? 'Uncategorized' }}</p>
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->short_description }}</p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @if($product->special_price && $product->special_price < $product->price)
                                            <span class="text-xl font-bold text-red-600">{{ format_currency($product->special_price, 2) }}</span>
                                            <span class="text-sm text-gray-500 line-through">{{ format_currency($product->price, 2) }}</span>
                                    @else
                                            <span class="text-xl font-bold text-gray-900">{{ format_currency($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-1">
                                    <button class="p-2 rounded-full hover:bg-gray-100 text-gray-400 hover:text-red-500 transition duration-300">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <div class="max-w-md mx-auto">
                        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No products found</h3>
                        <p class="text-gray-500 mb-4">
                            @if(request('search'))
                                No products match your search criteria. Try different keywords.
                            @else
                                There are no products available in this category.
                            @endif
                        </p>
                        <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                            View All Products
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Set up CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        function updateSort(sortValue) {
            const url = new URL(window.location);
            url.searchParams.set('sort', sortValue);
            window.location.href = url.toString();
        }

        function addToCart(productId, quantity = 1) {
            // Find the specific button that was clicked
            const clickedButton = event.target.closest('button');
            const originalContent = clickedButton.innerHTML;
            
            // Disable button temporarily
            clickedButton.disabled = true;
            clickedButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';

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
                    quantity: quantity
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    
                    // Update cart count in header
                    const cartCountElements = document.querySelectorAll('.cart-count');
                    cartCountElements.forEach(el => {
                        el.textContent = data.cart_count || 0;
                        // Show/hide badge based on count
                        if (data.cart_count > 0) {
                            el.style.display = 'flex';
                        } else {
                            el.style.display = 'none';
                        }
                    });
                    
                    // Change button text temporarily
                    clickedButton.innerHTML = '<i class="fas fa-check mr-2"></i>Added!';
                    clickedButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    clickedButton.classList.add('bg-green-600');
                    
                    // Revert button after 2 seconds
                    setTimeout(() => {
                        clickedButton.innerHTML = originalContent;
                        clickedButton.classList.remove('bg-green-600');
                        clickedButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    }, 2000);
                } else {
                    showMessage(data.message || 'Failed to add product to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred: ' + error.message, 'error');
            })
            .finally(() => {
                // Re-enable button
                clickedButton.disabled = false;
                if (clickedButton.innerHTML.includes('Adding...')) {
                    clickedButton.innerHTML = originalContent;
                }
            });
        }

        function showMessage(message, type = 'success') {
            // Create message container if it doesn't exist
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
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 5000);
        }

        // Load cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("cart.count") }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(el => {
                    el.textContent = data.count || 0;
                });
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
            });
        });

        // Price filter auto-submit functionality
        document.addEventListener('DOMContentLoaded', function() {
            const priceCheckboxes = document.querySelectorAll('.price-range-checkbox');
            const minPriceInput = document.querySelector('input[name="min_price"]');
            const maxPriceInput = document.querySelector('input[name="max_price"]');
            const priceForm = document.getElementById('priceFilterForm');
            
            // Auto-submit when checkboxes are changed
            priceCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Clear custom price range when using predefined ranges
                    if (this.checked && (minPriceInput.value || maxPriceInput.value)) {
                        minPriceInput.value = '';
                        maxPriceInput.value = '';
                    }
                    priceForm.submit();
                });
            });
            
            // Auto-submit when custom price range changes (with debounce)
            let priceTimeout;
            function handlePriceChange() {
                clearTimeout(priceTimeout);
                priceTimeout = setTimeout(() => {
                    // Clear checkboxes when using custom range
                    if (minPriceInput.value || maxPriceInput.value) {
                        priceCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                    }
                    priceForm.submit();
                }, 1000); // 1 second delay
            }
            
            if (minPriceInput) {
                minPriceInput.addEventListener('input', handlePriceChange);
            }
            if (maxPriceInput) {
                maxPriceInput.addEventListener('input', handlePriceChange);
            }
            
            // Load initial cart and wishlist counts
            loadInitialCounts();
        });
        
        // Function to load initial counts
        function loadInitialCounts() {
            // Load cart count
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const cartCountElements = document.querySelectorAll('.cart-count');
                    cartCountElements.forEach(el => {
                        el.textContent = data.count;
                        if (data.count > 0) {
                            el.style.display = 'flex';
                        } else {
                            el.style.display = 'none';
                        }
                    });
                })
                .catch(error => console.error('Error loading cart count:', error));
                
            // Load wishlist count
            fetch('{{ route("wishlist.count") }}')
                .then(response => response.json())
                .then(data => {
                    const wishlistCountElements = document.querySelectorAll('.wishlist-count');
                    wishlistCountElements.forEach(el => {
                        el.textContent = data.count;
                        if (data.count > 0) {
                            el.style.display = 'flex';
                        } else {
                            el.style.display = 'none';
                        }
                    });
                })
                .catch(error => console.error('Error loading wishlist count:', error));
        }
    </script>
</body>
</html>