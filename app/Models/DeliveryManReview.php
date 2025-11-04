<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryManReview extends Model
{
    use HasFactory;

    public function deliveryman(){
        return $this->belongsTo(DeliveryMan::class,'delivery_man_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
}
