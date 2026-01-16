<?php

namespace App\Models\Store;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ShopVisitor extends Model
{
    protected $table = 'shop_visitors';

    protected $appends = ['masked_phone'];

    protected $fillable = [
        'shop_id',
        'user_id',
        'visited_on',
        'redeemed',
    ];

    protected $casts = [
        'visited_on' => 'date',
        'redeemed' => 'bool',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'visitor_id');
    }

    public function getMaskedPhoneAttribute()
    {
        $phone = (string) ($this->user?->phone ?? '');
        if ($phone === '') {
            return null;
        }

        $len = strlen($phone);
        if ($len <= 3) {
            return $phone;
        }

        return substr($phone, 0, 3) . str_repeat('X', $len - 3);
    }
}


