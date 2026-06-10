<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Get or create cart for current user/session
     */
    private function getCart()
    {
        if (Auth::guard('customer')->check()) {
            // For logged in customers
            $customer = Auth::guard('customer')->user();
            $cart = $customer->getOrCreateCart();
        } else {
            // For guest users
            $sessionId = Session::getId();
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId
            ]);
        }

        return $cart;
    }

    /**
     * Display cart items
     */
    public function index()
    {
        $cart = $this->getCart();
        $cartItems = $cart->items()->with(['product', 'product.images'])->get();
        
        return view('cart.index', compact('cart', 'cartItems'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        try {
            Log::info('Cart add request received', [
                'request_data' => $request->all(),
                'is_ajax' => $request->ajax(),
                'accepts_json' => $request->acceptsJson(),
                'content_type' => $request->header('Content-Type'),
            ]);

            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'integer|min:1|max:10'
            ]);

            $product = Product::findOrFail($request->product_id);
            $quantity = $request->quantity ?? 1;
            
            // Get the price to use (special price if available, otherwise regular price)
            $price = $product->special_price && $product->special_price < $product->price 
                ? $product->special_price 
                : $product->price;

            $cart = $this->getCart();

            // Check if product already exists in cart
            $existingItem = $cart->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                // Update quantity if item exists
                $existingItem->quantity += $quantity;
                $existingItem->save();
                $message = 'Product quantity updated in cart!';
            } else {
                // Create new cart item
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price
                ]);
                $message = 'Product added to cart successfully!';
            }

            Log::info('Cart add successful', [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'cart_id' => $cart->id,
                'message' => $message
            ]);

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->acceptsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'cart_count' => $cart->total_quantity,
                    'cart_total' => number_format($cart->total_price, 2),
                    'shipping_fee' => $cart->items()->count() ? number_format((float) config('shop.shipping_fee', 150), 0) : number_format(0, 0),
                    'cart_total_with_shipping' => number_format($cart->total_price + ($cart->items()->count() ? (float) config('shop.shipping_fee', 150) : 0), 0),
                ]);
            }

            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Cart add failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->acceptsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add product to cart: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to add product to cart');
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        // Ensure the cart item belongs to current user's cart
        $cart = $this->getCart();
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!',
                'cart_count' => $cart->total_quantity,
                'cart_total' => number_format($cart->total_price, 0),
                'shipping_fee' => $cart->items()->count() ? number_format((float) config('shop.shipping_fee', 150), 0) : number_format(0, 0),
                'cart_total_with_shipping' => number_format($cart->total_price + ($cart->items()->count() ? (float) config('shop.shipping_fee', 150) : 0), 0),
                'item_total' => number_format($cartItem->quantity * $cartItem->price, 0)
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
    }

    /**
     * Remove item from cart
     */
    public function remove(CartItem $cartItem)
    {
        // Ensure the cart item belongs to current user's cart
        $cart = $this->getCart();
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

            if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart!',
                'cart_count' => $cart->total_quantity,
                'cart_total' => number_format($cart->total_price, 0),
                'shipping_fee' => $cart->items()->count() ? number_format((float) config('shop.shipping_fee', 150), 0) : number_format(0, 0),
                'cart_total_with_shipping' => number_format($cart->total_price + ($cart->items()->count() ? (float) config('shop.shipping_fee', 150) : 0), 0)
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        try {
            Log::info('Cart clear request received', [
                'is_ajax' => request()->ajax(),
                'accepts_json' => request()->acceptsJson(),
                'content_type' => request()->header('Content-Type'),
            ]);

            $cart = $this->getCart();
            $itemCount = $cart->items()->count();
            $cart->items()->delete();

            Log::info('Cart cleared successfully', [
                'cart_id' => $cart->id,
                'items_deleted' => $itemCount
            ]);

            if (request()->ajax() || request()->wantsJson() || request()->acceptsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart cleared successfully!',
                    'cart_count' => 0,
                    'cart_total' => '0.00',
                    'shipping_fee' => number_format(0, 0),
                    'cart_total_with_shipping' => number_format(0, 0)
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Cart cleared successfully!');
            
        } catch (\Exception $e) {
            Log::error('Cart clear failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax() || request()->wantsJson() || request()->acceptsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear cart: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('cart.index')->with('error', 'Failed to clear cart');
        }
    }

    /**
     * Get cart count for display
     */
    public function count()
    {
        $cart = $this->getCart();
        
        return response()->json([
            'count' => $cart->total_quantity
        ]);
    }
}
