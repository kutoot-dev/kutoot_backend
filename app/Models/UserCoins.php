<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoins extends Model
{
    use HasFactory;

    protected $table = 'table_usercoins';
   
    protected $fillable = [
        'purchased_camp_id',
        'user_id',
        'order_id',
        'coins',
        'coin_expires',
        'type',
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

    public function orderdetails()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
