<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function countryState(){
        return $this->belongsTo(CountryState::class);
    }

    public function addressCities(){
        return $this->hasMany(Address::class);
    }

    protected $fillable = [
        'country_state_id',
        'name',
        'slug',
        'status'
    ];
}
