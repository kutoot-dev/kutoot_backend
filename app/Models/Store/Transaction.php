<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'shop_id',
        'visitor_id',
        'txn_code',
        'total_amount',
        'discount_amount',
        'commission_amount',
        'shop_amount',
        'razorpay_payment_id',
        'razorpay_order_id',
        'split_details',
        'redeemed_coins',
        'status',
        'settled_at',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'discount_amount' => 'float',
        'commission_amount' => 'float',
        'shop_amount' => 'float',
        'split_details' => 'array',
        'redeemed_coins' => 'int',
        'settled_at' => 'date',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function visitor()
    {
        return $this->belongsTo(ShopVisitor::class, 'visitor_id');
    }
}


