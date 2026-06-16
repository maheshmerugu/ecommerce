<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $storeName }} - Online Shopping for Toy Cars & More</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .deal-card:hover {
            transform: translateY(-4px);
        }

        .category-icon {
            transition: all 0.3s ease;
        }

        .category-icon:hover {
            transform: scale(1.1);
        }

        .product-card {
            transition: all 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Account Dropdown Styles */
        .account-dropdown {
            transition: all 0.2s ease-in-out;
            transform: translateY(-10px);
            pointer-events: none;
        }

        .group:hover .account-dropdown,
        .account-dropdown:hover,
        .account-dropdown.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0);
            pointer-events: all;
        }

        .banner-slide {
            min-height: 280px;
        }
        
        /* Mobile specific improvements */
        @media (max-width: 768px) {
            .banner-slide { min-height: 200px; }
            .container { padding-left: 1rem; padding-right: 1rem; }
            .swiper-button-next, .swiper-button-prev { display: none !important; }
            .swiper-pagination-bullet { width: 8px; height: 8px; }
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            /* Hide elements on mobile for better space utilization */
            .mobile-hide { display: none; }
            /* Better touch targets */
            .touch-target { min-height: 44px; min-width: 44px; }
            /* Prevent horizontal scrolling */
            body { overflow-x: hidden; }
        }
        
        /* Tablet specific */
        @media (min-width: 768px) and (max-width: 1024px) {
            .banner-slide { min-height: 240px; }
        }
        
        /* Desktop specific */
        @media (min-width: 1024px) {
            .mobile-only { display: none; }
        }
        
        /* Ensure images don't overflow */
        img { max-width: 100%; height: auto; }
        
        /* Better scrolling on mobile */
        .swiper-container { padding: 0; }
        .swiper-wrapper { align-items: stretch; }
        
        /* Smooth transitions */
        * { 
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Main Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-3">
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button class="text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>

                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-lg md:text-2xl font-bold text-blue-600 mr-4 md:mr-8 whitespace-nowrap">
                        {{ $storeName }}
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
                    <!-- Mobile Search Icon -->
                    <button class="md:hidden flex flex-col items-center text-gray-700 hover:text-blue-600" onclick="toggleSearchBar()">
                        <i class="fas fa-search text-lg"></i>
                        <span class="text-xs">Search</span>
                    </button>

                    <!-- Account Dropdown -->
                    <div class="relative flex flex-col items-center text-gray-700 hover:text-blue-600 group">
                        <a href="#" class="flex flex-col items-center">
                            <i class="fas fa-user text-lg"></i>
                            <span class="text-xs hidden sm:inline">Account</span>
                        </a>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible account-dropdown z-50">
                            <div class="py-2">
                                @guest('customer')
                                <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                                </a>
                                <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600">
                                    <i class="fas fa-user-plus mr-2"></i>Sign Up
                                </a>
                                @endguest
                                
                                @auth('customer')
                                <div class="px-4 py-2 text-sm text-gray-500 border-b border-gray-100">
                                    Welcome, {{ auth('customer')->user()->first_name }}!
                                </div>
                                <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('customer.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600">
                                    <i class="fas fa-user mr-2"></i>My Profile
                                </a>
                                <a href="{{ route('customer.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600">
                                    <i class="fas fa-shopping-bag mr-2"></i>My Orders
                                </a>
                                <a href="{{ route('customer.addresses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600">
                                    <i class="fas fa-map-marker-alt mr-2"></i>My Addresses
                                </a>
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <form action="{{ route('customer.logout') }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('wishlist.index') }}" class="flex flex-col items-center text-gray-700 hover:text-blue-600 relative">
                        <i class="fas fa-heart text-lg"></i>
                        <span class="text-xs hidden sm:inline">Wishlist</span>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center wishlist-count" style="display: none;">0</span>
                    </a>

                    <a href="{{ route('cart.index') }}" class="flex flex-col items-center text-gray-700 hover:text-blue-600 relative">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span class="text-xs hidden sm:inline">Cart</span>
                        <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center cart-count" style="display: none;">0</span>
                    </a>

                    {{-- <a href="#" class="hidden md:flex flex-col items-center text-gray-700 hover:text-blue-600">
                        <i class="fas fa-store text-lg"></i>
                        <span class="text-xs">Seller</span>
                    </a> --}}

                    <a href="#" class="hidden md:flex flex-col items-center text-gray-700 hover:text-blue-600">
                        <i class="fas fa-ellipsis-v text-lg"></i>
                        <span class="text-xs">More</span>
                    </a>
                </div>
            </div>

            <!-- Mobile Search Bar (Hidden by default) -->
            <div id="mobile-search" class="hidden md:hidden pb-3">
                <form action="{{ route('products.index') }}" method="GET" class="relative">
                    <input type="text" name="search"
                        placeholder="Search for products, brands and more"
                        class="w-full px-4 py-2 border border-gray-300 rounded-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Mobile Navigation Menu (Hidden by default) -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 py-3">
                <div class="flex flex-col space-y-3">
                    @guest('customer')
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 py-2">Login</a>
                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-blue-600 py-2">Sign Up</a>
                    @endguest
                    @auth('customer')
                    <a href="{{ route('customer.dashboard') }}" class="text-gray-700 hover:text-blue-600 py-2">My Account</a>
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-blue-600 py-2 text-left">Logout</button>
                    </form>
                    @endauth
                    {{-- <a href="#" class="text-gray-700 hover:text-blue-600 py-2">Become a Seller</a> --}}
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-3 md:px-4 py-2 md:py-4">
        <!-- Main Content Area -->
        <div class="w-full">
            <!-- ─── Hero / Super Sale Banner Carousel ─── -->
            <div class="bg-white rounded shadow-sm mb-3 md:mb-4 overflow-hidden">
                <div class="swiper hero-swiper banner-slide">
                    <div class="swiper-wrapper">

                        {{-- Admin-managed HERO banners (car images + promotions) --}}
                        @if(isset($heroBanners) && $heroBanners->count() > 0)
                            @foreach($heroBanners as $banner)
                            <div class="swiper-slide relative overflow-hidden"
                                 style="min-height:280px; background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);">
                                {{-- Full banner image --}}
                                <img src="{{ asset('storage/' . $banner->image) }}"
                                     alt="{{ $banner->title }}"
                                     class="absolute inset-0 w-full h-full object-cover"
                                     style="opacity: {{ ($banner->title || $banner->caption) ? '0.65' : '1' }};">
                                {{-- Overlay text (only if title/caption/link set) --}}
                                @if($banner->title || $banner->caption || $banner->link)
                                <div class="relative z-10 flex items-end md:items-center h-full p-5 md:p-12"
                                     style="min-height:280px; background: linear-gradient(to right, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.1) 70%, transparent 100%);">
                                    <div class="text-white max-w-xl">
                                        @if($banner->title)
                                            <h2 class="text-2xl md:text-4xl font-bold mb-2 drop-shadow-lg leading-tight">
                                                {{ $banner->title }}
                                            </h2>
                                        @endif
                                        @if($banner->caption)
                                            <p class="text-sm md:text-lg mb-4 opacity-90 leading-snug">
                                                {{ $banner->caption }}
                                            </p>
                                        @endif
                                        @if($banner->link)
                                            <a href="{{ $banner->link }}"
                                               class="inline-block bg-white text-blue-700 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-gray-900 transition-all duration-200 shadow">
                                                Shop Now &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach

                        {{-- Fallback: show product images if no hero banners --}}
                        @elseif(isset($carouselProducts) && $carouselProducts->count() > 0)
                            @foreach($carouselProducts as $product)
                            <div class="swiper-slide bg-gradient-to-r from-blue-900 to-blue-600 text-white p-4 md:p-8 flex items-center"
                                 style="min-height:280px;">
                                <div class="w-full md:w-1/2 text-center md:text-left">
                                    <p class="text-yellow-300 text-xs md:text-sm font-semibold uppercase tracking-widest mb-1">Featured</p>
                                    <h2 class="text-2xl md:text-3xl font-bold mb-2 leading-tight">{{ Str::limit($product->name, 40) }}</h2>
                                    <p class="text-sm md:text-base mb-4 opacity-80">{{ $product->short_description ?? 'Explore this model' }}</p>
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="inline-block bg-white text-blue-700 px-5 py-2 rounded font-bold hover:bg-yellow-400 hover:text-gray-900 transition">
                                        View Details
                                    </a>
                                </div>
                                <div class="hidden md:flex w-1/2 justify-end items-center">
                                    @if($product->main_image_url)
                                        <img src="{{ $product->main_image_url }}"
                                             alt="{{ $product->name }}"
                                             class="max-h-52 object-contain drop-shadow-xl">
                                    @else
                                        <i class="fas fa-car-side text-8xl opacity-20"></i>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                        {{-- Static fallback when nothing is in DB --}}
                        @else
                            <div class="swiper-slide bg-gradient-to-r from-blue-900 to-indigo-700 text-white p-8 flex items-center"
                                 style="min-height:280px;">
                                <div>
                                    <p class="text-yellow-300 text-sm font-semibold uppercase tracking-widest mb-2">Super Sale</p>
                                    <h2 class="text-4xl font-bold mb-3">Big Deals on Cars & Accessories</h2>
                                    <p class="text-lg mb-5 opacity-80">Shop the best prices on SUVs, Sedans, and more</p>
                                    <a href="{{ route('products.index') }}"
                                       class="inline-block bg-white text-blue-700 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-gray-900 transition">
                                        Shop Now &rarr;
                                    </a>
                                </div>
                            </div>
                            <div class="swiper-slide bg-gradient-to-r from-green-800 to-teal-600 text-white p-8 flex items-center"
                                 style="min-height:280px;">
                                <div>
                                    <p class="text-yellow-300 text-sm font-semibold uppercase tracking-widest mb-2">New Arrivals</p>
                                    <h2 class="text-4xl font-bold mb-3">Latest Models Just In!</h2>
                                    <p class="text-lg mb-5 opacity-80">Explore our newest collection of vehicles</p>
                                    <a href="{{ route('products.index') }}"
                                       class="inline-block bg-white text-green-700 px-6 py-2 rounded font-bold hover:bg-yellow-400 hover:text-gray-900 transition">
                                        Explore Now &rarr;
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next hidden md:flex"></div>
                    <div class="swiper-button-prev hidden md:flex"></div>
                </div>
            </div>

            <!-- Quick Categories Grid -->
            @if($categories && $categories->count() > 0)
            <div class="bg-white rounded shadow-sm mb-6 p-3 md:p-4">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 md:gap-4">
                    @foreach($categories->take(8) as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                        class="text-center group category-icon">
                        <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full mx-auto mb-2 flex items-center justify-center group-hover:from-blue-200 group-hover:to-blue-300 transition-all duration-300">
                            <i class="fas fa-tag text-blue-600 text-sm md:text-xl"></i>
                        </div>
                        <span class="text-xs text-gray-700 group-hover:text-blue-600 font-medium block leading-tight">{{ Str::limit($category->name, 10) }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- ─── Promotional / Offers Banners ─── -->
            @if(isset($promoBanners) && $promoBanners->count() > 0)
            <div class="mb-4 md:mb-6">
                <div class="grid grid-cols-1 {{ $promoBanners->count() >= 2 ? 'sm:grid-cols-2' : '' }} {{ $promoBanners->count() >= 3 ? 'md:grid-cols-3' : '' }} gap-3 md:gap-4">
                    @foreach($promoBanners->take(3) as $promo)
                    <a href="{{ $promo->link ?: '#' }}" class="block rounded overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 relative group" style="min-height:140px;">
                        <img src="{{ asset('storage/' . $promo->image) }}"
                             alt="{{ $promo->title }}"
                             class="w-full h-full object-cover absolute inset-0 group-hover:scale-105 transition-transform duration-300"
                             style="min-height:140px;">
                        @if($promo->title || $promo->caption)
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-3 md:p-4">
                            @if($promo->title)
                                <h3 class="text-white font-bold text-sm md:text-base leading-tight drop-shadow">{{ $promo->title }}</h3>
                            @endif
                            @if($promo->caption)
                                <p class="text-white/80 text-xs mt-0.5">{{ $promo->caption }}</p>
                            @endif
                        </div>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Deal of the Day / Featured Products -->
            @if($featuredProducts && $featuredProducts->count() > 0)
            <div class="bg-white rounded shadow-sm mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-3 md:p-4 border-b">
                    <div class="flex items-center mb-2 sm:mb-0">
                        <h2 class="text-lg md:text-xl font-bold text-gray-800 mr-2">Deal of the Day</h2>
                        <div class="hidden sm:flex items-center text-xs md:text-sm text-gray-600">
                            <i class="fas fa-clock mr-1"></i>
                            <span id="countdown" class="font-semibold text-red-600"></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="sm:hidden flex items-center text-xs text-gray-600">
                            <i class="fas fa-clock mr-1"></i>
                            <span id="countdown-mobile" class="font-semibold text-red-600"></span>
                        </div>
                        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-xs md:text-sm">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <div class="p-3 md:p-4">
                    <div class="swiper featured-swiper">
                        <div class="swiper-wrapper">
                            @foreach($featuredProducts as $product)
                            @php $featuredOOS = $product->track_quantity && $product->quantity < 1; @endphp
                            <div class="swiper-slide">
                                <div class="product-card bg-white border border-gray-200 rounded p-3 md:p-4 group h-full {{ $featuredOOS ? 'opacity-75' : '' }}">
                                    <a href="{{ route('products.show', $product->slug) }}" class="block">
                                        <div class="relative mb-3">
                                            @if($product->main_image_url)
                                            <img src="{{ $product->main_image_url }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-24 md:h-32 object-contain group-hover:scale-105 transition duration-300">
                                            @else
                                            <div class="w-full h-24 md:h-32 bg-gray-100 flex items-center justify-center rounded">
                                                <i class="fas fa-image text-lg md:text-2xl text-gray-400"></i>
                                            </div>
                                            @endif

                                            @if($featuredOOS)
                                                <div class="absolute top-1 left-1">
                                                    <span class="bg-gray-800 text-white px-1 md:px-2 py-1 rounded text-xs font-bold">Out of Stock</span>
                                                </div>
                                            @elseif($product->special_price && $product->special_price < $product->price)
                                                <div class="absolute top-1 left-1">
                                                    <span class="bg-green-500 text-white px-1 md:px-2 py-1 rounded text-xs font-bold">
                                                        {{ round((($product->price - $product->special_price) / $product->price) * 100) }}% OFF
                                                    </span>
                                                </div>
                                            @endif

                                                <div class="absolute top-1 right-1">
                                                    <button onclick="addToWishlist({{ $product->id }})" class="w-6 h-6 md:w-8 md:h-8 bg-white rounded-full shadow-sm flex items-center justify-center hover:bg-red-50 group">
                                                        <i class="fas fa-heart text-xs md:text-sm text-gray-400 group-hover:text-red-500"></i>
                                                    </button>
                                                </div>
                                        </div>
                                    </a>

                                    <div class="space-y-1">
                                        <a href="{{ route('products.show', $product->slug) }}">
                                            <h3 class="font-medium text-xs md:text-sm text-gray-800 group-hover:text-blue-600 line-clamp-2 leading-tight">
                                                {{ Str::limit($product->name, 40) }}
                                            </h3>
                                        </a>

                                        <div class="flex items-center space-x-1">
                                            @if($featuredOOS)
                                                <span class="text-xs text-red-600 font-medium">Out of Stock</span>
                                            @else
                                                <div class="flex text-yellow-400 text-xs">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                                <span class="text-xs text-gray-500">({{ rand(50, 500) }})</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            @if($product->special_price && $product->special_price < $product->price)
                                                <span class="text-sm md:text-lg font-bold text-gray-900">{{ format_currency($product->special_price) }}</span>
                                                <span class="text-xs md:text-sm text-gray-500 line-through">{{ format_currency($product->price) }}</span>
                                                @else
                                                <span class="text-sm md:text-lg font-bold text-gray-900">{{ format_currency($product->price) }}</span>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-next !text-blue-600 hidden sm:flex"></div>
                        <div class="swiper-button-prev !text-blue-600 hidden sm:flex"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Latest Products -->
            @if($latestProducts && $latestProducts->count() > 0)
            <div class="bg-white rounded shadow-sm mb-6">
                <div class="flex justify-between items-center p-4 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Latest Products</h2>
                    <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($latestProducts->take(6) as $product)
                        @php $latestOOS = $product->track_quantity && $product->quantity < 1; @endphp
                        <div class="product-card bg-white border border-gray-200 rounded p-3 group {{ $latestOOS ? 'opacity-75' : '' }}">
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                <div class="relative mb-3">
                                    @if($product->main_image_url)
                                    <img src="{{ $product->main_image_url }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-24 md:h-32 object-contain group-hover:scale-105 transition duration-300">
                                    @else
                                    <div class="w-full h-24 md:h-32 bg-gray-100 flex items-center justify-center rounded">
                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                    </div>
                                    @endif

                                    @if($latestOOS)
                                    <div class="absolute top-1 left-1">
                                        <span class="bg-gray-800 text-white px-1 py-1 rounded text-xs font-bold">Out of Stock</span>
                                    </div>
                                    @elseif($product->special_price && $product->special_price < $product->price)
                                    <div class="absolute top-1 left-1">
                                        <span class="bg-green-500 text-white px-1 py-1 rounded text-xs font-bold">
                                            {{ round((($product->price - $product->special_price) / $product->price) * 100) }}% OFF
                                        </span>
                                    </div>
                                    @endif

                                    <div class="absolute top-1 right-1">
                                        <button onclick="addToWishlist({{ $product->id }})" class="w-8 h-8 bg-white rounded-full shadow-sm flex items-center justify-center hover:bg-red-50 group">
                                            <i class="fas fa-heart text-sm text-gray-400 group-hover:text-red-500"></i>
                                        </button>
                                    </div>
                                </div>
                            </a>

                            <div class="space-y-1">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <h3 class="font-medium text-sm text-gray-800 group-hover:text-blue-600 line-clamp-2">
                                        {{ Str::limit($product->name, 35) }}
                                    </h3>
                                </a>

                                <div class="flex items-center space-x-1">
                                    @if($latestOOS)
                                        <span class="text-xs text-red-600 font-medium">Out of Stock</span>
                                    @else
                                        <div class="flex text-yellow-400 text-xs">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span class="text-xs text-gray-500">({{ rand(25, 200) }})</span>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if($product->special_price && $product->special_price < $product->price)
                                    <span class="text-lg font-bold text-gray-900">{{ format_currency($product->special_price) }}</span>
                                    <span class="text-sm text-gray-500 line-through">{{ format_currency($product->price) }}</span>
                                    @else
                                    <span class="text-lg font-bold text-gray-900">{{ format_currency($product->price) }}</span>
                                    @endif
                                </div>

                                @if($latestOOS)
                                    <button disabled class="w-full bg-gray-400 text-white py-2 rounded text-sm font-semibold cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                @else
                                    <button onclick="addToCart({{ $product->id }})" class="w-full bg-blue-600 text-white py-2 rounded text-sm font-semibold hover:bg-blue-700 transition-colors">
                                        Add to Cart
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Footer -->
            <footer class="bg-gray-800 text-white mt-8">
                <div class="container mx-auto px-4">
                    <!-- Main Footer -->
                    <div class="py-8 grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div>
                            <h4 class="text-lg font-semibold mb-4">ABOUT</h4>
                            <ul class="space-y-2 text-gray-300">
                                <li><a href="#" class="hover:text-white">Contact Us</a></li>
                                <li><a href="#" class="hover:text-white">About Us</a></li>
                                <li><a href="#" class="hover:text-white">Careers</a></li>
                                <li><a href="#" class="hover:text-white">Corporate Information</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold mb-4">HELP</h4>
                            <ul class="space-y-2 text-gray-300">
                                <li><a href="#" class="hover:text-white">Payments</a></li>
                                <li><a href="#" class="hover:text-white">Shipping</a></li>
                                <li><a href="#" class="hover:text-white">Cancellation & Returns</a></li>
                                <li><a href="#" class="hover:text-white">FAQ</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold mb-4">CONSUMER POLICY</h4>
                            <ul class="space-y-2 text-gray-300">
                                <li><a href="#" class="hover:text-white">Terms Of Use</a></li>
                                <li><a href="#" class="hover:text-white">Security</a></li>
                                <li><a href="#" class="hover:text-white">Privacy</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold mb-4">SOCIAL</h4>
                            <div class="flex space-x-4 mb-4">
                                <a href="#" class="text-gray-300 hover:text-white text-xl"><i class="fab fa-facebook"></i></a>
                                <a href="#" class="text-gray-300 hover:text-white text-xl"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-gray-300 hover:text-white text-xl"><i class="fab fa-youtube"></i></a>
                            </div>
                            <!-- Contact address removed -->
                        </div>
                    </div>

                    <!-- Bottom Footer -->
                    <div class="border-t border-gray-700 py-4">
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                                {{-- <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-gray-300 text-sm">Become a Seller</span>
                                </div> --}}
                                <div class="flex items-center">
                                    <i class="fas fa-gift text-orange-400 mr-1"></i>
                                    <span class="text-gray-300 text-sm">Gift Cards</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-question-circle text-blue-400 mr-1"></i>
                                    <span class="text-gray-300 text-sm">Help Center</span>
                                </div>
                            </div>

                            <div class="text-center text-gray-400 text-sm">
                                <p>&copy; {{ date('Y') }} E-Commerce Store. All rights reserved.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

            <script>
                // Mobile Menu Functions
                function toggleMobileMenu() {
                    const mobileMenu = document.getElementById('mobile-menu');
                    mobileMenu.classList.toggle('hidden');
                }

                function toggleSearchBar() {
                    const mobileSearch = document.getElementById('mobile-search');
                    mobileSearch.classList.toggle('hidden');
                    if (!mobileSearch.classList.contains('hidden')) {
                        mobileSearch.querySelector('input').focus();
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    // Hero Banner Carousel
                    const heroSwiper = new Swiper('.hero-swiper', {
                        slidesPerView: 1,
                        spaceBetween: 0,
                        loop: true,
                        autoplay: {
                            delay: 4000,
                            disableOnInteraction: false,
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                    });

                    // Featured Products Carousel - Mobile Optimized
                    const featuredSwiper = new Swiper('.featured-swiper', {
                        slidesPerView: 1.2,
                        spaceBetween: 12,
                        loop: true,
                        autoplay: {
                            delay: 3000,
                            disableOnInteraction: false,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        breakpoints: {
                            480: {
                                slidesPerView: 2,
                                spaceBetween: 12,
                            },
                            640: {
                                slidesPerView: 2.5,
                                spaceBetween: 16,
                            },
                            768: {
                                slidesPerView: 3,
                                spaceBetween: 16,
                            },
                            1024: {
                                slidesPerView: 4,
                                spaceBetween: 20,
                            },
                            1280: {
                                slidesPerView: 5,
                                spaceBetween: 20,
                            },
                            1536: {
                                slidesPerView: 6,
                                spaceBetween: 24,
                            },
                        },
                    });

                    // Countdown Timer for Deal of the Day
                    function updateCountdown() {
                        const now = new Date().getTime();
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        tomorrow.setHours(0, 0, 0, 0);
                        const timeLeft = tomorrow.getTime() - now;

                        const hours = Math.floor(timeLeft / (1000 * 60 * 60));
                        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                        const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} left`;

                        const countdownElement = document.getElementById('countdown');
                        const countdownMobileElement = document.getElementById('countdown-mobile');
                        
                        if (countdownElement) {
                            countdownElement.innerHTML = timeString;
                        }
                        if (countdownMobileElement) {
                            countdownMobileElement.innerHTML = timeString;
                        }
                    }

                    // Update countdown every second
                    updateCountdown();
                    setInterval(updateCountdown, 1000);

                    // Close mobile menu when clicking outside
                    document.addEventListener('click', function(event) {
                        const mobileMenu = document.getElementById('mobile-menu');
                        const mobileSearch = document.getElementById('mobile-search');
                        const menuButton = event.target.closest('button');
                        
                        if (!menuButton && !mobileMenu.contains(event.target)) {
                            mobileMenu.classList.add('hidden');
                        }
                        
                        if (!event.target.closest('#mobile-search') && !event.target.closest('button[onclick="toggleSearchBar()"]')) {
                            mobileSearch.classList.add('hidden');
                        }
                    });

                    // Function to update cart count
                    function updateCartCount() {
                        fetch('{{ route("cart.count") }}')
                            .then(response => response.json())
                            .then(data => {
                                const cartCountElements = document.querySelectorAll('.cart-count');
                                cartCountElements.forEach(element => {
                                    element.textContent = data.count;
                                    // Hide the badge if count is 0
                                    if (data.count === 0) {
                                        element.style.display = 'none';
                                    } else {
                                        element.style.display = 'flex';
                                    }
                                });
                            })
                            .catch(error => {
                                console.error('Error updating cart count:', error);
                            });
                    }

                    // Function to update wishlist count
                    function updateWishlistCount() {
                        fetch('{{ route("wishlist.count") }}')
                            .then(response => response.json())
                            .then(data => {
                                const wishlistCountElements = document.querySelectorAll('.wishlist-count');
                                wishlistCountElements.forEach(element => {
                                    element.textContent = data.count;
                                    // Hide the badge if count is 0
                                    if (data.count === 0) {
                                        element.style.display = 'none';
                                    } else {
                                        element.style.display = 'flex';
                                    }
                                });
                            })
                            .catch(error => {
                                console.error('Error updating wishlist count:', error);
                            });
                    }

                    // Function to update both counts
                    function updateAllCounts() {
                        updateCartCount();
                        updateWishlistCount();
                    }

                    // Update counts when page loads
                    updateAllCounts();

                    // Update counts every 30 seconds to keep them fresh
                    setInterval(updateAllCounts, 30000);

                    // Make functions globally available for other scripts
                    window.updateCartCount = updateCartCount;
                    window.updateWishlistCount = updateWishlistCount;
                    window.updateAllCounts = updateAllCounts;

                    // Account Dropdown Delay Functionality
                    const accountDropdown = document.querySelector('.account-dropdown');
                    const accountTrigger = document.querySelector('.group');
                    let dropdownTimeout;

                    if (accountTrigger && accountDropdown) {
                        // Show dropdown on hover
                        accountTrigger.addEventListener('mouseenter', function() {
                            clearTimeout(dropdownTimeout);
                            accountDropdown.classList.add('show');
                        });

                        // Hide dropdown with delay when mouse leaves
                        accountTrigger.addEventListener('mouseleave', function() {
                            dropdownTimeout = setTimeout(function() {
                                accountDropdown.classList.remove('show');
                            }, 300); // 300ms delay
                        });

                        // Keep dropdown visible when hovering over it
                        accountDropdown.addEventListener('mouseenter', function() {
                            clearTimeout(dropdownTimeout);
                            accountDropdown.classList.add('show');
                        });

                        // Hide dropdown when mouse leaves the dropdown itself
                        accountDropdown.addEventListener('mouseleave', function() {
                            dropdownTimeout = setTimeout(function() {
                                accountDropdown.classList.remove('show');
                            }, 300); // 300ms delay
                        });
                    }
                });

                // Add to Cart function (global)
                window.addToCart = function(productId) {
                    fetch('{{ route("cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showToast('Product added to cart successfully!', 'success');
                            // Update cart count
                            updateCartCount();
                        } else {
                            showToast(data.message || 'Failed to add product to cart', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('An error occurred while adding to cart', 'error');
                    });
                };

                // Add to Wishlist function (global)
                window.addToWishlist = function(productId) {
                    fetch('{{ route("wishlist.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Product added to wishlist!', 'success');
                            updateWishlistCount();
                        } else {
                            showToast(data.message || 'Failed to add to wishlist', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('An error occurred while adding to wishlist', 'error');
                    });
                };

                // Toast notification function (global)
                window.showToast = function(message, type = 'info') {
                    // Remove existing toasts
                    const existingToast = document.querySelector('.toast');
                    if (existingToast) {
                        existingToast.remove();
                    }

                    // Create toast element
                    const toast = document.createElement('div');
                    toast.className = `toast fixed top-4 right-4 px-4 py-2 rounded-lg text-white text-sm font-medium z-50 transform transition-all duration-300`;
                    
                    // Set color based on type
                    if (type === 'success') {
                        toast.classList.add('bg-green-500');
                    } else if (type === 'error') {
                        toast.classList.add('bg-red-500');
                    } else {
                        toast.classList.add('bg-blue-500');
                    }
                    
                    toast.textContent = message;
                    toast.style.transform = 'translateX(100%)';
                    
                    document.body.appendChild(toast);
                    
                    // Animate in
                    setTimeout(() => {
                        toast.style.transform = 'translateX(0)';
                    }, 10);
                    
                    // Remove after 3 seconds
                    setTimeout(() => {
                        toast.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.parentNode.removeChild(toast);
                            }
                        }, 300);
                    }, 3000);
                };
            </script>
</body>

</html>