<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public function country(){
        return $this->belongsTo(Country::class)->select('id','name');
    }

    public function countryState(){
        return $this->belongsTo(CountryState::class,'state_id')->select('id','name');
    }

    public function city(){
        return $this->belongsTo(City::class)->select('id','name');
    }
}
