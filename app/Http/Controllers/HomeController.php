<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Get only the Toys Cars category for the categories carousel
        $categories = Category::where('status', true)
            ->where('name', 'Toys Cars')
            ->orderBy('sort_order')
            ->get();

        // Get products for the hero carousel: any product in categories matching 'car'
        $carouselProducts = Product::whereHas('categories', function ($q) {
                $q->where('name', 'like', '%car%');
            })
            ->where('status', true)
            ->with('images')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Get featured products (up to 8)
        $featuredProducts = Product::where('status', true)
            ->where('featured', true)
            ->with(['images', 'categories'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Get latest products (up to 8)
        $latestProducts = Product::where('status', true)
            ->with(['images', 'categories'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Get active banners for hero carousel
        $banners = Banner::active()->orderBy('position')->get();

        return view('home', compact('categories', 'carouselProducts', 'featuredProducts', 'latestProducts', 'banners'));
    }
}