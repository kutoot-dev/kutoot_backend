<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    public function city(){
        return $this->belongsTo(City::class);
    }

    protected $fillable = [
        'city_id',
        'shipping_rule',
        'type',
        'shipping_fee',
        'condition_from',
        'condition_to'
    ];
}
