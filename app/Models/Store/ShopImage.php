<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class ShopImage extends Model
{
    protected $table = 'shop_images';

    protected $fillable = [
        'seller_application_id',
        'image_url',
    ];

    public function sellerApplication()
    {
        return $this->belongsTo(SellerApplication::class, 'seller_application_id');
    }
}


