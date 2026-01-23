<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    protected $table = 'store_categories';

    protected $fillable = [
        'name',
        'image',
        'icon',
        'serial',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'serial' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('serial', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get stores in this category
     */
    public function stores()
    {
        return $this->belongsToMany(\App\Models\Vendor::class, 'store_category_vendor', 'store_category_id', 'vendor_id');
    }
}


