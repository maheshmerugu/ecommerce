@extends('customer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('customer.addresses.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Address</h1>
                    <p class="text-gray-600 mt-1">Update your saved address</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('customer.addresses.update', $address) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $address->first_name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $address->last_name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone', $address->phone) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                        <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['shipping','billing','home','work','other'] as $t)
                                <option value="{{ $t }}" {{ old('type', $address->type) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 <span class="text-red-500">*</span></label>
                    <input type="text" name="address_line_1" value="{{ old('address_line_1', $address->address_line_1) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                    <input type="text" name="address_line_2" value="{{ old('address_line_2', $address->address_line_2) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State <span class="text-red-500">*</span></label>
                        <select name="state" id="addr_state" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select state</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                        <select name="city" id="addr_city" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select city</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code <span class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" id="addr_pincode"
                               value="{{ old('postal_code', $address->postal_code) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1"
                           {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 text-sm text-gray-700">Set as default address</label>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('customer.addresses.index') }}"
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stateEl   = document.getElementById('addr_state');
    const cityEl    = document.getElementById('addr_city');
    const pinEl     = document.getElementById('addr_pincode');
    const savedState = "{{ $address->state }}";
    const savedCity  = "{{ $address->city }}";

    fetch('{{ route("locations.states") }}')
        .then(r => r.json())
        .then(data => {
            (data.states || []).forEach(s => {
                const opt = document.createElement('option');
                opt.value = s; opt.textContent = s;
                if (s === savedState) opt.selected = true;
                stateEl.appendChild(opt);
            });
            if (savedState) loadCities(savedState);
        });

    stateEl.addEventListener('change', function () {
        loadCities(this.value);
    });

    cityEl.addEventListener('change', function () {
        if (!this.value) return;
        fetch('{{ route("locations.pincodes") }}?city=' + encodeURIComponent(this.value))
            .then(r => r.json())
            .then(d => { if (d.pincodes && d.pincodes.length && !pinEl.value) pinEl.value = d.pincodes[0]; });
    });

    function loadCities(state) {
        cityEl.innerHTML = '<option value="">Select city</option>';
        if (!state) return;
        fetch('{{ route("locations.cities") }}?state=' + encodeURIComponent(state))
            .then(r => r.json())
            .then(data => {
                (data.cities || []).forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c; opt.textContent = c;
                    if (c === savedCity) opt.selected = true;
                    cityEl.appendChild(opt);
                });
            });
    }
});
</script>
@endsection
