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

    /**
     * Get the seller's store application.
     * This is the single source of truth for store data.
     *
     * @return SellerApplication|null
     */
    public function getStoreApplication(): ?SellerApplication
    {
        return $this->application;
    }

    /**
     * Update store details.
     * Accepts any key format (camelCase, snake_case).
     *
     * @param array $data
     * @return bool
     * @throws \RuntimeException if application doesn't exist
     */
    public function updateStoreDetails(array $data): bool
    {
        $application = $this->application;
        if (!$application) {
            throw new \RuntimeException('Cannot update store details: Application not found for seller');
        }
        return $application->updateNormalized($data);
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


