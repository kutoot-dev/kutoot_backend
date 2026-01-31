<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get all stores that have this tag
     */
    public function stores()
    {
        return $this->belongsToMany(
            SellerApplication::class,
            'store_tag',
            'tag_id',
            'seller_application_id'
        );
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = \Illuminate\Support\Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = \Illuminate\Support\Str::slug($tag->name);
            }
        });
    }
}
