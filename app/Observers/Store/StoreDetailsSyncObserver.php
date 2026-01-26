<?php

namespace App\Observers\Store;

use App\Models\Store\SellerApplication;
use App\Models\Store\Shop;
use App\Repositories\Store\StoreDetailsRepository;
use Illuminate\Support\Facades\Log;

/**
 * Observer for bidirectional sync between SellerApplication and Shop.
 *
 * When Application is updated → sync to Shop
 * When Shop is updated → sync to Application
 */
class StoreDetailsSyncObserver
{
    protected static bool $syncing = false;
    protected static bool $enabled = true;

    /**
     * Handle the SellerApplication "updated" event.
     * Syncs changed fields to the linked Shop.
     */
    public function applicationUpdated(SellerApplication $application): void
    {
        if (self::$syncing || !self::$enabled) {
            return;
        }

        // Only sync if there's a linked shop (approved applications)
        if (!$application->seller_id) {
            Log::debug('StoreSync: No seller_id on application, skipping sync', [
                'application_id' => $application->id
            ]);
            return;
        }

        $shop = Shop::where('seller_id', $application->seller_id)->first();
        if (!$shop) {
            Log::debug('StoreSync: No shop found for seller, skipping sync', [
                'seller_id' => $application->seller_id
            ]);
            return;
        }

        // Get dirty attributes (changed but not yet reflected in getChanges after save)
        // Use getDirty() before save or getChanges() after save
        $changedFields = $application->getChanges();

        // If getChanges is empty, try to get the original vs current diff
        if (empty($changedFields)) {
            $changedFields = $this->getActualChanges($application);
        }

        if (empty($changedFields)) {
            Log::debug('StoreSync: No changed fields detected', [
                'application_id' => $application->id
            ]);
            return;
        }

        Log::debug('StoreSync: Application changes detected', [
            'application_id' => $application->id,
            'changes' => $changedFields
        ]);

        $shopData = $this->mapApplicationToShop($changedFields);
        if (empty($shopData)) {
            Log::debug('StoreSync: No mappable shop data from changes', [
                'changes' => $changedFields
            ]);
            return;
        }

        Log::info('StoreSync: Syncing application to shop', [
            'application_id' => $application->id,
            'shop_id' => $shop->id,
            'shop_data' => $shopData
        ]);

        self::$syncing = true;
        try {
            $shop->updateQuietly($shopData);
        } finally {
            self::$syncing = false;
        }
    }

    /**
     * Handle the Shop "updated" event.
     * Syncs changed fields to the linked SellerApplication.
     */
    public function shopUpdated(Shop $shop): void
    {
        if (self::$syncing || !self::$enabled) {
            return;
        }

        $application = SellerApplication::where('seller_id', $shop->seller_id)->first();
        if (!$application) {
            return;
        }

        $changedFields = $shop->getChanges();
        if (empty($changedFields)) {
            $changedFields = $this->getActualChanges($shop);
        }

        if (empty($changedFields)) {
            return;
        }

        $appData = $this->mapShopToApplication($changedFields);
        if (empty($appData)) {
            return;
        }

        Log::info('StoreSync: Syncing shop to application', [
            'shop_id' => $shop->id,
            'application_id' => $application->id,
            'app_data' => $appData
        ]);

        self::$syncing = true;
        try {
            $application->updateQuietly($appData);
        } finally {
            self::$syncing = false;
        }
    }

    /**
     * Get actual changes by comparing original with current attributes.
     */
    protected function getActualChanges($model): array
    {
        $changes = [];
        $original = $model->getOriginal();
        $current = $model->getAttributes();

        foreach ($current as $key => $value) {
            if (array_key_exists($key, $original) && $original[$key] !== $value) {
                $changes[$key] = $value;
            }
        }

        return $changes;
    }

    /**
     * Map Application fields to Shop fields.
     */
    protected function mapApplicationToShop(array $appChanges): array
    {
        $shopData = [];
        $fields = StoreDetailsRepository::FIELDS;

        foreach ($fields as $fieldDef) {
            $appColumn = $fieldDef['app_column'] ?? null;
            $shopColumn = $fieldDef['shop_column'] ?? null;

            if (!$appColumn || !$shopColumn) {
                continue;
            }

            if (array_key_exists($appColumn, $appChanges)) {
                $shopData[$shopColumn] = $appChanges[$appColumn];
            }
        }

        // Handle owner_name special case (store_name → owner_name)
        if (array_key_exists('store_name', $appChanges)) {
            $shopData['owner_name'] = $appChanges['store_name'];
        }

        return $shopData;
    }

    /**
     * Map Shop fields to Application fields.
     */
    protected function mapShopToApplication(array $shopChanges): array
    {
        $appData = [];
        $fields = StoreDetailsRepository::FIELDS;

        foreach ($fields as $fieldDef) {
            $appColumn = $fieldDef['app_column'] ?? null;
            $shopColumn = $fieldDef['shop_column'] ?? null;

            if (!$appColumn || !$shopColumn) {
                continue;
            }

            if (array_key_exists($shopColumn, $shopChanges)) {
                $appData[$appColumn] = $shopChanges[$shopColumn];
            }
        }

        return $appData;
    }

    /**
     * Temporarily disable sync (useful for batch operations).
     */
    public static function withoutSync(callable $callback): mixed
    {
        $wasEnabled = self::$enabled;
        self::$enabled = false;
        try {
            return $callback();
        } finally {
            self::$enabled = $wasEnabled;
        }
    }

    /**
     * Enable sync.
     */
    public static function enable(): void
    {
        self::$enabled = true;
    }

    /**
     * Disable sync.
     */
    public static function disable(): void
    {
        self::$enabled = false;
    }

    /**
     * Check if sync is enabled.
     */
    public static function isEnabled(): bool
    {
        return self::$enabled;
    }
}
