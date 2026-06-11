<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'caption',
        'image',
        'link',
        'position',
        'type',       // 'hero' | 'promo'
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position'  => 'integer',
    ];

    /** Active banners of any type ordered by position */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('position');
    }

    /** Hero carousel banners (main super-sale slider) */
    public function scopeHero($query)
    {
        return $query->where('is_active', true)
                     ->where('type', 'hero')
                     ->orderBy('position');
    }

    /** Promotional offer banners (below categories section) */
    public function scopePromo($query)
    {
        return $query->where('is_active', true)
                     ->where('type', 'promo')
                     ->orderBy('position');
    }
}
