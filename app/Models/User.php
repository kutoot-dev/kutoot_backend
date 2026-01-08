<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'zoho_customer_id', // ✅ REQUIRED
        'email',
        'password',
        'provider',
        'provider_id',
        'provider_avatar',
        'status',
        'email_verified',
        'is_completed',
        'phone',
        'gender',
        'street',
        'house_no',
        'image',
        'city_id',
        'state_id',
        'country_id',
        'address',
        'zip_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function seller(){
        return $this->hasOne(Vendor::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function state(){
        return $this->belongsTo(CountryState::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


}
