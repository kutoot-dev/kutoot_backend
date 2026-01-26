<?php

namespace App\Models\Store;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\DTO\Store\StoreDetailsDTO;
use App\Repositories\Store\StoreDetailsRepository;
use App\Models\Store\SellerApplication;

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
     * Get unified store details.
     * Returns data from Shop (authoritative) or Application (pending).
     * This is the SINGLE SOURCE OF TRUTH for reading store data.
     *
     * @return StoreDetailsDTO|null
     */
    public function getStoreDetails(): ?StoreDetailsDTO
    {
        $repository = new StoreDetailsRepository();
        return $repository->getForSeller($this);
    }

    /**
     * Update store details.
     * Always updates SellerApplication (single source of truth).
     * Accepts any key format (camelCase, snake_case).
     *
     * @param array $data
     * @return SellerApplication
     * @throws \RuntimeException if application doesn't exist
     */
    public function updateStoreDetails(array $data): SellerApplication
    {
        $repository = new StoreDetailsRepository();
        return $repository->updateForSeller($this, $data);
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


