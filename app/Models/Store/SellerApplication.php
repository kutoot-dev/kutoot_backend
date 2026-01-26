<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Services\Store\ApplicationShopSyncService;

class SellerApplication extends Model
{
    // Status constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_VERIFIED = 'VERIFIED';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    protected $table = 'seller_applications';

    protected $fillable = [
        'application_id',
        'store_name',
        'owner_mobile',
        'owner_email',
        'store_type',
        'store_address',
        'state',
        'city',
        'country',
        'country_id',
        'state_id',
        'city_id',
        'lat',
        'lng',
        'min_bill_amount',
        'commission_percent',
        'discount_percent',
        'rating',
        'store_image',
        'images',
        'gst_number',
        'bank_name',
        'account_number',
        'ifsc_code',
        'beneficiary_name',
        'upi_id',
        'status',
        'verified_by',
        'verification_notes',
        'verified_at',
        'approved_by',
        'seller_email',
        'approved_at',
        'rejected_by',
        'rejection_reason',
        'rejected_at',
        'seller_id',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'min_bill_amount' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'rating' => 'decimal:2',
        'images' => 'array',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Generate a unique application ID
     */
    public static function generateApplicationId(): string
    {
        do {
            $id = 'KT-' . str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('application_id', $id)->exists());

        return $id;
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if application is verified
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    /**
     * Check if application is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Relationship to verifier admin
     */
    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    /**
     * Relationship to approver admin
     */
    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    /**
     * Relationship to rejecter admin
     */
    public function rejecter()
    {
        return $this->belongsTo(Admin::class, 'rejected_by');
    }

    /**
     * Relationship to created seller
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', strtoupper($status));
        }
        return $query;
    }

    /**
     * Get the shop created from this application
     */
    public function shop()
    {
        return $this->hasOne(Shop::class, 'seller_id', 'seller_id');
    }

    /**
     * Create a Shop from this application
     * Uses ApplicationShopSyncService as single source of truth for field mapping.
     *
     * @param int $sellerId
     * @param string|null $email Override email
     * @return Shop
     */
    public function createShop(int $sellerId, ?string $email = null): Shop
    {
        return ApplicationShopSyncService::createShopFromApplication($this, $sellerId, $email);
    }

    /**
     * Get data array for creating a Shop from this application
     *
     * @param array $additionalData Additional data to merge
     * @return array
     */
    public function toShopData(array $additionalData = []): array
    {
        return ApplicationShopSyncService::applicationToShopData($this, $additionalData);
    }

    /**
     * Sync this application's data from the linked Shop
     *
     * @param array|null $onlyFields Only sync these fields (null = all)
     * @return bool
     */
    public function syncFromShop(?array $onlyFields = null): bool
    {
        $shop = $this->shop;
        if (!$shop) {
            return false;
        }
        return ApplicationShopSyncService::syncApplicationFromShop($this, $shop, $onlyFields);
    }

    /**
     * Get the field mapping from SellerApplication to Shop
     *
     * @return array
     */
    public static function getShopFieldMapping(): array
    {
        return ApplicationShopSyncService::FIELD_MAPPING;
    }
}

