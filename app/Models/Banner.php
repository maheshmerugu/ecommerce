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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position'  => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('position');
    }
}
