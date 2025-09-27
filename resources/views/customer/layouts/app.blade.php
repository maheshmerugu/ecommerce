<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Customer Dashboard')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Additional styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">
                        {{ config('app.name', 'E-Commerce') }}
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-900">Home</a>
                    <a href="#" class="text-gray-500 hover:text-gray-900">Products</a>
                    <a href="#" class="text-gray-500 hover:text-gray-900">Categories</a>
                </nav>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    @auth('customer')
                        <div class="relative">
                            <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu">
                                <span class="bg-gray-600 text-white h-8 w-8 flex items-center justify-center rounded-full text-sm font-medium">
                                    {{ substr(auth('customer')->user()->first_name, 0, 1) }}
                                </span>
                                <span class="ml-2 text-gray-700">{{ auth('customer')->user()->first_name }}</span>
                                <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 hidden" id="user-dropdown">
                                <div class="py-1">
                                    <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <a href="{{ route('customer.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="{{ route('customer.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Orders</a>
                                    <a href="{{ route('customer.addresses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Addresses</a>
                                    <div class="border-t border-gray-200"></div>
                                    <form method="POST" action="{{ route('customer.logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-900">Sign in</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Sign up</a>
                    @endauth

                    <!-- Cart -->
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13h10m0 0l1.5 6"></path>
                        </svg>
                        <span class="sr-only">Shopping cart</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    @auth('customer')
        <!-- Customer Dashboard Navigation -->
        <div class="bg-gray-100 border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex space-x-8 overflow-x-auto py-3">
                    <a href="{{ route('customer.dashboard') }}" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('customer.dashboard') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('customer.profile.show') }}" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('customer.profile.*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Profile
                    </a>
                    <a href="{{ route('customer.orders.index') }}" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('customer.orders.*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Orders
                    </a>
                    <a href="{{ route('customer.addresses.index') }}" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('customer.addresses.*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Addresses
                    </a>
                </nav>
            </div>
        </div>
    @endauth

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-gray-500">&copy; 2025 {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Toggle user dropdown
        const userMenu = document.getElementById('user-menu');
        const userDropdown = document.getElementById('user-dropdown');
        
        if (userMenu && userDropdown) {
            userMenu.addEventListener('click', function(e) {
                e.preventDefault();
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenu.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>