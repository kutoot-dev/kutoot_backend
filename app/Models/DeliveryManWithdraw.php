<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryManWithdraw extends Model
{
    use HasFactory;

    public function deliveryman(){
        return $this->belongsTo(DeliveryMan::class,'delivery_man_id');
    }
}
