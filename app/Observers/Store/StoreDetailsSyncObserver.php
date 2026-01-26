<?php

namespace App\Observers\Store;

use App\Models\Store\SellerApplication;
use App\Models\Store\Shop;
use App\Repositories\Store\StoreDetailsRepository;

/**
 * Observer for bidirectional sync between SellerApplication and Shop.
 *
 * When Application is updated → sync to Shop
 * When Shop is updated → sync to Application
 */
class StoreDetailsSyncObserver
{
    protected static bool $syncing = false;

    /**
     * Handle the SellerApplication "updated" event.
     * Syncs changed fields to the linked Shop.
     */
    public function applicationUpdated(SellerApplication $application): void
    {
        if (self::$syncing) {
            return;
        }

        // Only sync if there's a linked shop (approved applications)
        if (!$application->seller_id) {
            return;
        }

        $shop = Shop::where('seller_id', $application->seller_id)->first();
        if (!$shop) {
            return;
        }

        $changedFields = $application->getChanges();
        if (empty($changedFields)) {
            return;
        }

        $shopData = $this->mapApplicationToShop($changedFields);
        if (empty($shopData)) {
            return;
        }

        self::$syncing = true;
        try {
            $shop->update($shopData);
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
        if (self::$syncing) {
            return;
        }

        $application = SellerApplication::where('seller_id', $shop->seller_id)->first();
        if (!$application) {
            return;
        }

        $changedFields = $shop->getChanges();
        if (empty($changedFields)) {
            return;
        }

        $appData = $this->mapShopToApplication($changedFields);
        if (empty($appData)) {
            return;
        }

        self::$syncing = true;
        try {
            $application->update($appData);
        } finally {
            self::$syncing = false;
        }
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
        $wasSyncing = self::$syncing;
        self::$syncing = true;
        try {
            return $callback();
        } finally {
            self::$syncing = $wasSyncing;
        }
    }
}
