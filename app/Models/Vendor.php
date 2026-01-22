<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'shop_name',
        'slug',
        'owner_name',
        'email',
        'phone',
        'address',
        'description',
        'greeting_msg',
        'open_at',
        'closed_at',
        'address_latitude',
        'address_longitude',
        'status',
        'is_featured',
        'is_top',
        'verified_token',
        'banner_image',
        'logo',
        'razorpay_account_id',
    ];

    protected $appends = ['averageRating'];

    public function getAverageRatingAttribute()
    {
        return $this->activeReviews()->avg('rating') ?: '0';
    }

    public function socialLinks()
    {
        return $this->hasMany(VendorSocialLink::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    public function brands()
    {
        return $this->hasMany(Brand::class, 'seller_id');
    }

    public function activeReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_vendor_id');
    }


}
