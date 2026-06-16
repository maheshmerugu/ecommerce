<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $storeName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <!-- Tom Select for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                        {{ $storeName }}
                    </a>
                </div>
                <div class="text-sm text-gray-600">
                    Secure Checkout
                    <i class="fas fa-lock ml-1"></i>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div class="flex items-center text-blue-600">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <span class="ml-2 text-sm font-medium">Cart</span>
                    </div>
                    <div class="w-20 h-1 bg-blue-600 mx-2"></div>
                    <div class="flex items-center text-blue-600">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">
                            2
                        </div>
                        <span class="ml-2 text-sm font-medium">Checkout</span>
                    </div>
                    <div class="w-20 h-1 bg-gray-300 mx-2"></div>
                    <div class="flex items-center text-gray-400">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm">
                            3
                        </div>
                        <span class="ml-2 text-sm font-medium">Payment</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Checkout Form -->
            <div class="order-2 lg:order-1">
                <!-- Authenticated User Welcome -->
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-check text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Welcome back, {{ Auth::guard('customer')->user()->first_name }}!</strong>
                                You can use your saved addresses or add a new one.
                            </p>
                        </div>
                    </div>
                </div>

                <form id="checkout-form" class="space-y-6">
                    @csrf
                    
                    <!-- Customer Information -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border">
                        <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="customer_name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ Auth::guard('customer')->user()->first_name }} {{ Auth::guard('customer')->user()->last_name }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" name="customer_phone" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ Auth::guard('customer')->user()->phone ?? '' }}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" name="customer_email" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ Auth::guard('customer')->user()->email }}">
                                <p class="text-xs text-gray-500 mt-1">Order confirmation will be sent to this email</p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border">
                        <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
                        
                        @if($addresses->count() > 0)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Saved Addresses</label>
                            <div class="space-y-2">
                                @foreach($addresses as $address)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="saved_address" value="{{ $address->id }}" 
                                        class="mr-3" onchange="fillAddress({{ json_encode($address) }})" {{ $address->is_default ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $address->full_name }}</div>
                                        <div class="text-sm text-gray-600">{{ $address->formatted_address }}</div>
                                        @if($address->phone)
                                        <div class="text-sm text-gray-500">{{ $address->phone }}</div>
                                        @endif
                                        @if($address->is_default)
                                        <div class="text-xs text-blue-600 font-medium">Default Address</div>
                                        @endif
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <label class="flex items-center">
                                    <input type="radio" name="saved_address" value="new" {{ $addresses->where('is_default',true)->count() ? '' : 'checked' }} class="mr-2">
                                    <span class="text-sm">Use new address</span>
                                </label>
                            </div>
                        </div>
                        @else
                        <div class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-400">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                You don't have any saved addresses. The address you enter below will be saved to your account.
                            </p>
                        </div>
                        @endif
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Street Address *</label>
                                <textarea name="shipping_address" required rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="House number, building name, street name, area"></textarea>
                            </div>
                                <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                    <select name="shipping_state" id="shipping_state" required class="w-full">
                                        <option value="">Select state</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                    <select name="shipping_city" id="shipping_city" required class="w-full">
                                        <option value="">Select city</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pincode *</label>
                                    <input type="text" name="shipping_pincode" id="shipping_pincode" required pattern="[0-9]{6}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="6-digit pincode">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Billing Address</h3>
                            <label class="flex items-center">
                                <input type="hidden" name="same_as_shipping" value="0">
                                <input type="checkbox" name="same_as_shipping" value="1" checked class="mr-2" 
                                    onchange="toggleBillingAddress()">
                                <span class="text-sm">Same as shipping address</span>
                            </label>
                        </div>
                        
                        <div id="billing-fields" class="space-y-4 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                                <textarea name="billing_address" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="House number, building name, street name, area"></textarea>
                            </div>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                <select name="billing_state" id="billing_state" class="w-full" onchange="onBillingStateChange(this.value)"><option value="">Select state</option></select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <select name="billing_city" id="billing_city" class="w-full"><option value="">Select city</option></select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                                <input type="text" name="billing_pincode" id="billing_pincode" pattern="[0-9]{6}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="6-digit pincode">
                            </div>
                        </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-1 lg:order-2">
                <div class="bg-white p-6 rounded-lg shadow-sm border sticky top-4">
                    <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                    
                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6">
                        @foreach($cartItems as $item)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if($item->product->images && $item->product->images->count() > 0)
                                    <img src="{{ product_image_url( $item->product->images->first()->image_path) }}" 
                                        alt="{{ $item->product->name }}" 
                                        class="w-12 h-12 object-cover rounded border">
                                @else
                                    <div class="w-12 h-12 bg-gray-100 flex items-center justify-center rounded border">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</div>
                                <div class="text-sm text-gray-500">Qty: {{ $item->quantity }}</div>
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ format_currency($item->price * $item->quantity) }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pricing Summary -->
                    <div class="space-y-2 pt-4 border-t">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal ({{ $cart->total_quantity }} items)</span>
                            <span>{{ format_currency($cart->total_price) }}</span>
                        </div>
                           <div class="flex justify-between text-sm">
                               <span>Shipping</span>
                               <span class="text-gray-900" id="checkout-shipping">{{ format_currency($defaultShippingFee) }}</span>
                        </div>
                        <p class="text-xs text-gray-500" id="checkout-shipping-note">Enter pincode to calculate exact shipping</p>
                        <div class="flex justify-between font-semibold text-lg pt-2 border-t">
                            <span>Total</span>
                            <span id="checkout-total">{{ format_currency($cart->total_price + $defaultShippingFee) }}</span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mt-6 pt-4 border-t">
                        <div class="flex items-center space-x-2 mb-4">
                            <i class="fas fa-credit-card text-blue-600"></i>
                            <span class="font-medium">Payment via Razorpay</span>
                        </div>
                        <div class="text-xs text-gray-500 mb-4">
                            We accept all major credit cards, debit cards, net banking, UPI and more payment methods via Razorpay
                        </div>
                    </div>

                    <!-- Place Order Button -->
                        <button type="button" onclick="processCheckout()" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-lock mr-2"></i>
                        <span id="checkout-pay-btn-text">Place Order & Pay {{ format_currency($cart->total_price + $defaultShippingFee) }}</span>
                    </button>

                    <div class="text-xs text-center text-gray-500 mt-3">
                        Your payment information is secure and encrypted
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span>Processing your order...</span>
            </div>
        </div>
    </div>

    <script>
        // Get Razorpay key from environment
        const RAZORPAY_KEY = '{{ $razorpayKeyId ?? env("RAZORPAY_KEY_ID") }}';
        const CART_SUBTOTAL = {{ (float) $cart->total_price }};
        let currentShipping = {{ (float) $defaultShippingFee }};
        
        function formatInr(amount) {
            return '₹' + Number(amount).toLocaleString('en-IN', { maximumFractionDigits: 0, minimumFractionDigits: 0 });
        }

        function updateCheckoutTotals(shippingCharge, label, isFree) {
            currentShipping = shippingCharge;
            const shippingEl = document.getElementById('checkout-shipping');
            const totalEl = document.getElementById('checkout-total');
            const noteEl = document.getElementById('checkout-shipping-note');
            const payBtnEl = document.getElementById('checkout-pay-btn-text');

            if (shippingEl) {
                shippingEl.textContent = isFree ? 'FREE' : formatInr(shippingCharge);
                shippingEl.classList.toggle('text-green-600', isFree);
            }
            if (totalEl) totalEl.textContent = formatInr(CART_SUBTOTAL + shippingCharge);
            if (payBtnEl) payBtnEl.textContent = 'Place Order & Pay ' + formatInr(CART_SUBTOTAL + shippingCharge);
            if (noteEl) {
                noteEl.textContent = label ? (isFree ? label : label + ' — ' + formatInr(shippingCharge)) : '';
            }
        }

        function fetchShippingForPincode(pincode) {
            const cleaned = (pincode || '').replace(/\D/g, '');
            if (cleaned.length !== 6) return;

            fetch('{{ route("shipping.calculate") }}?pincode=' + encodeURIComponent(cleaned) + '&subtotal=' + CART_SUBTOTAL)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        updateCheckoutTotals(data.shipping_charge, data.label, data.is_free);
                    }
                })
                .catch(err => console.error('Shipping calculation failed', err));
        }
        
        console.log('Razorpay Key ID:', RAZORPAY_KEY);

        function toggleBillingAddress() {
            const checkbox = document.querySelector('input[name="same_as_shipping"]');
            const billingFields = document.getElementById('billing-fields');
            
            if (checkbox.checked) {
                billingFields.classList.add('hidden');
            } else {
                billingFields.classList.remove('hidden');
            }
        }

        function fillAddress(address) {
            const nameEl = document.querySelector('input[name="customer_name"]');
            if (nameEl) nameEl.value = (address.first_name || '') + ' ' + (address.last_name || '');
            const phoneEl = document.querySelector('input[name="customer_phone"]');
            if (phoneEl) phoneEl.value = address.phone || '';
            const addrEl = document.querySelector('textarea[name="shipping_address"]');
            if (addrEl) addrEl.value = (address.address_line_1 || '') + (address.address_line_2 ? ', ' + address.address_line_2 : '');

            const cityVal = address.city || '';
            const stateVal = address.state || '';
            const pinVal = address.postal_code || '';

            // If TomSelect instances exist, use them to set values; otherwise set element values directly
            if (typeof shippingStateTS !== 'undefined' && shippingStateTS) {
                shippingStateTS.setValue(stateVal);
            } else {
                const sEl = document.getElementById('shipping_state'); if (sEl) sEl.value = stateVal;
            }

            if (typeof shippingCityTS !== 'undefined' && shippingCityTS) {
                // attempt to load options for the state and set city after a short delay
                loadCitiesForState(stateVal, shippingCityTS);
                setTimeout(() => { try { shippingCityTS.setValue(cityVal); } catch(e){} }, 300);
            } else {
                const cEl = document.getElementById('shipping_city'); if (cEl) cEl.value = cityVal;
            }

            const pEl = document.getElementById('shipping_pincode'); if (pEl) pEl.value = pinVal;
            fetchShippingForPincode(pinVal);
        }

        // On load: if a saved address radio is checked (not 'new'), fill the form
        document.addEventListener('DOMContentLoaded', function() {
            const checked = document.querySelector('input[name="saved_address"]:checked');
            if (checked && checked.value !== 'new' && typeof checked.onchange === 'function') {
                checked.dispatchEvent(new Event('change'));
            }

            const pinInput = document.getElementById('shipping_pincode');
            if (pinInput) {
                pinInput.addEventListener('input', function() {
                    if (this.value.replace(/\D/g, '').length === 6) {
                        fetchShippingForPincode(this.value);
                    }
                });
                if (pinInput.value.replace(/\D/g, '').length === 6) {
                    fetchShippingForPincode(pinInput.value);
                }
            }
        });

        function processCheckout() {
            const form = document.getElementById('checkout-form');
            const formData = new FormData(form);
            
            console.log('Processing checkout...');
            
            // Show loading
            document.getElementById('loading-modal').classList.remove('hidden');
            document.getElementById('loading-modal').classList.add('flex');

            fetch('{{ route("checkout.process") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading
                document.getElementById('loading-modal').classList.add('hidden');
                document.getElementById('loading-modal').classList.remove('flex');

                console.log('Checkout response:', data);

                if (data.success) {
                    // Check if this is a mock order (for development)
                    if (data.razorpay_order_id && data.razorpay_order_id.startsWith('order_mock_')) {
                        // Simulate payment success for mock orders
                        console.log('Mock payment detected - simulating success');
                        setTimeout(() => {
                            const mockResponse = {
                                razorpay_payment_id: 'pay_mock_' + Date.now(),
                                razorpay_order_id: data.razorpay_order_id,
                                razorpay_signature: 'mock_signature_' + Date.now()
                            };
                            handlePaymentSuccess(mockResponse, data.order_id);
                        }, 1000);
                        return;
                    }

                    // Check if Razorpay is loaded
                    if (typeof Razorpay === 'undefined') {
                        alert('Razorpay library not loaded. Please refresh the page and try again.');
                        return;
                    }

                    // Check if we have a valid Razorpay key
                    if (!RAZORPAY_KEY || RAZORPAY_KEY === 'your_razorpay_key_id') {
                        alert('Razorpay is not properly configured. Please contact support.');
                        return;
                    }

                    console.log('Initializing Razorpay with key:', RAZORPAY_KEY);
                    console.log('Order data:', data);

                    // Initialize Razorpay
                    const options = {
                        key: RAZORPAY_KEY,
                        amount: data.amount,
                        currency: data.currency,
                        name: data.name,
                        description: data.description,
                        order_id: data.razorpay_order_id,
                        prefill: data.prefill,
                        theme: {
                            color: '#2563eb'
                        },
                        handler: function (response) {
                            // Payment successful
                            handlePaymentSuccess(response, data.order_id);
                        },
                        modal: {
                            ondismiss: function() {
                                // Payment cancelled
                                handlePaymentFailure(data.order_id);
                            }
                        }
                    };
                    
                    console.log('Razorpay options:', options);
                    
                    try {
                        const rzp = new Razorpay(options);
                        console.log('Razorpay instance created:', rzp);
                        rzp.open();
                    } catch (error) {
                        console.error('Error creating Razorpay instance:', error);
                        alert('Error initializing payment. Please try again.');
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                // Hide loading
                document.getElementById('loading-modal').classList.add('hidden');
                document.getElementById('loading-modal').classList.remove('flex');
                
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        // Initialize Tom Selects for state/city fields
        let shippingStateTS, shippingCityTS, billingStateTS, billingCityTS;

        function initLocationSelects() {
            // Initialize TomSelect with remote loading for states and cities
            shippingStateTS = new TomSelect('#shipping_state', {
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                create: false,
                maxOptions: 200,
                load: function(query, callback) {
                    fetch('{{ route("locations.states") }}?q=' + encodeURIComponent(query || ''))
                        .then(res => res.json())
                        .then(data => callback((data.states || []).map(s => ({ value: s, text: s }))))
                        .catch(() => callback());
                }
            });

            billingStateTS = document.getElementById('billing_state') ? new TomSelect('#billing_state', {
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                create: false,
                maxOptions: 200,
                load: function(query, callback) {
                    fetch('{{ route("locations.states") }}?q=' + encodeURIComponent(query || ''))
                        .then(r => r.json())
                        .then(d => callback((d.states || []).map(s => ({ value: s, text: s }))))
                        .catch(() => callback());
                }
            }) : null;

            // City selects use remote load and need selected state param
            shippingCityTS = new TomSelect('#shipping_city', {
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                create: true,
                load: function(query, callback) {
                    const state = shippingStateTS.getValue();
                    if (!state) return callback();
                    fetch('{{ route("locations.cities") }}?state=' + encodeURIComponent(state) + '&q=' + encodeURIComponent(query || ''))
                        .then(res => res.json())
                        .then(data => callback((data.cities || []).map(c => ({ value: c, text: c })) ))
                        .catch(() => callback());
                }
            });

            if (document.getElementById('billing_city')) {
                billingCityTS = new TomSelect('#billing_city', {
                    valueField: 'value',
                    labelField: 'text',
                    searchField: 'text',
                    create: true,
                    load: function(query, callback) {
                        const st = billingStateTS.getValue(); if (!st) return callback();
                        fetch('{{ route("locations.cities") }}?state=' + encodeURIComponent(st) + '&q=' + encodeURIComponent(query || ''))
                            .then(res => res.json())
                            .then(data => callback((data.cities || []).map(c => ({ value: c, text: c }))))
                            .catch(() => callback());
                    }
                });
            }

            // Wire change events to load cities when state selected
            shippingStateTS.on('change', function(value) {
                if (shippingCityTS) {
                    shippingCityTS.clearOptions();
                    shippingCityTS.load(function(cb) {
                        fetch('{{ route("locations.cities") }}?state=' + encodeURIComponent(value)).then(r => r.json()).then(d => cb((d.cities || []).map(c => ({ value: c, text: c }))));
                    });
                }
            });

            if (billingStateTS) {
                billingStateTS.on('change', function(value) {
                    if (billingCityTS) {
                        billingCityTS.clearOptions();
                        billingCityTS.load(function(cb) {
                            fetch('{{ route("locations.cities") }}?state=' + encodeURIComponent(value)).then(r => r.json()).then(d => cb((d.cities || []).map(c => ({ value: c, text: c }))));
                        });
                    }
                });
            }

            // When city changes, attempt to load pincodes and autofill
            shippingCityTS.on('change', function(value) { loadPincodesForCity(value, 'shipping_pincode'); });
            if (billingCityTS) billingCityTS.on('change', function(value) { loadPincodesForCity(value, 'billing_pincode'); });
        }

        function loadPincodesForCity(city, inputId) {
            if (!city) return;
            fetch('{{ route("locations.pincodes") }}?city=' + encodeURIComponent(city))
                .then(res => res.json())
                .then(data => {
                    if (data.pincodes && data.pincodes.length) {
                        const el = document.getElementById(inputId);
                        if (el) {
                            el.value = data.pincodes[0];
                            if (inputId === 'shipping_pincode') {
                                fetchShippingForPincode(data.pincodes[0]);
                            }
                        }
                    }
                })
                .catch(err => console.error('Failed to load pincodes', err));
        }

        function loadCitiesForState(state, targetTS) {
            if (!state) {
                if (targetTS) targetTS.clearOptions();
                return;
            }
            fetch('{{ route("locations.cities") }}?state=' + encodeURIComponent(state))
                .then(res => res.json())
                .then(data => {
                    const cities = data.cities || [];
                    if (!targetTS) return;
                    targetTS.clearOptions();
                    cities.forEach(c => targetTS.addOption({value: c, text: c}));
                    // Optionally set value if empty
                    if (!targetTS.getValue() && cities.length) targetTS.setValue(cities[0]);
                })
                .catch(err => console.error('Failed to load cities', err));
        }

        function onBillingStateChange(value) {
            loadCitiesForState(value, billingCityTS);
        }

        document.addEventListener('DOMContentLoaded', function() { initLocationSelects(); });

        function handlePaymentSuccess(response, orderId) {
            console.log('Payment Success Response:', response);
            console.log('Order ID:', orderId);
            
            fetch('{{ route("checkout.payment.success") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature,
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Verification response:', data);
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    console.error('Verification failed:', data);
                    alert('Payment verification failed: ' + (data.message || 'Please contact support.'));
                }
            })
            .catch(error => {
                console.error('Verification request failed:', error);
                alert('Payment was successful but verification failed. Please contact support with Payment ID: ' + response.razorpay_payment_id);
            });
        }

        function handlePaymentFailure(orderId) {
            window.location.href = '{{ route("checkout.payment.failed") }}?order_id=' + orderId;
        }
    </script>
</body>
</html>