<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'name' => $row[0],
            'short_name' => $row[1],
            'slug' => $row[2],
            'thumb_image' => $row[3],
            'vendor_id' => $row[4],
            'category_id' => $row[5],
            'sub_category_id' => $row[6],
            'child_category_id' => $row[7],
            'brand_id' => $row[8],
            'qty' => $row[9],
            'weight' => $row[10],
            'short_description' => $row[11],
            'long_description' => $row[12],
            'video_link' => $row[13],
            'sku' => $row[14],
            'seo_title' => $row[15],
            'seo_description' => $row[16],
            'price' => $row[17],
            'offer_price' => $row[18],
            'show_homepage' => $row[19],
            'is_undefine' => $row[20],
            'is_featured' => $row[21],
            'new_product' => $row[22],
            'is_top' => $row[23],
            'is_best' => $row[24],
            'status' => $row[25],
            'is_specification' => $row[26],
            'approve_by_admin' => $row[27]
        ]);
    }
}
