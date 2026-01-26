<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

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
     * Field mapping from Shop columns to Application columns.
     */
    public const SHOP_TO_APP_MAP = [
        'shop_name' => 'store_name',
        'category' => 'store_type',
        'owner_name' => 'owner_name',
        'phone' => 'owner_mobile',
        'email' => 'owner_email',
        'gst_number' => 'gst_number',
        'address' => 'store_address',
        'google_map_url' => 'google_map_url',
        'location_lat' => 'lat',
        'location_lng' => 'lng',
        'min_bill_amount' => 'min_bill_amount',
        'country_id' => 'country_id',
        'state_id' => 'state_id',
        'city_id' => 'city_id',
        'tags' => 'tags',
    ];

    /**
     * Create a Shop from a SellerApplication.
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
        return static::create([
            'seller_id' => $sellerId,
            'shop_code' => $application->shop_code,
            'shop_name' => $application->store_name,
            'category' => $application->store_type,
            'owner_name' => $application->owner_name ?? $application->store_name,
            'phone' => $application->owner_mobile,
            'email' => $email ?? $application->owner_email,
            'gst_number' => $application->gst_number,
            'address' => $application->store_address,
            'country_id' => $application->country_id,
            'state_id' => $application->state_id,
            'city_id' => $application->city_id,
            'tags' => $application->tags,
            'google_map_url' => $application->google_map_url,
            'location_lat' => $application->lat,
            'location_lng' => $application->lng,
            'min_bill_amount' => $application->min_bill_amount,
            'razorpay_account_id' => $application->razorpay_account_id,
        ]);
    }
}


