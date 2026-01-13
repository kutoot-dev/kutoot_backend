<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function seller(){
        return $this->belongsTo(Vendor::class, 'seller_id');
    }

    public function scopeSellerBrands($query){
        return $query->whereNotNull('seller_id');
    }

    public function scopeAdminBrands($query){
        return $query->whereNull('seller_id');
    }
}
