<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\DTO\Store\StoreDetailsDTO;
use App\Repositories\Store\StoreDetailsRepository;

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
        'shop_code',
        'store_name',
        'owner_name',
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
        'google_map_url',
        'tags',
        'min_bill_amount',
        'commission_percent',
        'discount_percent',
        'rating',
        'no_of_ratings',
        'total_ratings',
        'is_active',
        'is_featured',
        'offer_tag',
        'last_updated_on',
        'store_image',
        'images',
        'gst_number',
        'bank_name',
        'account_number',
        'ifsc_code',
        'beneficiary_name',
        'upi_id',
        'razorpay_account_id',
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
        'no_of_ratings' => 'integer',
        'total_ratings' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'last_updated_on' => 'date',
        'images' => 'array',
        'tags' => 'array',
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
     * Get images for this application
     */
    public function shopImages()
    {
        return $this->hasMany(ShopImage::class, 'seller_application_id');
    }

    /**
     * Get visitors for this application
     */
    public function visitors()
    {
        return $this->hasMany(ShopVisitor::class, 'seller_application_id');
    }

    /**
     * Get transactions for this application
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'seller_application_id');
    }

    /**
     * Generate a unique shop code
     */
    public static function generateShopCode(): string
    {
        do {
            $code = 'SHOP-' . date('Y') . str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('shop_code', $code)->exists());

        return $code;
    }

    /**
     * Get unified store details DTO.
     * Returns Shop data if approved, else Application data.
     * This is the single source of truth for reading store data.
     *
     * @return StoreDetailsDTO
     */
    public function toStoreDetails(): StoreDetailsDTO
    {
        $repository = new StoreDetailsRepository();
        return $repository->getFromApplication($this);
    }

    /**
     * Update this application using normalized data.
     * Accepts any key format (camelCase, snake_case).
     * Use for pending applications only.
     *
     * @param array $data
     * @return bool
     */
    public function updateDetails(array $data): bool
    {
        $repository = new StoreDetailsRepository();
        $normalizedData = $repository->normalizeToApplicationColumns($data);
        return $this->update($normalizedData);
    }

    /**
     * Get field definitions from the single source of truth.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return StoreDetailsRepository::FIELDS;
    }
}

