<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    public function billingCountry(){
        return $this->belongsTo(\Nnjeim\World\Models\Country::class, 'billing_country_id');
    }

    public function billingState(){
        return $this->belongsTo(\Nnjeim\World\Models\State::class, 'billing_state_id');
    }

    public function billingCity(){
        return $this->belongsTo(\Nnjeim\World\Models\City::class, 'billing_city_id');
    }

    public function shippingCountry(){
        return $this->belongsTo(\Nnjeim\World\Models\Country::class, 'shipping_country_id');
    }

    public function shippingState(){
        return $this->belongsTo(\Nnjeim\World\Models\State::class, 'shipping_state_id');
    }

    public function shippingCity(){
        return $this->belongsTo(\Nnjeim\World\Models\City::class, 'shipping_city_id');
    }
}
