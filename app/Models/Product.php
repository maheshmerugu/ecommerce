<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'special_price',
        'special_price_from',
        'special_price_to',
        'quantity',
        'min_quantity',
        'track_quantity',
        'status',
        'weight',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'special_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'special_price_from' => 'date',
        'special_price_to' => 'date',
        'quantity' => 'integer',
        'min_quantity' => 'integer',
        'sort_order' => 'integer',
        'status' => 'boolean',
        'track_quantity' => 'boolean',
        'featured' => 'boolean'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get all categories for this product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Get all images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the main image for this product.
     */
    public function mainImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_main', true);
    }

    /**
     * Get cart items for this product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope to get only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to get only featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope to get products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Get the effective price (considering special price).
     */
    public function getEffectivePriceAttribute(): float
    {
        $now = now();
        
        if ($this->special_price && 
            ($this->special_price_from === null || $now >= $this->special_price_from) &&
            ($this->special_price_to === null || $now <= $this->special_price_to)) {
            return $this->special_price;
        }

        return $this->price;
    }

    /**
     * Check if product is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        if (!$this->track_quantity) {
            return true;
        }

        return $this->quantity > 0;
    }

    /**
     * Get the main image URL.
     */
    public function getMainImageUrlAttribute(): ?string
    {
        $mainImage = $this->mainImage()->first();
        return $mainImage ? asset('storage/' . $mainImage->image_path) : null;
    }
}
