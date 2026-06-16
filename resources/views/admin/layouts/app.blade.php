<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ $storeName }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Additional styles -->
    <style>
        [x-cloak] { display: none !important; }
        .badge-lg { font-size: 0.9em; padding: 0.5rem 0.75rem; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: false }">
        <!-- Mobile overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-20 md:hidden"
             x-cloak></div>

        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 fixed md:relative inset-y-0 left-0 z-30 transform transition duration-200 ease-in-out"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            <div class="flex items-center justify-between px-4">
                <a href="{{ route('admin.dashboard') }}" class="text-white flex items-center space-x-2" @click="sidebarOpen = false">
                    <span class="text-2xl font-extrabold">{{ $storeName }}</span>
                </a>
                <button type="button" @click="sidebarOpen = false" class="md:hidden text-gray-300 hover:text-white p-1" aria-label="Close menu">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 mr-2"></i> Dashboard
                </a>

                <div class="pt-2 pb-1 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Catalog</div>

                <a href="{{ route('admin.categories.index') }}" @click="sidebarOpen = false" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-tags w-5 mr-2"></i> Categories
                </a>

                <a href="{{ route('admin.products.index') }}" @click="sidebarOpen = false" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.products.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-box w-5 mr-2"></i> Products
                </a>

                <div class="pt-2 pb-1 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sales</div>

                <a href="{{ route('admin.orders.index') }}" @click="sidebarOpen = false" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-shopping-bag w-5 mr-2"></i> Orders
                </a>

                <a href="{{ route('admin.customers.index') }}" @click="sidebarOpen = false" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.customers.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-users w-5 mr-2"></i> Customers
                </a>

                <div class="pt-2 pb-1 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Content</div>

                <a href="{{ route('admin.banners.index') }}" @click="sidebarOpen = false" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.banners.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-images w-5 mr-2"></i> Banners
                </a>

                <div class="pt-2 pb-1 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</div>

                <a href="{{ route('admin.settings.index') }}" @click="sidebarOpen = false" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-cog w-5 mr-2"></i> Settings
                </a>
            </nav>
        </div>

        <!-- Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex justify-between items-center py-4 px-4 sm:px-6 bg-white border-b-4 border-indigo-600">
                <div class="flex items-center min-w-0">
                    <button type="button"
                            @click="sidebarOpen = true"
                            class="md:hidden mr-3 p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            aria-label="Open menu">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-lg sm:text-2xl font-semibold text-gray-800 truncate">@yield('title', 'Admin Panel')</h1>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <div class="relative">
                        <button class="relative z-10 block h-8 w-8 rounded-full overflow-hidden border-2 border-gray-600 focus:outline-none focus:border-white" id="user-menu">
                            <span class="bg-gray-600 text-white h-8 w-8 flex items-center justify-center text-sm font-medium">
                                {{ substr(auth('admin')->user()->name, 0, 1) }}
                            </span>
                        </button>

                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-10 hidden" id="user-dropdown">
                            <div class="px-4 py-3">
                                <p class="text-sm leading-5">Signed in as</p>
                                <p class="text-sm leading-5 font-medium text-gray-900 truncate">{{ auth('admin')->user()->name }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container mx-auto px-6 py-8">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle user dropdown
        document.getElementById('user-menu').addEventListener('click', function() {
            document.getElementById('user-dropdown').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!document.getElementById('user-menu').contains(e.target)) {
                document.getElementById('user-dropdown').classList.add('hidden');
            }
        });
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>