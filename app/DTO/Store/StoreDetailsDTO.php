<?php

namespace App\DTO\Store;

use App\Models\Store\SellerApplication;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Data Transfer Object for unified store details.
 *
 * This is the single representation of store data used across the application.
 * All data is read from SellerApplication (single source of truth).
 */
class StoreDetailsDTO implements Arrayable, JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $sellerId,
        public readonly ?string $shopCode,
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
        public readonly ?float $commissionPercent,
        public readonly ?float $discountPercent,
        public readonly ?float $rating,
        public readonly ?int $noOfRatings,
        public readonly ?int $totalRatings,
        public readonly bool $isActive,
        public readonly bool $isFeatured,
        public readonly ?string $offerTag,
        public readonly ?string $razorpayAccountId,
        public readonly string $status,
    ) {}

    /**
     * Create DTO from SellerApplication (single source of truth)
     */
    public static function fromApplication(SellerApplication $application): self
    {
        return new self(
            id: $application->id,
            sellerId: $application->seller_id,
            shopCode: $application->shop_code,
            code: $application->shop_code ?? $application->application_id,
            name: $application->store_name,
            category: $application->store_type,
            ownerName: $application->owner_name ?? $application->store_name,
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
            googleMapUrl: $application->google_map_url,
            minBillAmount: (float) $application->min_bill_amount,
            tags: $application->tags,
            commissionPercent: (float) $application->commission_percent,
            discountPercent: (float) $application->discount_percent,
            rating: (float) $application->rating,
            noOfRatings: (int) $application->no_of_ratings,
            totalRatings: (int) $application->total_ratings,
            isActive: (bool) $application->is_active,
            isFeatured: (bool) $application->is_featured,
            offerTag: $application->offer_tag,
            razorpayAccountId: $application->razorpay_account_id,
            status: $application->status,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'seller_id' => $this->sellerId,
            'shop_code' => $this->shopCode,
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
            'commission_percent' => $this->commissionPercent,
            'discount_percent' => $this->discountPercent,
            'rating' => $this->rating,
            'no_of_ratings' => $this->noOfRatings,
            'total_ratings' => $this->totalRatings,
            'is_active' => $this->isActive,
            'is_featured' => $this->isFeatured,
            'offer_tag' => $this->offerTag,
            'razorpay_account_id' => $this->razorpayAccountId,
            'status' => $this->status,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Check if application is approved
     */
    public function isApproved(): bool
    {
        return $this->status === SellerApplication::STATUS_APPROVED;
    }

    /**
     * Check if this is pending application data (not yet approved)
     */
    public function isPending(): bool
    {
        return $this->status === SellerApplication::STATUS_PENDING;
    }

    /**
     * Check if application is verified
     */
    public function isVerified(): bool
    {
        return $this->status === SellerApplication::STATUS_VERIFIED;
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === SellerApplication::STATUS_REJECTED;
    }
}
