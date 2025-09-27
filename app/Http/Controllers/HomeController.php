<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured categories (up to 6)
        $categories = Category::where('status', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->take(6)
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

        return view('home', compact('categories', 'featuredProducts', 'latestProducts'));
    }
}