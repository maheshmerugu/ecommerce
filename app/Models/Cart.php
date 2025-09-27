<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'customer_id'
    ];

    /**
     * Get the customer that owns this cart.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all items in this cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total quantity of items in cart.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get the total price of items in cart.
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Add a product to the cart.
     */
    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $existingItem = $this->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            return $existingItem;
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->effective_price
        ]);
    }

    /**
     * Remove a product from the cart.
     */
    public function removeProduct(Product $product): bool
    {
        return $this->items()->where('product_id', $product->id)->delete();
    }

    /**
     * Update quantity of a product in cart.
     */
    public function updateProductQuantity(Product $product, int $quantity): ?CartItem
    {
        $item = $this->items()->where('product_id', $product->id)->first();

        if ($item) {
            if ($quantity <= 0) {
                $item->delete();
                return null;
            }

            $item->update(['quantity' => $quantity]);
            return $item;
        }

        return null;
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): bool
    {
        return $this->items()->delete();
    }
}
