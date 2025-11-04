<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class)->select('id','name','email', 'image', 'phone');
    }

    public function product(){
        return $this->belongsTo(Product::class)->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price');
    }


}
