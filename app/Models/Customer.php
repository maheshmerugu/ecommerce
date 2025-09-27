<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'gender',
        'phone',
        'is_active',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all addresses for this customer.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the default shipping address.
     */
    public function defaultShippingAddress(): HasMany
    {
        return $this->hasMany(Address::class)
                    ->where('type', 'shipping')
                    ->where('is_default', true);
    }

    /**
     * Get the default billing address.
     */
    public function defaultBillingAddress(): HasMany
    {
        return $this->hasMany(Address::class)
                    ->where('type', 'billing')
                    ->where('is_default', true);
    }

    /**
     * Get all carts for this customer.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the current cart for this customer.
     */
    public function currentCart(): HasMany
    {
        return $this->hasMany(Cart::class)->latest();
    }

    /**
     * Get all orders for this customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Scope to get only active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
