<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'shipment_id',
        'awb_code',
        'courier_name',
        'shipping_status',
        'tracking_url',
        'response',
    ];

    protected $casts = [
        'response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
