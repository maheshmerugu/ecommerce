<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['categories', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'sku' => 'required|string|unique:products',
                'price' => 'required|numeric|min:0',
                'special_price' => 'nullable|numeric|min:0|lt:price',
                'special_price_from' => 'nullable|date',
                'special_price_to' => 'nullable|date|after_or_equal:special_price_from',
                'quantity' => 'required|integer|min:0',
                'min_quantity' => 'nullable|integer|min:1',
                'weight' => 'nullable|numeric|min:0',
                'status' => 'boolean',
                'featured' => 'boolean',
                'categories' => 'array',
                'categories.*' => 'exists:categories,id',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery_images' => 'nullable|array',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        }

        try {
            // Check for duplicate slug
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;
            
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Create product
            $product = Product::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'sku' => $request->sku,
                'price' => $request->price,
                'special_price' => $request->special_price,
                'special_price_from' => $request->special_price_from,
                'special_price_to' => $request->special_price_to,
                'quantity' => $request->quantity,
                'min_quantity' => $request->min_quantity ?? 1,
                'track_quantity' => $request->track_quantity == '1',
                'status' => $request->status == '1',
                'weight' => $request->weight,
                'featured' => $request->featured == '1',
                'meta_title' => $request->meta_title ?? $request->name,
                'meta_description' => $request->meta_description ?? $request->short_description,
                'meta_keywords' => $request->meta_keywords,
                'sort_order' => 0,
            ]);

            // Sync categories (prevents duplicates)
            try {
                if ($request->has('categories') && is_array($request->categories)) {
                    // Remove duplicates and filter out invalid category IDs
                    $categoryIds = array_unique(array_filter($request->categories));
                    $validCategories = Category::whereIn('id', $categoryIds)
                        ->where('status', true)
                        ->pluck('id')
                        ->toArray();
                    
                    // Use sync to prevent duplicate relationships
                    $product->categories()->sync($validCategories);
                } else {
                    // If no categories selected, ensure empty relationship
                    $product->categories()->sync([]);
                }
            } catch (\Exception $e) {
                Log::error('Category assignment failed: ' . $e->getMessage(), [
                    'product_id' => $product->id,
                    'categories' => $request->categories ?? []
                ]);
                throw new \Exception('Failed to assign categories: ' . $e->getMessage());
            }

            // Handle main image upload
            if ($request->hasFile('main_image')) {
                try {
                    $mainImage = $request->file('main_image');
                    $mainImagePath = $mainImage->store('products', 'public');
                    
                    // Update product with main image
                    $product->update(['image' => $mainImagePath]);
                    
                    // Create product image record
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $mainImagePath,
                        'alt_text' => $product->name . ' - Main Image',
                        'is_main' => true,
                        'sort_order' => 1
                    ]);
                } catch (\Exception $e) {
                    // Delete the product if image upload fails
                    $product->delete();
                    throw new \Exception('Failed to upload main image: ' . $e->getMessage());
                }
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $sortOrder = 2;
                $uploadedImages = [];
                
                try {
                    foreach ($request->file('gallery_images') as $galleryImage) {
                        $galleryImagePath = $galleryImage->store('products', 'public');
                        $uploadedImages[] = $galleryImagePath;
                        
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $galleryImagePath,
                            'alt_text' => $product->name . ' - Gallery Image ' . ($sortOrder - 1),
                            'is_main' => false,
                            'sort_order' => $sortOrder
                        ]);
                        
                        $sortOrder++;
                    }
                } catch (\Exception $e) {
                    // Clean up uploaded images and delete product
                    foreach ($uploadedImages as $imagePath) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    $product->delete();
                    throw new \Exception('Failed to upload gallery images: ' . $e->getMessage());
                }
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product "' . $product->name . '" created successfully!');

        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage(), [
                'user_id' => auth('admin')->id(),
                'request_data' => $request->except(['main_image', 'gallery_images'])
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['categories', 'images']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
        $product->load(['categories', 'images']);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'sku' => ['required', 'string', Rule::unique('products')->ignore($product->id)],
                'price' => 'required|numeric|min:0',
                'special_price' => 'nullable|numeric|min:0|lt:price',
                'special_price_from' => 'nullable|date',
                'special_price_to' => 'nullable|date|after_or_equal:special_price_from',
                'quantity' => 'required|integer|min:0',
                'min_quantity' => 'nullable|integer|min:1',
                'weight' => 'nullable|numeric|min:0',
                'status' => 'boolean',
                'featured' => 'boolean',
                'categories' => 'array',
                'categories.*' => 'exists:categories,id',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery_images' => 'nullable|array',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'remove_images' => 'nullable|array',
                'remove_images.*' => 'exists:product_images,id',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        }

        try {

        // Update product
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'sku' => $request->sku,
            'price' => $request->price,
            'special_price' => $request->special_price,
            'special_price_from' => $request->special_price_from,
            'special_price_to' => $request->special_price_to,
            'quantity' => $request->quantity,
            'min_quantity' => $request->min_quantity ?? 1,
            'track_quantity' => $request->track_quantity == '1',
            'status' => $request->status == '1',
            'weight' => $request->weight,
            'featured' => $request->featured == '1',
            'meta_title' => $request->meta_title ?? $request->name,
            'meta_description' => $request->meta_description ?? $request->short_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        // Update categories (sync prevents duplicates)
        try {
            if ($request->has('categories') && is_array($request->categories)) {
                // Filter out any invalid category IDs
                $validCategories = Category::whereIn('id', $request->categories)
                    ->where('status', true)
                    ->pluck('id')
                    ->toArray();
                
                $product->categories()->sync($validCategories);
                
                Log::info('Categories updated for product', [
                    'product_id' => $product->id,
                    'requested_categories' => $request->categories,
                    'synced_categories' => $validCategories
                ]);
            } else {
                // If no categories selected, remove all
                $product->categories()->sync([]);
            }
        } catch (\Exception $e) {
            Log::error('Category update failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'categories' => $request->categories
            ]);
            throw new \Exception('Failed to update categories: ' . $e->getMessage());
        }

        // Handle image removal
        if ($request->has('remove_images')) {
            $imagesToRemove = ProductImage::whereIn('id', $request->remove_images)
                ->where('product_id', $product->id)
                ->get();
                
            foreach ($imagesToRemove as $imageToRemove) {
                // Delete file from storage
                Storage::disk('public')->delete($imageToRemove->image_path);
                
                // If this was the main product image, clear it
                if ($imageToRemove->is_main) {
                    $product->update(['image' => null]);
                }
                
                // Delete database record
                $imageToRemove->delete();
            }
        }

        // Handle new main image upload
        if ($request->hasFile('main_image')) {
            // Remove old main image
            $oldMainImage = $product->images()->where('is_main', true)->first();
            if ($oldMainImage) {
                Storage::disk('public')->delete($oldMainImage->image_path);
                $oldMainImage->delete();
            }
            
            $mainImage = $request->file('main_image');
            $mainImagePath = $mainImage->store('products', 'public');
            
            // Update product with main image
            $product->update(['image' => $mainImagePath]);
            
            // Create new main image record
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $mainImagePath,
                'alt_text' => $product->name . ' - Main Image',
                'is_main' => true,
                'sort_order' => 1
            ]);
        }

        // Handle new gallery images upload
        if ($request->hasFile('gallery_images')) {
            $maxSortOrder = $product->images()->max('sort_order') ?? 1;
            $sortOrder = $maxSortOrder + 1;
            
            foreach ($request->file('gallery_images') as $galleryImage) {
                $galleryImagePath = $galleryImage->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $galleryImagePath,
                    'alt_text' => $product->name . ' - Gallery Image ' . ($sortOrder - 1),
                    'is_main' => false,
                    'sort_order' => $sortOrder
                ]);
                
                $sortOrder++;
            }
        }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product "' . $product->name . '" updated successfully!');

        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'user_id' => auth('admin')->id(),
                'request_data' => $request->except(['main_image', 'gallery_images'])
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete all product images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        // Delete product (images will be deleted via cascade)
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['status' => !$product->status]);
        
        $status = $product->status ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Product {$status} successfully!");
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Product $product)
    {
        $product->update(['featured' => !$product->featured]);
        
        $status = $product->featured ? 'marked as featured' : 'unmarked as featured';
        return redirect()->back()->with('success', "Product {$status} successfully!");
    }
}
