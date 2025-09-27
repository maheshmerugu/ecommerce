<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', true)
            ->with(['images', 'categories']);

        // Filter by category if provided
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('categories.id', $category->id);
                });
            }
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('short_description', 'like', '%' . $request->search . '%');
            });
        }

        // Price range filtering
        if ($request->has('price_ranges') && is_array($request->price_ranges)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->price_ranges as $range) {
                    switch ($range) {
                        case 'under_50':
                            $q->orWhere(function ($subQ) {
                                $subQ->where(function ($priceQ) {
                                    $priceQ->whereRaw('(CASE WHEN special_price IS NOT NULL AND special_price < price THEN special_price ELSE price END) < ?', [50]);
                                });
                            });
                            break;
                        case '50_100':
                            $q->orWhere(function ($subQ) {
                                $subQ->where(function ($priceQ) {
                                    $priceQ->whereRaw('(CASE WHEN special_price IS NOT NULL AND special_price < price THEN special_price ELSE price END) >= ? AND (CASE WHEN special_price IS NOT NULL AND special_price < price THEN special_price ELSE price END) <= ?', [50, 100]);
                                });
                            });
                            break;
                        case '100_200':
                            $q->orWhere(function ($subQ) {
                                $subQ->where(function ($priceQ) {
                                    $priceQ->whereRaw('(CASE WHEN special_price IS NOT NULL AND special_price < price THEN special_price ELSE price END) >= ? AND (CASE WHEN special_price IS NOT NULL AND special_price < price THEN special_price ELSE price END) <= ?', [100, 200]);
                                });
                            });
                            break;
                        case 'over_200':
                            $q->orWhere(function ($subQ) {
                                $subQ->where(function ($priceQ) {
                                    $priceQ->whereRaw('(CASE WHEN special_price IS NOT NULL AND special_price < price THEN special_price ELSE price END) > ?', [200]);
                                });
                            });
                            break;
                    }
                }
            });
        }

        // Custom price range filtering (min/max)
        if ($request->has('min_price') && $request->min_price) {
            $query->whereRaw('(CASE WHEN special_price IS NOT NULL AND special_price < price THEN special_price ELSE price END) >= ?', [$request->min_price]);
        }
        
        if ($request->has('max_price') && $request->max_price) {
            $query->whereRaw('(CASE WHEN special_price IS NOT NULL AND special_price < price THEN special_price ELSE price END) <= ?', [$request->max_price]);
        }

        // Sort functionality
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'featured':
                $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::where('status', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', true)
            ->with(['images', 'categories'])
            ->firstOrFail();

        // Get related products from the same categories
        $relatedProducts = Product::where('status', true)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->with(['images', 'categories'])
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
