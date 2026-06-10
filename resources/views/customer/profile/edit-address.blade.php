@extends('customer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('customer.addresses.index') }}" 
                   class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Address</h1>
                    <p class="text-gray-600 mt-1">Update your address details</p>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Address Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('customer.addresses.update', $address->id ?? 1) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Address Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Address Type</label>
                    <select name="type" id="type" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="home" {{ (old('type', $address->type ?? 'home') === 'home') ? 'selected' : '' }}>Home</option>
                        <option value="work" {{ (old('type', $address->type ?? 'home') === 'work') ? 'selected' : '' }}>Work</option>
                        <option value="other" {{ (old('type', $address->type ?? 'home') === 'other') ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $address->name ?? '') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter full name">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $address->phone ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter phone number">
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label for="address_line_1" class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                    <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $address->address_line_1 ?? '') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Street address, building number">
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label for="address_line_2" class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                    <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $address->address_line_2 ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Apartment, suite, unit, building, floor, etc.">
                </div>

                <!-- City, State, ZIP -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                        <input list="cities-datalist" type="text" name="city" id="city" value="{{ old('city', $address->city ?? '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Start typing city">
                        <datalist id="cities-datalist"></datalist>
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                        <input list="states-datalist" type="text" name="state" id="state" value="{{ old('state', $address->state ?? '') }}" required onchange="onStateChangeEdit(this.value)"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Start typing state">
                        <datalist id="states-datalist"></datalist>
                    </div>
                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">ZIP Code *</label>
                        <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $address->zip_code ?? '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="ZIP Code">
                    </div>
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                    <select name="country" id="country" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Country</option>
                        <option value="United States" {{ (old('country', $address->country ?? '') === 'United States') ? 'selected' : '' }}>United States</option>
                        <option value="Canada" {{ (old('country', $address->country ?? '') === 'Canada') ? 'selected' : '' }}>Canada</option>
                        <option value="United Kingdom" {{ (old('country', $address->country ?? '') === 'United Kingdom') ? 'selected' : '' }}>United Kingdom</option>
                        <option value="Australia" {{ (old('country', $address->country ?? '') === 'Australia') ? 'selected' : '' }}>Australia</option>
                        <option value="Germany" {{ (old('country', $address->country ?? '') === 'Germany') ? 'selected' : '' }}>Germany</option>
                        <option value="France" {{ (old('country', $address->country ?? '') === 'France') ? 'selected' : '' }}>France</option>
                        <option value="India" {{ (old('country', $address->country ?? '') === 'India') ? 'selected' : '' }}>India</option>
                        <option value="Other" {{ (old('country', $address->country ?? '') === 'Other') ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Default Address Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1" 
                           {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-700">
                        Set as default address
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('customer.addresses.index') }}" 
                       class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Address
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
    let editStateTS, editCityTS;
    function initEditAddressSelects() {
        fetch('{{ route("locations.states") }}')
            .then(res => res.json())
            .then(data => {
                const states = data.states || [];
                const stateEl = document.getElementById('state');
                stateEl.innerHTML = '<option value="">Select state</option>' + states.map(s => `<option value="${s}">${s}</option>`).join('');
                editStateTS = new TomSelect('#state', { create: false, sortField: {field: 'text'} });

                editCityTS = new TomSelect('#city', { create: true, sortField: {field: 'text'} });

                const pre = editStateTS.getValue();
                if (pre) loadCitiesForEdit(pre);
                editStateTS.on('change', loadCitiesForEdit);
                editCityTS.on('change', function(value) { loadPincodesForEdit(value); });
            })
            .catch(err => console.error('Failed to load states', err));
    }

    function loadCitiesForEdit(state) {
        if (!state) { editCityTS.clearOptions(); return; }
        fetch('{{ route("locations.cities") }}?state=' + encodeURIComponent(state))
            .then(res => res.json())
            .then(data => {
                editCityTS.clearOptions();
                (data.cities || []).forEach(c => editCityTS.addOption({value: c, text: c}));
            })
            .catch(err => console.error('Failed to load cities', err));
    }

    function loadPincodesForEdit(city) {
        if (!city) return;
        fetch('{{ route("locations.pincodes") }}?city=' + encodeURIComponent(city))
            .then(res => res.json())
            .then(data => {
                if (data.pincodes && data.pincodes.length) {
                    const el = document.getElementById('zip_code');
                    if (el) el.value = data.pincodes[0];
                }
            })
            .catch(err => console.error('Failed to load pincodes', err));
    }

    document.addEventListener('DOMContentLoaded', function() { initEditAddressSelects(); });
</script>
@endsection