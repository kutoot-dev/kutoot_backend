<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    use HasFactory;

    public function key(){
        return $this->belongsTo(ProductSpecificationKey::class,'product_specification_key_id');
    }


}
