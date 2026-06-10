<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class CheckoutController extends Controller
{
    private $razorpay;
    
    public function __construct()
    {
        // Prefer DB settings, fall back to .env
        $keyId     = Setting::get('razorpay_key_id')     ?: env('RAZORPAY_KEY_ID');
        $keySecret = Setting::get('razorpay_key_secret') ?: env('RAZORPAY_KEY_SECRET');

        $placeholders = ['your_razorpay_key_id', 'your_razorpay_key_secret', '', null];

        if (in_array($keyId, $placeholders, true) || in_array($keySecret, $placeholders, true)) {
            Log::warning('Razorpay credentials not properly configured');
            $this->razorpay = null;
        } else {
            try {
                $this->razorpay = new Api($keyId, $keySecret);
                Log::info('Razorpay initialized successfully');
            } catch (\Exception $e) {
                Log::error('Razorpay initialization failed: ' . $e->getMessage());
                $this->razorpay = null;
            }
        }
    }

    /**
     * Display checkout page
     */
    public function index()
    {
        $cart = $this->getCart();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $cartItems = $cart->items()->with('product.images')->get();
        $user = Auth::guard('customer')->user();

        // Load saved addresses for authenticated user
        $addresses = $user ? $user->addresses : collect();

        // Pass Razorpay Key ID to view (DB setting first, then .env fallback)
        $razorpayKeyId = Setting::get('razorpay_key_id') ?: env('RAZORPAY_KEY_ID', '');

        return view('checkout.index', compact('cart', 'cartItems', 'addresses', 'user', 'razorpayKeyId'));
    }

    /**
     * Process checkout and create Razorpay order
     */
    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_pincode' => 'required|string|max:10',
            'billing_address' => 'nullable|string',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_pincode' => 'nullable|string|max:10',
            'same_as_shipping' => 'required|in:0,1,true,false'
        ]);

        // Check if user is authenticated
        if (!Auth::guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }

        $cart = $this->getCart();
        
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty']);
        }

        // Stock validation before creating order
        $cart->load('items.product');
        foreach ($cart->items as $item) {
            if (!$item->product) {
                return response()->json(['success' => false, 'message' => 'A product in your cart is no longer available.']);
            }
            if ($item->product->track_quantity && $item->product->quantity < $item->quantity) {
                $available = $item->product->quantity;
                return response()->json([
                    'success' => false,
                    'message' => "\"" . $item->product->name . "\" only has {$available} unit(s) available. Please update your cart."
                ]);
            }
        }

        try {
            DB::beginTransaction();

            $customerId = Auth::guard('customer')->id();
            if (!$customerId) {
                throw new \Exception('Customer ID not found');
            }

            // Create order
            $order = $this->createOrder($request, $cart);
            
            // Create Razorpay order
            if (!$this->razorpay) {
                // Mock Razorpay response for development/testing
                $razorpayOrder = [
                    'id' => 'order_mock_' . time(),
                    'amount' => $order->total * 100,
                    'currency' => 'INR',
                    'status' => 'created'
                ];
            } else {
                try {
                    // Ensure amount is an integer (convert rupees to paisa)
                    $amountInPaisa = (int) round($order->total * 100);
                    
                    // Debug logging
                    Log::info('Razorpay Order Creation', [
                        'original_total' => $order->total,
                        'amount_in_paisa' => $amountInPaisa,
                        'order_number' => $order->order_number
                    ]);
                    
                    $razorpayOrder = $this->razorpay->order->create([
                        'amount' => $amountInPaisa,
                        'currency' => 'INR',
                        'receipt' => $order->order_number,
                        'payment_capture' => 1
                    ]);
                } catch (\Exception $razorpayError) {
                    throw new \Exception('Razorpay API Error: ' . $razorpayError->getMessage());
                }
            }

            // Update order with Razorpay order ID
            $order->update(['razorpay_order_id' => $razorpayOrder['id']]);

            DB::commit();

            // Ensure consistent amount formatting
            $amountInPaisa = (int) round($order->total * 100);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $amountInPaisa,
                'currency' => 'INR',
                'name' => Setting::get('store_name') ?: config('app.name'),
                'description' => 'Order #' . $order->order_number,
                'prefill' => [
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'contact' => $request->customer_phone
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Order creation failed: ' . $e->getMessage()]);
        }
    }
   
    public function paymentSuccess(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'order_id' => 'required|exists:orders,id'
        ]);

        try {
            // Log the payment verification attempt
            Log::info('Payment verification attempt', [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'order_id' => $request->order_id
            ]);

            // Verify payment signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            // Skip signature verification if Razorpay is not properly initialized (development mode)
            $skipVerification = env('RAZORPAY_SKIP_VERIFICATION', false);
            
            if ($this->razorpay && !$skipVerification) {
                try {
                    $this->razorpay->utility->verifyPaymentSignature($attributes);
                    Log::info('Payment signature verified successfully');
                } catch (\Exception $verificationError) {
                    Log::error('Payment signature verification failed: ' . $verificationError->getMessage());
                    throw new \Exception('Payment signature verification failed: ' . $verificationError->getMessage());
                }
            } else {
                if ($skipVerification) {
                    Log::warning('Skipping payment verification - RAZORPAY_SKIP_VERIFICATION is enabled');
                } else {
                    Log::warning('Skipping payment verification - Razorpay not initialized');
                }
            }

            // Update order
            $order = Order::with('items.product')->findOrFail($request->order_id);
            
            // Check if this order belongs to the current user
            if ($order->customer_id !== Auth::guard('customer')->id()) {
                throw new \Exception('Order does not belong to the current user');
            }
            
            $order->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
                'payment_status'      => 'paid',
                'status'              => 'processing',
            ]);

            // Deduct inventory
            foreach ($order->items as $item) {
                if ($item->product && $item->product->track_quantity) {
                    $item->product->decrement('quantity', $item->quantity);
                }
            }

            // Record payment
            Payment::create([
                'order_id'               => $order->id,
                'payment_method'         => 'razorpay',
                'transaction_id'         => $request->razorpay_payment_id,
                'gateway_transaction_id' => $request->razorpay_order_id,
                'amount'                 => $order->total,
                'currency'               => $order->currency ?? 'INR',
                'status'                 => 'completed',
                'gateway_response'       => [
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_order_id'   => $request->razorpay_order_id,
                    'razorpay_signature'  => $request->razorpay_signature,
                ],
                'processed_at' => now(),
            ]);

            Log::info('Order updated successfully', ['order_id' => $order->id]);

            // Clear cart
            $this->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Payment successful!',
                'redirect' => route('checkout.success', $order->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display order success page
     */
    public function success($orderId)
    {
        $order = Order::with(['items.product.images'])->findOrFail($orderId);
        
        // Ensure user can access this order
        if (!$this->canAccessOrder($order)) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * Handle payment failure
     */
    public function paymentFailed(Request $request)
    {
        $orderId = $request->order_id;
        
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);
            }
        }

        return redirect()->route('checkout.index')->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Create order from cart and form data
     */
    private function createOrder(Request $request, $cart)
    {
        // Convert same_as_shipping to boolean
        $sameAsShipping = in_array($request->same_as_shipping, [1, '1', 'true', true], true);
        
        $customerId = Auth::guard('customer')->id();
        if (!$customerId) {
            throw new \Exception('Unable to get customer ID for order creation');
        }
        
        $orderNumber = 'ORD-' . strtoupper(Str::random(8)) . '-' . time();
        
        // Shipping fee: DB setting > config > default 150
        $shippingFee = (float) (Setting::get('shipping_fee') ?: config('shop.shipping_fee', 150));

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_id' => $customerId,
            'session_id' => Session::getId(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'status' => 'pending',
            'subtotal' => $cart->total_price,
            'tax_amount' => 0,
            'shipping_amount' => $shippingFee,
            'discount_amount' => 0,
            'total' => $cart->total_price + $shippingFee,
            'currency' => 'INR',
            'billing_address' => json_encode([
                'address' => $sameAsShipping ? $request->shipping_address : $request->billing_address,
                'city' => $sameAsShipping ? $request->shipping_city : $request->billing_city,
                'state' => $sameAsShipping ? $request->shipping_state : $request->billing_state,
                'pincode' => $sameAsShipping ? $request->shipping_pincode : $request->billing_pincode,
                'country' => 'India'
            ]),
            'shipping_address' => json_encode([
                'address' => $request->shipping_address,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'pincode' => $request->shipping_pincode,
                'country' => 'India'
            ]),
            'payment_status' => 'pending',
            'payment_method' => 'razorpay'
        ]);

        // Create order items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_sku' => $item->product->sku ?? 'N/A',
                'price' => $item->price,
                'quantity' => $item->quantity,
                'total' => $item->price * $item->quantity
            ]);
        }

        // Save address for authenticated users if it's a new address
        if ($request->input('saved_address') === 'new') {
            $this->saveUserAddress($request);
        }

        return $order;
    }

    /**
     * Get current cart
     */
    private function getCart()
    {
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $cart = $customer->getOrCreateCart();
            
            // Load relationships
            $cart->load('items.product.images');
            
            return $cart;
        } else {
            $sessionId = Session::getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
            
            // Load relationships
            $cart->load('items.product.images');
            
            return $cart;
        }
    }

    /**
     * Clear cart after successful order
     */
    private function clearCart()
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->items()->delete();
        }
    }

    /**
     * Check if user can access order
     */
    private function canAccessOrder($order)
    {
        return $order->customer_id === Auth::guard('customer')->id();
    }

    /**
     * Save address for authenticated user
     */
    private function saveUserAddress(Request $request)
    {
        try {
            Address::create([
                'customer_id' => Auth::guard('customer')->id(),
                'type' => 'shipping',
                'first_name' => $request->first_name ?? explode(' ', $request->customer_name)[0] ?? '',
                'last_name' => $request->last_name ?? (explode(' ', $request->customer_name)[1] ?? ''),
                'phone' => $request->customer_phone,
                'address_line_1' => $request->shipping_address,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'postal_code' => $request->shipping_pincode,
                'country' => 'India',
                'is_default' => Address::where('customer_id', Auth::guard('customer')->id())->count() === 0 // Make first address default
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the order
            Log::error('Failed to save user address: ' . $e->getMessage());
        }
    }
}
