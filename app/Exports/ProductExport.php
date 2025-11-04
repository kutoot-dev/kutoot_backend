<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection , WithHeadings
{
    protected $is_dummy = false;

    public function __construct($is_dummy)
    {
        $this->is_dummy = $is_dummy;
    }


    public function headings(): array
    {
        return
            $this->is_dummy ? [
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
                'short_description',
                'long_description',
                'video_link',
                'sku',
                'seo_title',
                'seo_description',
                'price',
                'offer_price',
                'show_homepage',
                'is_undefine',
                'is_featured',
                'new_product',
                'is_top',
                'is_best',
                'status',
                'is_specification',
                'approve_by_admin',
              
            ] :
            [
                'id',
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
                'short_description',
                'long_description',
                'video_link',
                'sku',
                'seo_title',
                'seo_description',
                'price',
                'offer_price',
                'show_homepage',
                'is_undefine',
                'is_featured',
                'new_product',
                'is_top',
                'is_best',
                'status',
                'is_specification',
                'approve_by_admin',
              
            ]
            ;
    }


   public function collection()
{
    if ($this->is_dummy) {
        $dummyData = Product::where('status', 1)
            ->select([
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
                'short_description',
                'long_description',
                'video_link',
                'sku',
                'seo_title',
                'seo_description',
                'price',
                'offer_price',
                'show_homepage',
                'is_undefine',
                'is_featured',
                'new_product',
                'is_top',
                'is_best',
                'status',
                'is_specification',
                'approve_by_admin',
              
            ])
            ->limit(1)
            ->get();

        // Hide appended attributes if any
        $dummyData->each->makeHidden(['averageRating', 'totalSold']);

        return $dummyData;
    }

    $fullData = Product::select([
        'id',
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
        'short_description',
        'long_description',
        'video_link',
        'sku',
        'seo_title',
        'seo_description',
        'price',
        'offer_price',
        'show_homepage',
        'is_undefine',
        'is_featured',
        'new_product',
        'is_top',
        'is_best',
        'status',
        'is_specification',
        'approve_by_admin',
        
    ])->get();

    // Hide appended attributes if needed
    $fullData->each->makeHidden(['averageRating', 'totalSold']);

    return $fullData;
}

}
