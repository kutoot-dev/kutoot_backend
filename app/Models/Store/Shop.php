<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\DTO\Store\StoreDetailsDTO;
use App\Repositories\Store\StoreDetailsRepository;

class Shop extends Model
{
    protected $table = 'shops';

    protected $fillable = [
        'seller_id',
        'shop_code',
        'shop_name',
        'category',
        'owner_name',
        'phone',
        'email',
        'gst_number',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'tags',
        'google_map_url',
        'location_lat',
        'location_lng',
        'min_bill_amount',
        'razorpay_account_id',
    ];

    protected $casts = [
        'location_lat' => 'float',
        'location_lng' => 'float',
        'min_bill_amount' => 'decimal:2',
        'tags' => 'array',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function country()
    {
        return $this->belongsTo(\Nnjeim\World\Models\Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(\Nnjeim\World\Models\State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(\Nnjeim\World\Models\City::class, 'city_id');
    }

    public function images()
    {
        return $this->hasMany(ShopImage::class, 'shop_id');
    }

    public function visitors()
    {
        return $this->hasMany(ShopVisitor::class, 'shop_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'shop_id');
    }

    public function adminSettings()
    {
        return $this->hasMany(AdminShopCommissionDiscount::class, 'shop_id');
    }

    /**
     * Get the active admin settings for this shop
     */
    public function activeAdminSettings()
    {
        return $this->hasOne(AdminShopCommissionDiscount::class, 'shop_id')
            ->where('is_active', true)
            ->latest();
    }

    /**
     * Get the linked seller application
     */
    public function application()
    {
        return $this->hasOne(SellerApplication::class, 'seller_id', 'seller_id');
    }

    /**
     * Create a Shop from a SellerApplication.
     * Uses StoreDetailsRepository as single source of truth.
     *
     * @param SellerApplication $application
     * @param int $sellerId
     * @param string|null $email Override email
     * @return static
     */
    public static function createFromApplication(
        SellerApplication $application,
        int $sellerId,
        ?string $email = null
    ): static {
        $repository = new StoreDetailsRepository();
        return $repository->createFromApplication($application, $sellerId, $email);
    }

    /**
     * Get unified store details DTO.
     * This is the single source of truth for reading store data.
     *
     * @return StoreDetailsDTO
     */
    public function toStoreDetails(): StoreDetailsDTO
    {
        return StoreDetailsDTO::fromShop($this);
    }

    /**
     * Update store details using normalized data.
     * Accepts any key format (camelCase, snake_case).
     *
     * @param array $data
     * @return bool
     */
    public function updateDetails(array $data): bool
    {
        $repository = new StoreDetailsRepository();
        $normalizedData = $repository->normalizeToShopColumns($data);
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


