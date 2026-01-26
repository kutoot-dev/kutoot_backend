<?php

namespace App\Repositories\Store;

use App\DTO\Store\StoreDetailsDTO;
use App\Models\Store\Seller;
use App\Models\Store\SellerApplication;

/**
 * Repository for unified store details access.
 *
 * SINGLE SOURCE OF TRUTH:
 * All store data is stored in and fetched from SellerApplication.
 */
class StoreDetailsRepository
{
    /**
     * Unified field definitions with validation rules.
     * This is the single source of truth for store detail fields.
     */
    public const FIELDS = [
        'name' => [
            'column' => 'store_name',
            'type' => 'string',
            'max' => 255,
            'request_keys' => ['shopName', 'storeName', 'shop_name', 'store_name', 'name'],
        ],
        'category' => [
            'column' => 'store_type',
            'type' => 'string',
            'max' => 100,
            'request_keys' => ['category', 'storeType', 'store_type'],
        ],
        'owner_name' => [
            'column' => 'owner_name',
            'type' => 'string',
            'max' => 255,
            'request_keys' => ['ownerName', 'owner_name'],
        ],
        'phone' => [
            'column' => 'owner_mobile',
            'type' => 'string',
            'max' => 15,
            'request_keys' => ['phone', 'ownerMobile', 'owner_mobile'],
        ],
        'email' => [
            'column' => 'owner_email',
            'type' => 'string',
            'max' => 255,
            'request_keys' => ['email', 'ownerEmail', 'owner_email'],
        ],
        'gst_number' => [
            'column' => 'gst_number',
            'type' => 'string',
            'max' => 20,
            'request_keys' => ['gstNumber', 'gst_number'],
        ],
        'address' => [
            'column' => 'store_address',
            'type' => 'string',
            'max' => 500,
            'request_keys' => ['address', 'storeAddress', 'store_address'],
        ],
        'country_id' => [
            'column' => 'country_id',
            'type' => 'integer',
            'request_keys' => ['countryId', 'country_id'],
        ],
        'state_id' => [
            'column' => 'state_id',
            'type' => 'integer',
            'request_keys' => ['stateId', 'state_id'],
        ],
        'city_id' => [
            'column' => 'city_id',
            'type' => 'integer',
            'request_keys' => ['cityId', 'city_id'],
        ],
        'lat' => [
            'column' => 'lat',
            'type' => 'numeric',
            'request_keys' => ['lat', 'locationLat', 'location_lat'],
        ],
        'lng' => [
            'column' => 'lng',
            'type' => 'numeric',
            'request_keys' => ['lng', 'locationLng', 'location_lng'],
        ],
        'google_map_url' => [
            'column' => 'google_map_url',
            'type' => 'string',
            'max' => 2048,
            'request_keys' => ['googleMapUrl', 'google_map_url'],
        ],
        'min_bill_amount' => [
            'column' => 'min_bill_amount',
            'type' => 'numeric',
            'request_keys' => ['minBillAmount', 'min_bill_amount'],
        ],
        'tags' => [
            'column' => 'tags',
            'type' => 'array',
            'request_keys' => ['tags'],
        ],
        'commission_percent' => [
            'column' => 'commission_percent',
            'type' => 'numeric',
            'request_keys' => ['commissionPercent', 'commission_percent'],
        ],
        'discount_percent' => [
            'column' => 'discount_percent',
            'type' => 'numeric',
            'request_keys' => ['discountPercent', 'discount_percent'],
        ],
        'rating' => [
            'column' => 'rating',
            'type' => 'numeric',
            'request_keys' => ['rating'],
        ],
        'is_active' => [
            'column' => 'is_active',
            'type' => 'boolean',
            'request_keys' => ['isActive', 'is_active'],
        ],
        'is_featured' => [
            'column' => 'is_featured',
            'type' => 'boolean',
            'request_keys' => ['isFeatured', 'is_featured'],
        ],
        'offer_tag' => [
            'column' => 'offer_tag',
            'type' => 'string',
            'max' => 255,
            'request_keys' => ['offerTag', 'offer_tag'],
        ],
    ];

    /**
     * Get store details for a seller.
     * Returns data from SellerApplication (single source of truth).
     */
    public function getForSeller(Seller $seller): ?StoreDetailsDTO
    {
        $application = $seller->application;

        if (!$application) {
            return null;
        }

        return StoreDetailsDTO::fromApplication($application);
    }

    /**
     * Get store details by seller ID.
     */
    public function getBySellerId(int $sellerId): ?StoreDetailsDTO
    {
        $application = SellerApplication::where('seller_id', $sellerId)->latest()->first();

        if (!$application) {
            return null;
        }

        return StoreDetailsDTO::fromApplication($application);
    }

    /**
     * Get store details by application ID.
     */
    public function getByApplicationId(int $applicationId): ?StoreDetailsDTO
    {
        $application = SellerApplication::find($applicationId);
        return $application ? StoreDetailsDTO::fromApplication($application) : null;
    }

    /**
     * Get store details by shop code.
     */
    public function getByShopCode(string $shopCode): ?StoreDetailsDTO
    {
        $application = SellerApplication::where('shop_code', $shopCode)
            ->where('status', SellerApplication::STATUS_APPROVED)
            ->first();
        return $application ? StoreDetailsDTO::fromApplication($application) : null;
    }

    /**
     * Get store details from application.
     */
    public function getFromApplication(SellerApplication $application): StoreDetailsDTO
    {
        return StoreDetailsDTO::fromApplication($application);
    }

    /**
     * Update store details.
     * Updates SellerApplication (the single source of truth).
     *
     * @param SellerApplication $application The application to update
     * @param array $data Request data (accepts any key format)
     * @return SellerApplication
     */
    public function update(SellerApplication $application, array $data): SellerApplication
    {
        $applicationData = $this->normalizeToApplicationColumns($data);
        $application->update($applicationData);
        return $application->fresh();
    }

    /**
     * Update store details by seller.
     */
    public function updateForSeller(Seller $seller, array $data): SellerApplication
    {
        $application = $seller->application;

        if (!$application) {
            throw new \RuntimeException('Cannot update store details: Application not found for seller');
        }

        return $this->update($application, $data);
    }

    /**
     * Normalize request data to Application column names.
     * Accepts any key format (camelCase, snake_case, etc.)
     */
    public function normalizeToApplicationColumns(array $data): array
    {
        $appData = [];

        foreach (self::FIELDS as $field => $config) {
            $column = $config['column'];

            // Check all possible request keys for this field
            foreach ($config['request_keys'] as $key) {
                if (array_key_exists($key, $data)) {
                    $appData[$column] = $data[$key];
                    break;
                }
            }
        }

        return $appData;
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
                case 'boolean':
                    $fieldRules[] = 'boolean';
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
     * Approve an application - generates shop code and updates status.
     */
    public function approveApplication(
        SellerApplication $application,
        int $sellerId,
        int $approvedBy,
        ?string $sellerEmail = null
    ): SellerApplication {
        $application->update([
            'seller_id' => $sellerId,
            'shop_code' => SellerApplication::generateShopCode(),
            'status' => SellerApplication::STATUS_APPROVED,
            'approved_by' => $approvedBy,
            'seller_email' => $sellerEmail ?? $application->owner_email,
            'approved_at' => now(),
            'is_active' => true,
        ]);

        return $application->fresh();
    }

    /**
     * Get all approved applications (active stores).
     */
    public function getApprovedApplications()
    {
        return SellerApplication::where('status', SellerApplication::STATUS_APPROVED)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Query approved applications.
     */
    public function queryApproved()
    {
        return SellerApplication::where('status', SellerApplication::STATUS_APPROVED);
    }
}
