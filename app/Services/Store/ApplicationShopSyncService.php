<?php

namespace App\Services\Store;

use App\DTO\Store\StoreDetailsDTO;
use App\Models\Store\Seller;
use App\Models\Store\SellerApplication;
use App\Models\Store\Shop;
use App\Repositories\Store\StoreDetailsRepository;

/**
 * Service layer for store operations.
 *
 * SINGLE SOURCE OF TRUTH PATTERN:
 * - Uses StoreDetailsRepository for all data access
 * - Shop table = authoritative source for approved sellers
 * - Application table = only for pending applications and audit trail
 *
 * @see StoreDetailsRepository for field definitions and data access
 */
class StoreService
{
    protected StoreDetailsRepository $repository;

    public function __construct(?StoreDetailsRepository $repository = null)
    {
        $this->repository = $repository ?? new StoreDetailsRepository();
    }

    /**
     * Get store details for a seller.
     * Returns unified DTO from the authoritative source.
     */
    public function getStoreDetails(Seller $seller): ?StoreDetailsDTO
    {
        return $this->repository->getForSeller($seller);
    }

    /**
     * Get store details by seller ID.
     */
    public function getStoreDetailsBySellerId(int $sellerId): ?StoreDetailsDTO
    {
        return $this->repository->getBySellerId($sellerId);
    }

    /**
     * Get store details from application.
     * Returns Shop data if approved, else Application data.
     */
    public function getStoreDetailsFromApplication(SellerApplication $application): StoreDetailsDTO
    {
        return $this->repository->getFromApplication($application);
    }

    /**
     * Update store details for a seller.
     * Always updates the Shop table (single source of truth).
     *
     * @param Seller $seller
     * @param array $data Request data in any format (camelCase, snake_case)
     * @return Shop
     */
    public function updateStoreDetails(Seller $seller, array $data): Shop
    {
        return $this->repository->updateForSeller($seller, $data);
    }

    /**
     * Update store details for a shop.
     */
    public function updateShop(Shop $shop, array $data): Shop
    {
        return $this->repository->update($shop, $data);
    }

    /**
     * Create shop from approved application.
     * Used during application approval flow.
     */
    public function createShopFromApplication(
        SellerApplication $application,
        int $sellerId,
        ?string $overrideEmail = null
    ): Shop {
        return $this->repository->createFromApplication($application, $sellerId, $overrideEmail);
    }

    /**
     * Update pending application.
     * Use this before application is approved.
     */
    public function updatePendingApplication(SellerApplication $application, array $data): SellerApplication
    {
        return $this->repository->updateApplication($application, $data);
    }

    /**
     * Get validation rules for store details.
     */
    public function getValidationRules(bool $required = false): array
    {
        return $this->repository->getValidationRules($required);
    }

    /**
     * Get the repository instance.
     */
    public function getRepository(): StoreDetailsRepository
    {
        return $this->repository;
    }

    /**
     * Static helper: Get field definitions.
     * Use StoreDetailsRepository::FIELDS directly for the single source of truth.
     */
    public static function getFieldDefinitions(): array
    {
        return StoreDetailsRepository::FIELDS;
    }

    /**
     * Static helper: Create shop from application (legacy compatibility).
     * @deprecated Use instance method createShopFromApplication() instead
     */
    public static function createShopFromApplicationStatic(
        SellerApplication $application,
        int $sellerId,
        ?string $email = null
    ): Shop {
        $repository = new StoreDetailsRepository();
        return $repository->createFromApplication($application, $sellerId, $email);
    }
}

// Backward compatibility alias
class_alias(StoreService::class, 'App\Services\Store\ApplicationShopSyncService');
