<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winners extends Model
{
    use HasFactory;

    protected $table = 'table_prizewinner';
   
    protected $fillable = [
        'camp_id',
        'purchased_camp_id',
        'coupon_id',
        'coupon_number',
        'announcing_date',
        'user_id',
        'is_claimed',
        'prize_details',
        'prize_id',
        'status',
    ];




    public function getImgAttribute($value)
    {
        return $value ? asset($value) : null;
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function userdetails()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','image','created_at');
    }

    public function campaign()
    {
        return $this->belongsTo(PurchasedCoins::class, 'purchased_camp_id');
    }



    public function coupon()
    {
        return $this->belongsTo(UserCoupons::class, 'coupon_id');
    }
}

