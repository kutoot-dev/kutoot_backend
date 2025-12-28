<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'zoho_salesorder_id',
        'zoho_invoice_id',
        'zoho_shipment_id',
        'payment_type',
        'payment_status',
        'order_status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function orderProducts(){
        return $this->hasMany(OrderProduct::class);
    }

    public function orderAddress(){
        return $this->hasOne(OrderAddress::class);
    }

    public function deliveryman(){
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id', 'id');
    }
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
    public function address()
{
    return $this->hasOne(OrderAddress::class, 'order_id');
}
public function items()
{
    return $this->hasMany(OrderProduct::class, 'order_id');
}
public function isPaid()
{
    return $this->payment_status === 'success';
}

public function isCOD()
{
    return $this->payment_type === 'COD';
}

public function markConfirmed()
{
    $this->update(['order_status' => 'CONFIRMED']);
}

}
