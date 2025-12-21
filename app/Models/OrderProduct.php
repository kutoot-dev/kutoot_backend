<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class OrderProduct extends Model
{
    use HasFactory;

    public function seller(){
        return $this->belongsTo(Vendor::class,'seller_id');
    }

    public function orderProductVariants(){
        return $this->hasMany(OrderProductVariant::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}   
