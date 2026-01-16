<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class SellerNotificationSetting extends Model
{
    protected $table = 'seller_notification_settings';

    protected $fillable = [
        'seller_id',
        'enabled',
        'email',
        'sms',
        'whatsapp',
    ];

    protected $casts = [
        'enabled' => 'bool',
        'email' => 'bool',
        'sms' => 'bool',
        'whatsapp' => 'bool',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
}


