<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'logo',
        'banner',
        'link',
        'serial',
        'status'
    ];

    protected $casts = [
        'status' => 'integer',
        'serial' => 'integer',
    ];

    /**
     * Scope for active sponsors
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for ordered sponsors
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('serial', 'asc');
    }

    /**
     * Get full logo URL
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset($this->logo) : null;
    }

    /**
     * Get full banner URL
     */
    public function getBannerUrlAttribute()
    {
        return $this->banner ? asset($this->banner) : null;
    }
}
