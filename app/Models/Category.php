<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'icon',
        'image',
        'status',
        'is_featured',
        'is_top',
        'is_popular',
        'is_trending',
    ];

    public function subCategories(){
        return $this->hasMany(SubCategory::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function activeSubCategories(){
        return $this->hasMany(SubCategory::class)->where('status',1)->select(['id','name','slug','category_id']);
    }
}
