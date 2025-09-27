@extends('customer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Addresses</h1>
                    <p class="text-gray-600 mt-1">Manage your saved addresses for faster checkout</p>
                </div>
                <a href="{{ route('customer.addresses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add New Address
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Addresses List -->
        @if($addresses && $addresses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($addresses as $address)
                    <div class="bg-white rounded-lg shadow-sm border-2 {{ $address->is_default ?? false ? 'border-blue-500' : 'border-gray-200' }} p-6">
                        <!-- Default Badge -->
                        @if($address->is_default ?? false)
                            <div class="mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-star mr-1"></i>Default
                                </span>
                            </div>
                        @endif

                        <!-- Address Type -->
                        <div class="mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-{{ ($address->type ?? 'home') === 'home' ? 'home' : (($address->type ?? 'home') === 'work' ? 'building' : 'map-marker-alt') }} mr-1"></i>
                                {{ ucfirst($address->type ?? 'Home') }}
                            </span>
                        </div>

                        <!-- Address Details -->
                        <div class="space-y-2 mb-4">
                            @if(isset($address->name))
                                <p class="font-medium text-gray-900">{{ $address->name }}</p>
                            @endif
                            <p class="text-gray-600">{{ $address->address_line_1 ?? 'Address line 1' }}</p>
                            @if(isset($address->address_line_2))
                                <p class="text-gray-600">{{ $address->address_line_2 }}</p>
                            @endif
                            <p class="text-gray-600">
                                {{ $address->city ?? 'City' }}, {{ $address->state ?? 'State' }} {{ $address->zip_code ?? 'ZIP' }}
                            </p>
                            <p class="text-gray-600">{{ $address->country ?? 'Country' }}</p>
                            @if(isset($address->phone))
                                <p class="text-gray-600">
                                    <i class="fas fa-phone mr-2"></i>{{ $address->phone }}
                                </p>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('customer.addresses.edit', $address->id ?? 1) }}" 
                                   class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                @if(!($address->is_default ?? false))
                                    <button class="text-green-600 hover:text-green-700 text-sm font-medium">
                                        <i class="fas fa-star mr-1"></i>Set Default
                                    </button>
                                @endif
                            </div>
                            @if(!($address->is_default ?? false))
                                <form method="POST" action="{{ route('customer.addresses.destroy', $address->id ?? 1) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete this address?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-map-marker-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No addresses saved</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Save your addresses to make checkout faster and easier. You can add multiple addresses for home, work, or other locations.
                </p>
                <a href="{{ route('customer.addresses.create') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Address
                </a>
            </div>
        @endif
    </div>
</div>
@endsection