<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    public function variantItems(){
        return $this->hasMany(ProductVariantItem::class);
    }

    public function activeVariantItems(){
        return $this->hasMany(ProductVariantItem::class)->select(['product_variant_id','name','price','id']);
    }


}
