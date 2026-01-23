<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ImageHelper;

class StoreBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'image_tablet',
        'image_mobile',
        'link',
        'button_text',
        'location',
        'serial',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'status' => 'integer',
        'serial' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'image_tablet_url',
        'image_mobile_url',
        'is_active',
    ];

    /**
     * Scope to get only active banners
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get banners by location
     */
    public function scopeLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope to get currently valid banners (within date range)
     */
    public function scopeValid($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $now);
        });
    }

    /**
     * Get full URL for desktop image
     */
    public function getImageUrlAttribute(): ?string
    {
        return ImageHelper::url($this->image);
    }

    /**
     * Get full URL for tablet image
     */
    public function getImageTabletUrlAttribute(): ?string
    {
        return ImageHelper::url($this->image_tablet);
    }

    /**
     * Get full URL for mobile image
     */
    public function getImageMobileUrlAttribute(): ?string
    {
        return ImageHelper::url($this->image_mobile);
    }

    /**
     * Check if banner is currently active (status + date range)
     */
    public function getIsActiveAttribute(): bool
    {
        if ($this->status !== 1) {
            return false;
        }

        $now = now();

        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get responsive image data for frontend
     */
    public function getResponsiveImagesAttribute(): array
    {
        return [
            'desktop' => $this->image_url,
            'tablet' => $this->image_tablet_url ?? $this->image_url,
            'mobile' => $this->image_mobile_url ?? $this->image_tablet_url ?? $this->image_url,
        ];
    }

    /**
     * Delete all associated images
     */
    public function deleteImages(): int
    {
        return ImageHelper::deleteMultiple([
            $this->image,
            $this->image_tablet,
            $this->image_mobile,
        ]);
    }

    /**
     * Get old image paths for replacement
     */
    public function getOldImagePaths(): array
    {
        return [
            'desktop' => $this->image,
            'tablet' => $this->image_tablet,
            'mobile' => $this->image_mobile,
        ];
    }
}
