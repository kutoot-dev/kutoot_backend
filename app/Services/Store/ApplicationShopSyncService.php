<?php

namespace App\Services\Store;

use App\Models\Store\SellerApplication;
use App\Models\Store\Shop;

/**
 * Single source of truth for SellerApplication ↔ Shop field mapping and synchronization.
 *
 * This service handles:
 * - Field mapping between SellerApplication and Shop tables
 * - Creating Shop from SellerApplication data
 * - Syncing updates between the two tables
 */
class ApplicationShopSyncService
{
    /**
     * Field mapping: SellerApplication field => Shop field
     *
     * This is the single source of truth for field correspondence.
     */
    public const FIELD_MAPPING = [
        'store_name' => 'shop_name',
        'owner_mobile' => 'phone',
        'owner_email' => 'email',
        'store_type' => 'category',
        'store_address' => 'address',
        'country_id' => 'country_id',
        'state_id' => 'state_id',
        'city_id' => 'city_id',
        'lat' => 'location_lat',
        'lng' => 'location_lng',
        'min_bill_amount' => 'min_bill_amount',
        'gst_number' => 'gst_number',
    ];

    /**
     * Reverse mapping: Shop field => SellerApplication field
     */
    public static function getReverseMapping(): array
    {
        return array_flip(self::FIELD_MAPPING);
    }

    /**
     * Get Shop field name from SellerApplication field name
     */
    public static function getShopField(string $applicationField): ?string
    {
        return self::FIELD_MAPPING[$applicationField] ?? null;
    }

    /**
     * Get SellerApplication field name from Shop field name
     */
    public static function getApplicationField(string $shopField): ?string
    {
        return self::getReverseMapping()[$shopField] ?? null;
    }

    /**
     * Create Shop data array from SellerApplication
     *
     * @param SellerApplication $application
     * @param array $additionalData Additional data to merge (e.g., seller_id, shop_code, email override)
     * @return array
     */
    public static function applicationToShopData(SellerApplication $application, array $additionalData = []): array
    {
        $shopData = [];

        foreach (self::FIELD_MAPPING as $appField => $shopField) {
            $value = $application->{$appField};
            if ($value !== null) {
                $shopData[$shopField] = $value;
            }
        }

        // Store name is also used as owner_name by default
        if ($application->store_name) {
            $shopData['owner_name'] = $application->store_name;
        }

        return array_merge($shopData, $additionalData);
    }

    /**
     * Create SellerApplication data array from Shop
     *
     * @param Shop $shop
     * @param array $additionalData Additional data to merge
     * @return array
     */
    public static function shopToApplicationData(Shop $shop, array $additionalData = []): array
    {
        $appData = [];
        $reverseMapping = self::getReverseMapping();

        foreach ($reverseMapping as $shopField => $appField) {
            $value = $shop->{$shopField};
            if ($value !== null) {
                $appData[$appField] = $value;
            }
        }

        return array_merge($appData, $additionalData);
    }

    /**
     * Create a Shop from a SellerApplication
     *
     * @param SellerApplication $application
     * @param int $sellerId
     * @param string|null $email Override email (e.g., seller login email)
     * @return Shop
     */
    public static function createShopFromApplication(
        SellerApplication $application,
        int $sellerId,
        ?string $email = null
    ): Shop {
        $shopCode = self::generateShopCode($sellerId);

        $shopData = self::applicationToShopData($application, [
            'seller_id' => $sellerId,
            'shop_code' => $shopCode,
        ]);

        // Override email if provided (seller login email)
        if ($email !== null) {
            $shopData['email'] = $email;
        }

        return Shop::create($shopData);
    }

    /**
     * Sync Shop data from its linked SellerApplication
     *
     * @param Shop $shop
     * @param array|null $onlyFields Only sync these Shop fields (null = all mapped fields)
     * @return bool
     */
    public static function syncShopFromApplication(Shop $shop, ?array $onlyFields = null): bool
    {
        $application = SellerApplication::where('seller_id', $shop->seller_id)
            ->latest()
            ->first();

        if (!$application) {
            return false;
        }

        $shopData = self::applicationToShopData($application);

        if ($onlyFields !== null) {
            $shopData = array_intersect_key($shopData, array_flip($onlyFields));
        }

        return $shop->update($shopData);
    }

    /**
     * Sync SellerApplication data from Shop (reverse sync)
     *
     * @param SellerApplication $application
     * @param Shop $shop
     * @param array|null $onlyFields Only sync these Application fields (null = all mapped fields)
     * @return bool
     */
    public static function syncApplicationFromShop(
        SellerApplication $application,
        Shop $shop,
        ?array $onlyFields = null
    ): bool {
        $appData = self::shopToApplicationData($shop);

        if ($onlyFields !== null) {
            $appData = array_intersect_key($appData, array_flip($onlyFields));
        }

        return $application->update($appData);
    }

    /**
     * Generate a unique shop code
     */
    public static function generateShopCode(int $sellerId): string
    {
        return 'SHOP-' . date('Y') . str_pad($sellerId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Convert camelCase request field to the appropriate database field
     * for both Application and Shop tables
     *
     * @param string $camelCaseField
     * @return array ['application' => field_name, 'shop' => field_name]
     */
    public static function mapRequestFieldToBoth(string $camelCaseField): array
    {
        $requestToApplicationMap = [
            'storeName' => 'store_name',
            'ownerMobile' => 'owner_mobile',
            'ownerEmail' => 'owner_email',
            'storeType' => 'store_type',
            'storeAddress' => 'store_address',
            'state' => 'state',
            'stateId' => 'state_id',
            'city' => 'city',
            'cityId' => 'city_id',
            'country' => 'country',
            'countryId' => 'country_id',
            'lat' => 'lat',
            'lng' => 'lng',
            'minBillAmount' => 'min_bill_amount',
            'gstNumber' => 'gst_number',
            'shopName' => 'store_name',
            'phone' => 'owner_mobile',
            'email' => 'owner_email',
            'category' => 'store_type',
            'address' => 'store_address',
            'googleMapUrl' => 'google_map_url',
            'locationLat' => 'lat',
            'locationLng' => 'lng',
        ];

        $appField = $requestToApplicationMap[$camelCaseField] ?? null;
        $shopField = $appField ? (self::FIELD_MAPPING[$appField] ?? null) : null;

        // Handle shop-specific fields
        $requestToShopMap = [
            'shopName' => 'shop_name',
            'ownerName' => 'owner_name',
            'phone' => 'phone',
            'email' => 'email',
            'category' => 'category',
            'address' => 'address',
            'googleMapUrl' => 'google_map_url',
            'locationLat' => 'location_lat',
            'locationLng' => 'location_lng',
        ];

        if (!$shopField && isset($requestToShopMap[$camelCaseField])) {
            $shopField = $requestToShopMap[$camelCaseField];
        }

        return [
            'application' => $appField,
            'shop' => $shopField,
        ];
    }

    /**
     * Build update data for both tables from a request
     *
     * @param array $requestData Key-value pairs from request
     * @return array ['application' => [...], 'shop' => [...]]
     */
    public static function buildUpdateDataFromRequest(array $requestData): array
    {
        $applicationData = [];
        $shopData = [];

        $appFieldMap = [
            'storeName' => 'store_name',
            'ownerMobile' => 'owner_mobile',
            'ownerEmail' => 'owner_email',
            'storeType' => 'store_type',
            'storeAddress' => 'store_address',
            'state' => 'state',
            'stateId' => 'state_id',
            'city' => 'city',
            'cityId' => 'city_id',
            'country' => 'country',
            'countryId' => 'country_id',
            'lat' => 'lat',
            'lng' => 'lng',
            'minBillAmount' => 'min_bill_amount',
            'gstNumber' => 'gst_number',
            'bankName' => 'bank_name',
            'accountNumber' => 'account_number',
            'ifscCode' => 'ifsc_code',
            'beneficiaryName' => 'beneficiary_name',
            'upiId' => 'upi_id',
        ];

        foreach ($requestData as $key => $value) {
            if ($value === null) {
                continue;
            }

            // Map to application field
            if (isset($appFieldMap[$key])) {
                $applicationData[$appFieldMap[$key]] = $value;
            }

            // Map to shop field using the single source of truth
            $appFieldName = $appFieldMap[$key] ?? null;
            if ($appFieldName && isset(self::FIELD_MAPPING[$appFieldName])) {
                $shopData[self::FIELD_MAPPING[$appFieldName]] = $value;
            }
        }

        return [
            'application' => $applicationData,
            'shop' => $shopData,
        ];
    }
}
