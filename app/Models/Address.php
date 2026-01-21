<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'type',
        'pincode',
        'default_billing',
        'default_shipping',
    ];

    public function country(){
        return $this->belongsTo(\Nnjeim\World\Models\Country::class)->select('id','name');
    }

    public function countryState(){
        return $this->belongsTo(\Nnjeim\World\Models\State::class,'state_id')->select('id','name');
    }

    public function city(){
        return $this->belongsTo(\Nnjeim\World\Models\City::class)->select('id','name');
    }
}
