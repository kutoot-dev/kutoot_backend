<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSpecificationKey extends Model
{
    use HasFactory;

    public function productSpecifications(){
        return $this->hasMany(ProductSpecification::class);
    }
}
