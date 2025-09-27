<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * Get wishlist for current user/session
     */
    private function getWishlistQuery()
    {
        if (Auth::guard('customer')->check()) {
            // For logged in customers
            return Wishlist::where('customer_id', Auth::guard('customer')->id());
        } else {
            // For guest users
            $sessionId = Session::getId();
            return Wishlist::where('session_id', $sessionId);
        }
    }

    /**
     * Display wishlist items
     */
    public function index()
    {
        $wishlistItems = $this->getWishlistQuery()
            ->with(['product.images', 'product.categories'])
            ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist
     */
    public function add(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);

            $product = Product::findOrFail($request->product_id);

            // Check if already in wishlist
            $existingItem = $this->getWishlistQuery()
                ->where('product_id', $product->id)
                ->first();

            if ($existingItem) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product is already in your wishlist'
                    ]);
                }
                return redirect()->back()->with('error', 'Product is already in your wishlist');
            }

            // Create wishlist item
            $wishlistData = [
                'product_id' => $product->id
            ];

            if (Auth::guard('customer')->check()) {
                $wishlistData['customer_id'] = Auth::guard('customer')->id();
            } else {
                $wishlistData['session_id'] = Session::getId();
            }

            Wishlist::create($wishlistData);

            $message = 'Product added to wishlist successfully!';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'wishlist_count' => $this->getWishlistQuery()->count()
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Wishlist add failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add product to wishlist'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to add product to wishlist');
        }
    }

    /**
     * Remove product from wishlist
     */
    public function remove(Wishlist $wishlist)
    {
        try {
            // Ensure the wishlist item belongs to current user/session
            $query = $this->getWishlistQuery();
            
            if (Auth::guard('customer')->check()) {
                if ($wishlist->customer_id !== Auth::guard('customer')->id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            } else {
                if ($wishlist->session_id !== Session::getId()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            }

            $wishlist->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from wishlist!',
                    'wishlist_count' => $this->getWishlistQuery()->count()
                ]);
            }

            return redirect()->back()->with('success', 'Product removed from wishlist!');

        } catch (\Exception $e) {
            Log::error('Wishlist remove failed', [
                'error' => $e->getMessage(),
                'wishlist_id' => $wishlist->id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove product from wishlist'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to remove product from wishlist');
        }
    }

    /**
     * Clear entire wishlist
     */
    public function clear()
    {
        try {
            $this->getWishlistQuery()->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Wishlist cleared successfully!',
                    'wishlist_count' => 0
                ]);
            }

            return redirect()->back()->with('success', 'Wishlist cleared successfully!');

        } catch (\Exception $e) {
            Log::error('Wishlist clear failed', ['error' => $e->getMessage()]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear wishlist'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to clear wishlist');
        }
    }

    /**
     * Get wishlist count for display
     */
    public function count()
    {
        $count = $this->getWishlistQuery()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }
}
