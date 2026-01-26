<?php

namespace App\Models\Store;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Seller extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'store_sellers';

    protected $fillable = [
        'seller_code',
        'username',
        'password',
        'owner_name',
        'email',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function shop()
    {
        return $this->hasOne(Shop::class, 'seller_id');
    }

    public function bankAccount()
    {
        return $this->hasOne(SellerBankAccount::class, 'seller_id');
    }

    public function notificationSettings()
    {
        return $this->hasOne(SellerNotificationSetting::class, 'seller_id');
    }

    public function application()
    {
        return $this->hasOne(SellerApplication::class, 'seller_id');
    }

    /**
     * Check if seller has an approved store application
     */
    public function hasApprovedApplication(): bool
    {
        return $this->application()
            ->where('status', SellerApplication::STATUS_APPROVED)
            ->exists();
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


