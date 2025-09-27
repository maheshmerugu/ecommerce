<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'session_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total',
        'currency',
        'billing_address',
        'shipping_address',
        'payment_status',
        'payment_method',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'notes',
        'shipped_at',
        'delivered_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    /**
     * Get the customer that owns this order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get all payments for this order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . now()->format('Y') . '-' . strtoupper(uniqid());
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by payment status.
     */
    public function scopePaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Get the formatted billing address.
     */
    public function getFormattedBillingAddressAttribute(): string
    {
        $address = $this->billing_address;
        
        if (!$address) {
            return '';
        }

        $formatted = $address['first_name'] . ' ' . $address['last_name'] . "\n";
        
        if (!empty($address['company'])) {
            $formatted .= $address['company'] . "\n";
        }
        
        $formatted .= $address['address_line_1'] . "\n";
        
        if (!empty($address['address_line_2'])) {
            $formatted .= $address['address_line_2'] . "\n";
        }
        
        $formatted .= $address['city'] . ', ' . $address['state'] . ' ' . $address['postal_code'] . "\n";
        $formatted .= $address['country'];
        
        return $formatted;
    }

    /**
     * Get the formatted shipping address.
     */
    public function getFormattedShippingAddressAttribute(): string
    {
        $address = $this->shipping_address;
        
        if (!$address) {
            return '';
        }

        $formatted = $address['first_name'] . ' ' . $address['last_name'] . "\n";
        
        if (!empty($address['company'])) {
            $formatted .= $address['company'] . "\n";
        }
        
        $formatted .= $address['address_line_1'] . "\n";
        
        if (!empty($address['address_line_2'])) {
            $formatted .= $address['address_line_2'] . "\n";
        }
        
        $formatted .= $address['city'] . ', ' . $address['state'] . ' ' . $address['postal_code'] . "\n";
        $formatted .= $address['country'];
        
        return $formatted;
    }
}
