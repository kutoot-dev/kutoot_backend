<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminShopCommissionDiscount extends Model
{
    protected $table = 'admin_shop_commission_discounts';

    protected $fillable = [
        'shop_id',
        'commission_percent',
        'discount_percent',
        'minimum_bill_amount',
        'last_updated_on',
    ];

    protected $casts = [
        'shop_id' => 'int',
        'commission_percent' => 'int',
        'discount_percent' => 'int',
        'minimum_bill_amount' => 'float',
        'last_updated_on' => 'date',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function scopeForShop(Builder $query, int $shopId): Builder
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Resolve settings for a shop, falling back to the latest global (shop_id NULL) row.
     */
    public static function resolveForShop(?int $shopId): ?self
    {
        if ($shopId) {
            $row = static::query()->forShop($shopId)->orderByDesc('id')->first();
            if ($row) {
                return $row;
            }
        }

        return static::query()->whereNull('shop_id')->orderByDesc('id')->first();
    }
}


