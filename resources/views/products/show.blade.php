<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .product-image-zoom:hover img {
            transform: scale(1.1);
        }
        .product-thumbnail:hover {
            border-color: #2563eb;
        }
        .breadcrumb-item:not(:last-child)::after {
            content: '>';
            margin: 0 8px;
            color: #6b7280;
        }
        
        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .swiper-button-next, .swiper-button-prev { display: none !important; }
            .mobile-sticky-buttons {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                border-top: 1px solid #e5e7eb;
                z-index: 50;
            }
            .product-content { margin-bottom: 80px; }
        }
        
        /* Image gallery improvements - Flipkart Style */
        .image-gallery-container {
            display: flex;
            gap: 16px;
        }
        
        .thumbnail-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 60px;
        }
        
        .thumbnail-item {
            width: 60px;
            height: 60px;
            border: 2px solid #e5e7eb;
            border-radius: 4px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .thumbnail-item:hover,
        .thumbnail-item.active {
            border-color: #2563eb;
        }
        
        .thumbnail-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .main-image-container {
            flex: 1;
            position: relative;
            background: #f9fafb;
            border-radius: 8px;
            overflow: hidden;
            cursor: zoom-in;
        }
        
        .main-image {
            height: 500px;
            width: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        /* Zoom functionality */
        .zoom-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: zoom-out;
        }
        
        .zoomed-image {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .zoom-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        
        .zoom-btn {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }
        
        .zoom-btn:hover {
            background: white;
        }
        
        @media (max-width: 768px) {
            .image-gallery-container {
                flex-direction: column-reverse;
            }
            
            .thumbnail-list {
                flex-direction: row;
                width: 100%;
                overflow-x: auto;
                padding: 8px 0;
            }
            
            .thumbnail-item {
                flex-shrink: 0;
            }
            
            .main-image {
                height: 350px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header Navigation -->
    <div class="bg-white shadow-sm border-b mb-4">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl md:text-2xl font-bold text-blue-600 mr-4">
                        <span class="hidden sm:inline">{{ config('app.name', 'Laravel') }}</span>
                        <span class="sm:hidden">{{ substr(config('app.name', 'Laravel'), 0, 2) }}</span>
                    </a>
                </div>

                <!-- Search Bar - Hidden on small screens -->
                <div class="hidden md:flex flex-1 max-w-2xl mx-8">
                    <form action="{{ route('products.index') }}" method="GET" class="relative w-full">
                        <input type="text" name="search"
                            placeholder="Search for products, brands and more"
                            class="w-full px-4 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-blue-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-2 md:space-x-6">
                    <!-- Mobile Back Button -->
                    <a href="{{ route('home') }}" class="md:hidden flex flex-col items-center text-gray-700 hover:text-blue-600">
                        <i class="fas fa-arrow-left text-lg"></i>
                        <span class="text-xs">Back</span>
                    </a>

                    <a href="{{ route('wishlist.index') }}" class="flex flex-col items-center text-gray-700 hover:text-blue-600 relative">
                        <i class="fas fa-heart text-lg"></i>
                        <span class="text-xs hidden sm:inline">Wishlist</span>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center wishlist-count">0</span>
                    </a>

                    <a href="{{ route('cart.index') }}" class="flex flex-col items-center text-gray-700 hover:text-blue-600 relative">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span class="text-xs hidden sm:inline">Cart</span>
                        <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center cart-count" style="display: none;">0</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="container mx-auto px-3 md:px-4 mb-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="breadcrumb-item hover:text-blue-600">Home</a>
            <a href="{{ route('products.index') }}" class="breadcrumb-item hover:text-blue-600">Products</a>
            @if($product->categories->count() > 0)
            <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}" class="breadcrumb-item hover:text-blue-600">
                {{ $product->categories->first()->name }}
            </a>
            @endif
            <span class="breadcrumb-item text-gray-800 font-medium">{{ Str::limit($product->name, 30) }}</span>
        </nav>
    </div>

    <!-- Product Detail Content -->
    <div class="container mx-auto px-3 md:px-4 product-content">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <!-- Product Images -->
            <div class="space-y-4">
                <!-- Image Gallery - Flipkart Style -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="image-gallery-container">
                        <!-- Thumbnail list -->
                        <div class="thumbnail-list">
                            @php
                                $images = collect();
                                
                                // Add main product image first
                                if($product->image) {
                                    $images->push([
                                        'thumb' => asset('public/storage/' . $product->image),
                                        'full' => asset('public/storage/' . $product->image),
                                        'alt' => $product->name . ' - Main Image'
                                    ]);
                                } elseif($product->images && $product->images->count() > 0) {
                                    $images->push([
                                        'thumb' => asset('public/storage/' . $product->images->first()->image_path),
                                        'full' => asset('public/storage/' . $product->images->first()->image_path),
                                        'alt' => $product->name . ' - Main Image'
                                    ]);
                                }
                                
                                // Add additional product images
                                if($product->images) {
                                    $imageIndex = 2; // Start from 2 since main image is 1
                                    foreach($product->images->skip(1)->take(3) as $img) {
                                        $images->push([
                                            'thumb' => asset('public/storage/' . $img->image_path),
                                            'full' => asset('public/storage/' . $img->image_path),
                                            'alt' => $product->name . ' - Image ' . $imageIndex
                                        ]);
                                        $imageIndex++;
                                    }
                                }
                                
                                // Fill remaining slots with main image if needed (to show 4 images total)
                                while($images->count() < 4 && $images->count() > 0) {
                                    $images->push($images->first());
                                }
                            @endphp
                            
                            @if($images->count() > 0)
                                @foreach($images->take(4) as $index => $image)
                                    <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" 
                                         onclick="changeMainImage('{{ $image['full'] }}', '{{ $image['alt'] }}', this)" 
                                         data-image="{{ $image['full'] }}">
                                        <img src="{{ $image['thumb'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <!-- Main image container -->
                        <div class="main-image-container" onclick="openZoom()">
                            @if($images->count() > 0)
                                <img id="mainProductImage" 
                                     class="main-image" 
                                     src="{{ $images->first()['full'] }}" 
                                     alt="{{ $images->first()['alt'] }}" 
                                     loading="eager">
                            @else
                                <div class="main-image flex items-center justify-center bg-gray-100">
                                    <i class="fas fa-image text-6xl text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mobile Action Buttons -->
                <div class="mobile-sticky-buttons md:hidden">
                    <div class="flex p-3 space-x-3">
                        <button onclick="addToCart({{ $product->id }})" class="flex-1 bg-blue-600 text-white py-3 rounded font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Add to Cart
                        </button>
                        <button onclick="buyNow({{ $product->id }})" class="flex-1 bg-orange-600 text-white py-3 rounded font-semibold hover:bg-orange-700 transition-colors">
                            <i class="fas fa-bolt mr-2"></i>
                            Buy Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Zoom Overlay -->
            <div id="zoomOverlay" class="zoom-overlay" onclick="closeZoom()">
                <div class="zoom-controls">
                    <button class="zoom-btn" onclick="event.stopPropagation(); zoomIn()">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="zoom-btn" onclick="event.stopPropagation(); zoomOut()">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button class="zoom-btn" onclick="closeZoom()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <img id="zoomedImage" class="zoomed-image" src="" alt="">
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <!-- Product Title -->
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    <div class="flex items-center space-x-4 text-sm">
                        @if($product->categories->count() > 0)
                        <span class="text-blue-600 font-medium">{{ $product->categories->first()->name }}</span>
                        @endif
                        <div class="flex items-center">
                            <div class="flex text-yellow-400 text-sm mr-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="text-gray-600">(4.5) • {{ rand(50, 500) }} reviews</span>
                        </div>
                    </div>
                </div>

                <!-- Price -->
                <div class="border-b pb-4">
                    <div class="flex items-center space-x-4">
                        @if($product->special_price && $product->special_price < $product->price)
                            <span class="text-3xl md:text-4xl font-bold text-gray-900">₹{{ number_format($product->special_price, 0) }}</span>
                            <span class="text-xl text-gray-500 line-through">₹{{ number_format($product->price, 0) }}</span>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ round((($product->price - $product->special_price) / $product->price) * 100) }}% OFF
                            </span>
                        @else
                            <span class="text-3xl md:text-4xl font-bold text-gray-900">₹{{ number_format($product->price, 0) }}</span>
                        @endif
                    </div>
                    <p class="text-green-600 font-semibold mt-2">
                        <i class="fas fa-truck mr-1"></i>
                        Free delivery • Return policy
                    </p>
                </div>

                <!-- Product Description -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Product Details</h3>
                    @if($product->short_description)
                        <p class="text-gray-700 mb-4">{{ $product->short_description }}</p>
                    @endif
                    @if($product->description)
                        <div class="text-gray-700 space-y-2">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    @endif
                </div>

                <!-- Key Features -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Key Highlights</h4>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Free shipping on orders above ₹499
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            7-day return policy
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Cash on delivery available
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Warranty included
                        </li>
                    </ul>
                </div>

                <!-- Desktop Action Buttons -->
                <div class="hidden md:block space-y-3">
                    <div class="flex space-x-4">
                        <button onclick="addToCart({{ $product->id }})" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Add to Cart
                        </button>
                        <button onclick="buyNow({{ $product->id }})" class="flex-1 bg-orange-600 text-white py-3 px-6 rounded font-semibold hover:bg-orange-700 transition-colors">
                            <i class="fas fa-bolt mr-2"></i>
                            Buy Now
                        </button>
                    </div>
                    <button onclick="addToWishlist({{ $product->id }})" class="w-full border border-gray-300 text-gray-700 py-3 px-6 rounded font-semibold hover:bg-gray-50 transition-colors">
                        <i class="fas fa-heart mr-2"></i>
                        Add to Wishlist
                    </button>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts && $relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="block">
                        <div class="aspect-square bg-gray-100 relative overflow-hidden">
                            @if($relatedProduct->images && $relatedProduct->images->count() > 0)
                                <img src="{{ asset('public/storage/' . $relatedProduct->images->first()->image_path) }}" 
                                    alt="{{ $relatedProduct->name }}" 
                                    class="w-full h-full object-contain hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-2xl text-gray-400"></i>
                                </div>
                            @endif
                            @if($relatedProduct->special_price && $relatedProduct->special_price < $relatedProduct->price)
                                <div class="absolute top-2 left-2">
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">
                                        {{ round((($relatedProduct->price - $relatedProduct->special_price) / $relatedProduct->price) * 100) }}% OFF
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="font-medium text-sm text-gray-800 mb-2 line-clamp-2">
                                {{ Str::limit($relatedProduct->name, 50) }}
                            </h3>
                            <div class="flex items-center space-x-2">
                                @if($relatedProduct->special_price && $relatedProduct->special_price < $relatedProduct->price)
                                    <span class="text-lg font-bold text-gray-900">₹{{ number_format($relatedProduct->special_price, 0) }}</span>
                                    <span class="text-sm text-gray-500 line-through">₹{{ number_format($relatedProduct->price, 0) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">₹{{ number_format($relatedProduct->price, 0) }}</span>
                                @endif
                            </div>
                            <div class="flex text-yellow-400 text-xs mt-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <script>
        // Set up CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Debug: Log token on page load
        console.log('CSRF Token loaded:', token);

        // Image gallery functions
        function changeMainImage(imageSrc, alt, thumbnail) {
            const mainImage = document.getElementById('mainProductImage');
            if (mainImage) {
                mainImage.src = imageSrc;
                mainImage.alt = alt;
            }
            
            // Update thumbnail active state
            document.querySelectorAll('.thumbnail-item').forEach(thumb => {
                thumb.classList.remove('active');
            });
            thumbnail.classList.add('active');
        }

        // Zoom functionality
        let currentZoomLevel = 1;
        
        function openZoom() {
            const mainImage = document.getElementById('mainProductImage');
            const zoomOverlay = document.getElementById('zoomOverlay');
            const zoomedImage = document.getElementById('zoomedImage');
            
            if (mainImage && zoomOverlay && zoomedImage) {
                zoomedImage.src = mainImage.src;
                zoomedImage.alt = mainImage.alt;
                currentZoomLevel = 1;
                zoomedImage.style.transform = `scale(${currentZoomLevel})`;
                zoomOverlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeZoom() {
            const zoomOverlay = document.getElementById('zoomOverlay');
            if (zoomOverlay) {
                zoomOverlay.style.display = 'none';
                document.body.style.overflow = 'auto';
                currentZoomLevel = 1;
            }
        }
        
        function zoomIn() {
            if (currentZoomLevel < 3) {
                currentZoomLevel += 0.5;
                const zoomedImage = document.getElementById('zoomedImage');
                if (zoomedImage) {
                    zoomedImage.style.transform = `scale(${currentZoomLevel})`;
                }
            }
        }
        
        function zoomOut() {
            if (currentZoomLevel > 0.5) {
                currentZoomLevel -= 0.5;
                const zoomedImage = document.getElementById('zoomedImage');
                if (zoomedImage) {
                    zoomedImage.style.transform = `scale(${currentZoomLevel})`;
                }
            }
        }
        
        // Close zoom on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeZoom();
            }
        });

        function addToCart(productId, quantity = 1) {
            console.log('AddToCart called with:', { productId, quantity, token });
            
            // Disable button temporarily
            const buttons = document.querySelectorAll('[onclick*="addToCart"]');
            buttons.forEach(btn => {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
            });

            const requestData = {
                product_id: productId,
                quantity: quantity
            };
            
            console.log('Making request to {{ route("cart.add") }} with:', requestData);

            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response received:', { 
                    status: response.status, 
                    statusText: response.statusText,
                    ok: response.ok 
                });
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error response body:', text);
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Success response data:', data);
                
                if (data.success) {
                    showMessage(data.message, 'success');
                    
                    // Update cart count in header if exists
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
                } else {
                    console.error('Server returned success=false:', data);
                    showMessage(data.message || 'Failed to add product to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showMessage('An error occurred: ' + error.message, 'error');
            })
            .finally(() => {
                // Re-enable buttons
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Add to Cart';
                });
            });
        }

        function buyNow(productId, quantity = 1) {
            // Find the specific button that was clicked
            const clickedButton = event.target.closest('button');
            const originalContent = clickedButton.innerHTML;
            
            // Disable button temporarily
            clickedButton.disabled = true;
            clickedButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

            // Add to cart first, then redirect to cart/checkout
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
                    // Show brief success message
                    showMessage('Product added to cart! Redirecting to checkout...', 'success');
                    
                    // Redirect to checkout page for "Buy Now" experience
                    setTimeout(() => {
                        window.location.href = '{{ route("checkout.index") }}';
                    }, 1500);
                } else {
                    console.error('Server returned success=false:', data);
                    showMessage(data.message || 'Failed to add product to cart', 'error');
                    // Re-enable button on error
                    clickedButton.disabled = false;
                    clickedButton.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Buy Now error:', error);
                showMessage('An error occurred: ' + error.message, 'error');
                // Re-enable button on error
                clickedButton.disabled = false;
                clickedButton.innerHTML = originalContent;
            });
        }

        function addToWishlist(productId) {
            // Find the specific button that was clicked
            const clickedButton = event.target.closest('button');
            const originalContent = clickedButton.innerHTML;
            
            // Disable button temporarily
            clickedButton.disabled = true;
            clickedButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';

            fetch('{{ route("wishlist.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    product_id: productId
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
                    
                    // Change button to indicate it's added
                    clickedButton.innerHTML = '<i class="fas fa-heart text-red-500 mr-2"></i>Added to Wishlist';
                    clickedButton.classList.add('border-red-500', 'text-red-500');
                    
                    // Update wishlist count if elements exist
                    const wishlistCountElements = document.querySelectorAll('.wishlist-count');
                    wishlistCountElements.forEach(el => {
                        el.textContent = data.wishlist_count || 0;
                        // Show/hide badge based on count
                        if (data.wishlist_count > 0) {
                            el.style.display = 'flex';
                        } else {
                            el.style.display = 'none';
                        }
                    });
                } else {
                    console.error('Server returned success=false:', data);
                    showMessage(data.message || 'Failed to add product to wishlist', 'error');
                    // Re-enable button on error
                    clickedButton.disabled = false;
                    clickedButton.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Wishlist error:', error);
                showMessage('An error occurred: ' + error.message, 'error');
                // Re-enable button on error
                clickedButton.disabled = false;
                clickedButton.innerHTML = originalContent;
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

        // Set first thumbnail as active by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstThumbnail = document.querySelector('.product-thumbnail');
            if (firstThumbnail) {
                firstThumbnail.classList.remove('border-transparent');
                firstThumbnail.classList.add('border-blue-500');
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