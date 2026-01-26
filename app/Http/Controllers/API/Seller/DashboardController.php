<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store\Seller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

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

        $from = Carbon::createFromFormat('Y-m-d', $request->query('from'))->startOfDay();
        $to = Carbon::createFromFormat('Y-m-d', $request->query('to'))->endOfDay();

        // Get commission/discount directly from application (single source of truth)
        $commissionPercent = $application->commission_percent ?? 0;
        $discountPercent = $application->discount_percent ?? 0;
        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);

        $txQuery = Transaction::query()
            ->where('seller_application_id', $application->id)
            ->whereDate('settled_at', '>=', $from->toDateString())
            ->whereDate('settled_at', '<=', $to->toDateString());

        $successCount = (clone $txQuery)->where('status', 'SUCCESS')->count();
        $failedCount = (clone $txQuery)->where('status', 'FAILED')->count();
        $totalSales = (float) (clone $txQuery)->where('status', 'SUCCESS')->sum('total_amount');
        // Discount applies only if the linked visitor is redeemed AND txn amount meets min bill.
        $discountQuery = Transaction::query()
            ->join('shop_visitors as sv', 'sv.id', '=', 'transactions.visitor_id')
            ->where('transactions.seller_application_id', $application->id)
            ->where('transactions.status', 'SUCCESS')
            ->where('sv.redeemed', true)
            ->whereDate('transactions.settled_at', '>=', $from->toDateString())
            ->whereDate('transactions.settled_at', '<=', $to->toDateString());

        if ($minimumBillAmount > 0) {
            $discountQuery->where('transactions.total_amount', '>=', $minimumBillAmount);
        }

        $totalDiscountGiven = (float) $discountQuery->sum('transactions.discount_amount');

        $visitorsQuery = ShopVisitor::query()
            ->where('seller_application_id', $application->id)
            ->whereDate('visited_on', '>=', $from->toDateString())
            ->whereDate('visited_on', '<=', $to->toDateString());

        $totalVisitors = $visitorsQuery->count();
        $redeemedVisitors = (clone $visitorsQuery)->where('redeemed', true)->count();
        $conversionPercent = $totalVisitors > 0 ? round(($redeemedVisitors / $totalVisitors) * 100, 2) : 0;

        $commissionAmount = round($totalSales * ($commissionPercent / 100), 2);
        $sellerBalance = round($totalSales - $commissionAmount, 2);

        return response()->json([
            'success' => true,
            'data' => [
                'range' => [
                    'from' => $from->toDateString(),
                    'to' => $to->toDateString(),
                ],
                'masterAdmin' => [
                    'commissionPercent' => $commissionPercent,
                    'discountPercent' => $discountPercent,
                    'minimumBillAmount' => $minimumBillAmount,
                ],
                'transactions' => [
                    'successCount' => $successCount,
                    'failedCount' => $failedCount,
                    'totalSales' => $totalSales,
                    'totalDiscountGiven' => $totalDiscountGiven,
                ],
                'visitors' => [
                    'totalVisitors' => $totalVisitors,
                    'redeemedVisitors' => $redeemedVisitors,
                    'conversionPercent' => $conversionPercent,
                ],
                'payout' => [
                    'sellerBalance' => $sellerBalance,
                    'calculation' => [
                        'sellerBalanceFormula' => 'totalSales - (totalSales * commissionPercent/100)',
                        'commissionAmount' => $commissionAmount,
                    ],
                ],
            ],
        ]);
    }

    public function revenueTrend(Request $request)
    {
        $days = (int) ($request->query('days', 7));
        if ($days <= 0 || $days > 365) {
            $days = 7;
        }

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

        $end = now()->startOfDay();
        $start = (clone $end)->subDays($days - 1);

        $sums = Transaction::query()
            ->selectRaw('DATE(settled_at) as d, SUM(total_amount) as amount')
            ->where('seller_application_id', $application->id)
            ->where('status', 'SUCCESS')
            ->whereDate('settled_at', '>=', $start->toDateString())
            ->whereDate('settled_at', '<=', $end->toDateString())
            ->groupBy('d')
            ->pluck('amount', 'd')
            ->toArray();

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $date = (clone $start)->addDays($i)->toDateString();
            $trend[] = [
                'date' => $date,
                'amount' => (float) ($sums[$date] ?? 0),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'days' => $days,
                'trend' => $trend,
            ],
        ]);
    }

    public function visitorsTrend(Request $request)
    {
        $days = (int) ($request->query('days', 7));
        if ($days <= 0 || $days > 365) {
            $days = 7;
        }

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

        $end = now()->startOfDay();
        $start = (clone $end)->subDays($days - 1);

        $counts = ShopVisitor::query()
            ->selectRaw('DATE(visited_on) as d, COUNT(*) as c')
            ->where('seller_application_id', $application->id)
            ->whereDate('visited_on', '>=', $start->toDateString())
            ->whereDate('visited_on', '<=', $end->toDateString())
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $date = (clone $start)->addDays($i)->toDateString();
            $trend[] = [
                'date' => $date,
                'visitors' => (int) ($counts[$date] ?? 0),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'days' => $days,
                'trend' => $trend,
            ],
        ]);
    }
}


