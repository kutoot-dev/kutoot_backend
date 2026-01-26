<?php

namespace App\Repositories\Store;

use App\DTO\Store\StoreDetailsDTO;
use App\Models\Store\Seller;
use App\Models\Store\SellerApplication;
use App\Models\Store\Shop;
use Illuminate\Support\Facades\DB;

/**
 * Repository for unified store details access.
 *
 * SINGLE SOURCE OF TRUTH:
 * - READ: Shop table is authoritative for approved sellers. Falls back to Application for pending.
 * - WRITE: Always updates Shop table (the live operational entity).
 *
 * This ensures data is stored and fetched from one place only.
 */
class StoreDetailsRepository
{
    /**
     * Unified field definitions with validation rules.
     * This is the single source of truth for store detail fields.
     */
    public const FIELDS = [
        'name' => [
            'shop_column' => 'shop_name',
            'app_column' => 'store_name',
            'type' => 'string',
            'max' => 255,
            'request_keys' => ['shopName', 'storeName', 'shop_name', 'store_name', 'name'],
        ],
        'category' => [
            'shop_column' => 'category',
            'app_column' => 'store_type',
            'type' => 'string',
            'max' => 100,
            'request_keys' => ['category', 'storeType', 'store_type'],
        ],
        'owner_name' => [
            'shop_column' => 'owner_name',
            'app_column' => 'store_name', // Application doesn't have separate owner_name
            'type' => 'string',
            'max' => 255,
            'request_keys' => ['ownerName', 'owner_name'],
        ],
        'phone' => [
            'shop_column' => 'phone',
            'app_column' => 'owner_mobile',
            'type' => 'string',
            'max' => 15,
            'request_keys' => ['phone', 'ownerMobile', 'owner_mobile'],
        ],
        'email' => [
            'shop_column' => 'email',
            'app_column' => 'owner_email',
            'type' => 'string',
            'max' => 255,
            'request_keys' => ['email', 'ownerEmail', 'owner_email'],
        ],
        'gst_number' => [
            'shop_column' => 'gst_number',
            'app_column' => 'gst_number',
            'type' => 'string',
            'max' => 20,
            'request_keys' => ['gstNumber', 'gst_number'],
        ],
        'address' => [
            'shop_column' => 'address',
            'app_column' => 'store_address',
            'type' => 'string',
            'max' => 500,
            'request_keys' => ['address', 'storeAddress', 'store_address'],
        ],
        'country_id' => [
            'shop_column' => 'country_id',
            'app_column' => 'country_id',
            'type' => 'integer',
            'request_keys' => ['countryId', 'country_id'],
        ],
        'state_id' => [
            'shop_column' => 'state_id',
            'app_column' => 'state_id',
            'type' => 'integer',
            'request_keys' => ['stateId', 'state_id'],
        ],
        'city_id' => [
            'shop_column' => 'city_id',
            'app_column' => 'city_id',
            'type' => 'integer',
            'request_keys' => ['cityId', 'city_id'],
        ],
        'lat' => [
            'shop_column' => 'location_lat',
            'app_column' => 'lat',
            'type' => 'numeric',
            'request_keys' => ['lat', 'locationLat', 'location_lat'],
        ],
        'lng' => [
            'shop_column' => 'location_lng',
            'app_column' => 'lng',
            'type' => 'numeric',
            'request_keys' => ['lng', 'locationLng', 'location_lng'],
        ],
        'google_map_url' => [
            'shop_column' => 'google_map_url',
            'app_column' => null, // Application doesn't have this
            'type' => 'string',
            'max' => 2048,
            'request_keys' => ['googleMapUrl', 'google_map_url'],
        ],
        'min_bill_amount' => [
            'shop_column' => 'min_bill_amount',
            'app_column' => 'min_bill_amount',
            'type' => 'numeric',
            'request_keys' => ['minBillAmount', 'min_bill_amount'],
        ],
        'tags' => [
            'shop_column' => 'tags',
            'app_column' => null, // Application doesn't have this
            'type' => 'array',
            'request_keys' => ['tags'],
        ],
    ];

    /**
     * Get store details for a seller.
     * Returns data from Shop (authoritative) or Application (pending).
     */
    public function getForSeller(Seller $seller): ?StoreDetailsDTO
    {
        $shop = $seller->shop;
        $application = $seller->application;

        return StoreDetailsDTO::fromSellerOrApplication($shop, $application);
    }

    /**
     * Get store details by seller ID.
     */
    public function getBySellerId(int $sellerId): ?StoreDetailsDTO
    {
        $shop = Shop::where('seller_id', $sellerId)->first();
        if ($shop) {
            return StoreDetailsDTO::fromShop($shop);
        }

        $application = SellerApplication::where('seller_id', $sellerId)->latest()->first();
        if ($application) {
            return StoreDetailsDTO::fromApplication($application);
        }

        return null;
    }

    /**
     * Get store details by shop ID.
     */
    public function getByShopId(int $shopId): ?StoreDetailsDTO
    {
        $shop = Shop::find($shopId);
        return $shop ? StoreDetailsDTO::fromShop($shop) : null;
    }

    /**
     * Get store details from application (for pending applications).
     */
    public function getFromApplication(SellerApplication $application): StoreDetailsDTO
    {
        // If application is approved and has a shop, return shop data
        if ($application->seller_id) {
            $shop = Shop::where('seller_id', $application->seller_id)->first();
            if ($shop) {
                return StoreDetailsDTO::fromShop($shop);
            }
        }

        return StoreDetailsDTO::fromApplication($application);
    }

    /**
     * Update store details.
     * ALWAYS updates Shop table (the authoritative source for live data).
     *
     * @param Shop $shop The shop to update
     * @param array $data Request data (accepts any key format)
     * @return Shop
     */
    public function update(Shop $shop, array $data): Shop
    {
        $shopData = $this->normalizeToShopColumns($data);
        $shop->update($shopData);
        return $shop->fresh();
    }

    /**
     * Update store details by seller.
     * Creates shop if it doesn't exist (shouldn't happen for approved sellers).
     */
    public function updateForSeller(Seller $seller, array $data): Shop
    {
        $shop = $seller->shop;

        if (!$shop) {
            throw new \RuntimeException('Cannot update store details: Shop not found for seller');
        }

        return $this->update($shop, $data);
    }

    /**
     * Create shop from application data.
     * Used when approving an application.
     */
    public function createFromApplication(
        SellerApplication $application,
        int $sellerId,
        ?string $overrideEmail = null
    ): Shop {
        $shopData = $this->applicationToShopData($application);
        $shopData['seller_id'] = $sellerId;
        $shopData['shop_code'] = $this->generateShopCode($sellerId);

        if ($overrideEmail !== null) {
            $shopData['email'] = $overrideEmail;
        }

        return Shop::create($shopData);
    }

    /**
     * Normalize request data to Shop column names.
     * Accepts any key format (camelCase, snake_case, etc.)
     */
    public function normalizeToShopColumns(array $data): array
    {
        $shopData = [];

        foreach (self::FIELDS as $field => $config) {
            $shopColumn = $config['shop_column'];
            if (!$shopColumn) {
                continue;
            }

            // Check all possible request keys for this field
            foreach ($config['request_keys'] as $key) {
                if (array_key_exists($key, $data)) {
                    $shopData[$shopColumn] = $data[$key];
                    break;
                }
            }
        }

        return $shopData;
    }

    /**
     * Normalize request data to Application column names.
     */
    public function normalizeToApplicationColumns(array $data): array
    {
        $appData = [];

        foreach (self::FIELDS as $field => $config) {
            $appColumn = $config['app_column'];
            if (!$appColumn) {
                continue;
            }

            foreach ($config['request_keys'] as $key) {
                if (array_key_exists($key, $data)) {
                    $appData[$appColumn] = $data[$key];
                    break;
                }
            }
        }

        return $appData;
    }

    /**
     * Convert Application model to Shop data array.
     */
    public function applicationToShopData(SellerApplication $application): array
    {
        $shopData = [];

        foreach (self::FIELDS as $field => $config) {
            $shopColumn = $config['shop_column'];
            $appColumn = $config['app_column'];

            if (!$shopColumn || !$appColumn) {
                continue;
            }

            $value = $application->{$appColumn};
            if ($value !== null) {
                $shopData[$shopColumn] = $value;
            }
        }

        // Set owner_name from store_name
        if (isset($shopData['shop_name'])) {
            $shopData['owner_name'] = $shopData['shop_name'];
        }

        return $shopData;
    }

    /**
     * Get validation rules for store details update.
     * Uses unified field definitions.
     */
    public function getValidationRules(bool $required = false): array
    {
        $rules = [];
        $rule = $required ? 'required' : 'sometimes';

        foreach (self::FIELDS as $field => $config) {
            $fieldRules = [$rule];

            switch ($config['type']) {
                case 'string':
                    $fieldRules[] = 'string';
                    if (isset($config['max'])) {
                        $fieldRules[] = 'max:' . $config['max'];
                    }
                    break;
                case 'integer':
                    $fieldRules[] = 'integer';
                    break;
                case 'numeric':
                    $fieldRules[] = 'numeric';
                    break;
                case 'array':
                    $fieldRules[] = 'array';
                    break;
            }

            // Add rules for all possible request keys
            foreach ($config['request_keys'] as $key) {
                $rules[$key] = $fieldRules;
            }
        }

        return $rules;
    }

    /**
     * Generate unique shop code.
     */
    public function generateShopCode(int $sellerId): string
    {
        return 'SHOP-' . date('Y') . str_pad($sellerId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update application with pending changes (before approval).
     */
    public function updateApplication(SellerApplication $application, array $data): SellerApplication
    {
        $appData = $this->normalizeToApplicationColumns($data);
        $application->update($appData);
        return $application->fresh();
    }
}
