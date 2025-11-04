<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMessage extends Model
{
    use HasFactory;

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function deliveryman(){
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id', 'id');
    }
}
