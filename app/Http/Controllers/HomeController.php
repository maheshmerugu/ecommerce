<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', true)
            ->orderBy('sort_order')
            ->get();

        // Hero carousel banners (type = hero)
        $heroBanners = Banner::hero()->get();

        // Promotional banners (type = promo) — shown below categories
        $promoBanners = Banner::promo()->get();

        // Fallback product carousel when no hero banners exist
        $carouselProducts = $heroBanners->isEmpty()
            ? Product::where('status', true)
                ->with('images')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get()
            : collect();

        $featuredProducts = Product::where('status', true)
            ->where('featured', true)
            ->with(['images', 'categories'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $latestProducts = Product::where('status', true)
            ->with(['images', 'categories'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Keep $banners for backward compatibility
        $banners = $heroBanners;

        return view('home', compact(
            'categories',
            'heroBanners',
            'promoBanners',
            'carouselProducts',
            'featuredProducts',
            'latestProducts',
            'banners'
        ));
    }
}
