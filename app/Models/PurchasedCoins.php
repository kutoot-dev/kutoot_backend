<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedCoins extends Model
{
    use HasFactory;

    protected $table = 'table_purchasecoins';
   
     protected $fillable = [
        'camp_id',
        'user_id',
        'camp_title',
        'camp_description',
        'camp_ticket_price',
        'camp_coins_per_campaign',
        'camp_coupons_per_campaign',
        'status',
        'is_cart',
        'razorpay_order_id',      // ✅ renamed
        'payment_id',             // ✅ ensure it's string-compatible
        'payment_status',
        'razorpay_signature',     // ✅ new column
        'base_plan_id',
        'razor_key',
        'quantity',
        'created_at',
        'updated_at',
    ];


    public function coupons()
    {
        return $this->hasMany(UserCoupons::class, 'purchased_camp_id');
    }

    public function campaign()
    {
        return $this->belongsTo(CoinCampaigns::class, 'camp_id');
    }

    public function basedetails()
    {
        return $this->belongsTo(Baseplans::class, 'base_plan_id');
    }

    public function getImgAttribute($value)
    {
        return $value ? asset($value) : null;
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
