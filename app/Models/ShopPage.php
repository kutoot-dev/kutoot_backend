<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'header_one',
        'header_two',
        'title_one',
        'title_two',
        'banner',
        'link',
        'button_text',
        'filter_price_range',
    ];
}
