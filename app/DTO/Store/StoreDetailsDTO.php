<?php

namespace App\DTO\Store;

use App\Models\Store\SellerApplication;
use App\Models\Store\Shop;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Data Transfer Object for unified store details.
 *
 * This is the single representation of store data used across the application.
 * Data is read from Shop (if exists) or SellerApplication (for pending applications).
 */
class StoreDetailsDTO implements Arrayable, JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $sellerId,
        public readonly ?string $code,
        public readonly ?string $name,
        public readonly ?string $category,
        public readonly ?string $ownerName,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly ?string $gstNumber,
        public readonly ?string $address,
        public readonly ?int $countryId,
        public readonly ?int $stateId,
        public readonly ?int $cityId,
        public readonly ?string $country,
        public readonly ?string $state,
        public readonly ?string $city,
        public readonly ?float $lat,
        public readonly ?float $lng,
        public readonly ?string $googleMapUrl,
        public readonly ?float $minBillAmount,
        public readonly ?array $tags,
        public readonly string $source, // 'shop' or 'application'
    ) {}

    /**
     * Create DTO from Shop model (authoritative source for approved sellers)
     */
    public static function fromShop(Shop $shop): self
    {
        return new self(
            id: $shop->id,
            sellerId: $shop->seller_id,
            code: $shop->shop_code,
            name: $shop->shop_name,
            category: $shop->category,
            ownerName: $shop->owner_name,
            phone: $shop->phone,
            email: $shop->email,
            gstNumber: $shop->gst_number,
            address: $shop->address,
            countryId: $shop->country_id,
            stateId: $shop->state_id,
            cityId: $shop->city_id,
            country: $shop->country?->name,
            state: $shop->state?->name,
            city: $shop->city?->name,
            lat: $shop->location_lat,
            lng: $shop->location_lng,
            googleMapUrl: $shop->google_map_url,
            minBillAmount: $shop->min_bill_amount,
            tags: $shop->tags,
            source: 'shop',
        );
    }

    /**
     * Create DTO from SellerApplication (for pending/rejected applications without shop)
     */
    public static function fromApplication(SellerApplication $application): self
    {
        return new self(
            id: null,
            sellerId: $application->seller_id,
            code: null,
            name: $application->store_name,
            category: $application->store_type,
            ownerName: $application->store_name,
            phone: $application->owner_mobile,
            email: $application->owner_email,
            gstNumber: $application->gst_number,
            address: $application->store_address,
            countryId: $application->country_id,
            stateId: $application->state_id,
            cityId: $application->city_id,
            country: $application->country,
            state: $application->state,
            city: $application->city,
            lat: (float) $application->lat,
            lng: (float) $application->lng,
            googleMapUrl: null,
            minBillAmount: (float) $application->min_bill_amount,
            tags: null,
            source: 'application',
        );
    }

    /**
     * Get the authoritative source - Shop if exists, else Application
     */
    public static function fromSellerOrApplication(
        ?Shop $shop,
        ?SellerApplication $application
    ): ?self {
        if ($shop) {
            return self::fromShop($shop);
        }
        if ($application) {
            return self::fromApplication($application);
        }
        return null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'seller_id' => $this->sellerId,
            'code' => $this->code,
            'name' => $this->name,
            'category' => $this->category,
            'owner_name' => $this->ownerName,
            'phone' => $this->phone,
            'email' => $this->email,
            'gst_number' => $this->gstNumber,
            'address' => $this->address,
            'country_id' => $this->countryId,
            'state_id' => $this->stateId,
            'city_id' => $this->cityId,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'google_map_url' => $this->googleMapUrl,
            'min_bill_amount' => $this->minBillAmount,
            'tags' => $this->tags,
            'source' => $this->source,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Check if data comes from the authoritative Shop source
     */
    public function isFromShop(): bool
    {
        return $this->source === 'shop';
    }

    /**
     * Check if this is pending application data (not yet approved)
     */
    public function isPendingApplication(): bool
    {
        return $this->source === 'application';
    }
}
