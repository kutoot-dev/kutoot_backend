<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store\Seller;
use Illuminate\Support\Facades\Auth;

/**
 * @group Master Admin Settings
 */
class MasterAdminSettingsController extends Controller
{
    public function show()
    {
        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application || $application->status !== 'APPROVED') {
            return response()->json([
                'success' => false,
                'message' => 'Store not found for this seller',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'commissionPercent' => $application->commission_percent ?? 0,
                'discountPercent' => $application->discount_percent ?? 0,
                'minimumBillAmount' => (float) ($application->min_bill_amount ?? 0),
                'lastUpdatedOn' => optional($application->last_updated_on)->toDateString(),
            ],
        ]);
    }
}


