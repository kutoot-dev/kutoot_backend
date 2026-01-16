<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store\AdminShopCommissionDiscount;
use App\Models\Store\Seller;
use Illuminate\Support\Facades\Auth;

class MasterAdminSettingsController extends Controller
{
    public function show()
    {
        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('shop');
        $shop = $seller->shop;

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found for this seller',
            ], 404);
        }

        $master = AdminShopCommissionDiscount::resolveForShop($shop->id);

        return response()->json([
            'success' => true,
            'data' => [
                'commissionPercent' => $master?->commission_percent ?? 0,
                'discountPercent' => $master?->discount_percent ?? 0,
                'minimumBillAmount' => (float) ($master?->minimum_bill_amount ?? 0),
                'lastUpdatedOn' => optional($master?->last_updated_on)->toDateString(),
            ],
        ]);
    }
}


