<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class Product extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     'zoho_item_id',
    //     'sku',
    //     'name',
    //     'price',
    //     'stock'
    // ];
    protected $appends = ['averageRating','totalSold'];

    public function getAverageRatingAttribute()
    {
        return $this->avgReview()->avg('rating') ? : '0';
    }

    public function getTotalSoldAttribute()
    {
        return $this->orderProducts()->sum('qty');
    }




    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function seller(){
        return $this->belongsTo(Vendor::class,'vendor_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function gallery(){
        return $this->hasMany(ProductGallery::class);
    }

    public function specifications(){
        return $this->hasMany(ProductSpecification::class);
    }

    public function reviews(){
        return $this->hasMany(ProductReview::class);
    }


    public function variants(){
        return $this->hasMany(ProductVariant::class);
    }

    public function orderProducts(){
        return $this->hasMany(OrderProduct::class);
    }



    public function activeVariants(){
        return $this->hasMany(ProductVariant::class)->select(['id','name','product_id']);
    }



    public function variantItems(){
        return $this->hasMany(ProductVariantItem::class);
    }

    public function avgReview(){
        return $this->hasMany(ProductReview::class)->where('status', 1);
    }

    public function category_name(){
        return $this->belongsTo(Category::class,'category_id','id');
    }


    protected $fillable = [
        'name',
        'short_name',
        'slug',
        'thumb_image',
        'vendor_id',
        'category_id',
        'sub_category_id',
        'child_category_id',
        'brand_id',
        'qty',
        'weight',
        'sold_qty',
        'short_description',
        'long_description',
        'video_link',
        'sku',
        'seo_title',
        'seo_description',
        'price',
        'offer_price',
        'tags',
        'show_homepage',
        'is_undefine',
        'is_featured',
        'new_product',
        'is_top',
        'is_best',
        'status',
        'is_specification',
        'approve_by_admin',
        'reedem_percentage',
        // ZOHO FIELDS (add here)
        'zoho_item_id',
        'sku',
        'stock',
    ];

}
