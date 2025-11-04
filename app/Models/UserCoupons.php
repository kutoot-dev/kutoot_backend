<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoupons extends Model
{
    use HasFactory;

    protected $table = 'table_purchaselinkedcoupons';


    protected $fillable = [
        'purchased_camp_id',
        'coupon_code',
        'coupon_expires',
        'coins',
        'is_claimed',
        'main_campaign_id',
        'series_label',
        'status',
        'created_at',
        'updated_at',
    ];


    public function getImgAttribute($value)
    {
        return $value ? asset($value) : null;
    }

    public function purchasedCampaign()
        {
            return $this->belongsTo(PurchasedCoins::class, 'purchased_camp_id');
        }
}
