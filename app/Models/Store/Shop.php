<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'shops';

    protected $fillable = [
        'seller_id',
        'shop_code',
        'shop_name',
        'category',
        'owner_name',
        'phone',
        'email',
        'gst_number',
        'address',
        'google_map_url',
        'location_lat',
        'location_lng',
        'min_bill_amount',
        'razorpay_account_id',
    ];

    protected $casts = [
        'location_lat' => 'float',
        'location_lng' => 'float',
        'min_bill_amount' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function images()
    {
        return $this->hasMany(ShopImage::class, 'shop_id');
    }

    public function visitors()
    {
        return $this->hasMany(ShopVisitor::class, 'shop_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'shop_id');
    }

    public function adminSettings()
    {
        return $this->hasMany(AdminShopCommissionDiscount::class, 'shop_id');
    }
}


