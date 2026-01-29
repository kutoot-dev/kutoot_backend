<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'favicon',
        'enable_user_register',
        'enable_multivendor',
        'enable_subscription_notify',
        'enable_save_contact_message',
        'text_direction',
        'timezone',
        'sidebar_lg_header',
        'sidebar_sm_header',
        'featured_category_banner',
        'current_version',
        'tax',
        'currency_id',
        'home_section_title',
        'homepage_section_title',
        'popular_category_banner',
        'contact_email',
        'topbar_phone',
        'topbar_email',
        'show_product_progressbar',
        'phone_number_required',
        'default_phone_code',
        'theme_one',
        'theme_two',
        'currency_icon',
        'currency_name',
        'seller_condition',
        'empty_cart',
        'empty_wishlist',
        'change_password_image',
        'become_seller_avatar',
        'become_seller_banner',
        'login_image',
        'error_page',
    ];

    protected $casts = [
        'home_section_title' => 'array',
        'homepage_section_title' => 'array',
    ];

    public function currency()
    {
        return $this->belongsTo(\Nnjeim\World\Models\Currency::class, 'currency_id');
    }
}
