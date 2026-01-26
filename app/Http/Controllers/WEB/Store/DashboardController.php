<?php

namespace App\Http\Controllers\WEB\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private function parseIndianOrIsoDate(?string $value, ?Carbon $fallback = null): Carbon
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return $fallback ? $fallback->copy() : now();
        }

        // Prefer Indian format (dd-mm-yyyy), but accept ISO (yyyy-mm-dd) as fallback.
        foreach (['d-m-Y', 'Y-m-d'] as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $value);
                if ($d !== false) {
                    return $d->startOfDay();
                }
            } catch (\Throwable $e) {
                // keep trying
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $e) {
            return $fallback ? $fallback->copy() : now();
        }
    }

    public function index(Request $request)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        $defaultFromC = now()->subDays(6)->startOfDay();
        $defaultToC = now()->startOfDay();

        $fromC = $this->parseIndianOrIsoDate($request->query('from'), $defaultFromC);
        $toC = $this->parseIndianOrIsoDate($request->query('to'), $defaultToC);
        if ($fromC->greaterThan($toC)) {
            [$fromC, $toC] = [$toC, $fromC];
        }

        // DB filters need ISO dates; UI should show Indian dates.
        $fromDb = $fromC->toDateString();
        $toDb = $toC->toDateString();
        $fromUi = $fromC->format('d-m-Y');
        $toUi = $toC->format('d-m-Y');

        // Get settings directly from seller application
        $commissionPercent = (float) ($application?->commission_percent ?? 0);
        $discountPercent = (float) ($application?->discount_percent ?? 0);
        $minimumBillAmount = (float) ($application?->min_bill_amount ?? 0);

        $totalSales = 0;
        $totalDiscountGiven = 0;
        $totalCoinsRedeemed = 0;
        $successCount = 0;
        $failedCount = 0;
        $totalVisitors = 0;
        $redeemedVisitors = 0;
        $charts = [
            'labels' => [],
            'revenue' => [],
            'visitors' => [],
            'success' => 0,
            'failed' => 0,
        ];

        if ($application && $application->status === 'APPROVED') {
            $txQ = Transaction::query()
                ->where('seller_application_id', $application->id)
                ->whereDate('settled_at', '>=', $fromDb)
                ->whereDate('settled_at', '<=', $toDb);
            $successCount = (clone $txQ)->where('status', 'SUCCESS')->count();
            $failedCount = (clone $txQ)->where('status', 'FAILED')->count();
            $totalSales = (float) (clone $txQ)->where('status', 'SUCCESS')->sum('total_amount');

            // Discount applies only if visitor is redeemed AND txn amount meets min bill.
            $discountQ = Transaction::query()
                ->join('shop_visitors as sv', 'sv.id', '=', 'transactions.visitor_id')
                ->where('transactions.seller_application_id', $application->id)
                ->where('transactions.status', 'SUCCESS')
                ->where('sv.redeemed', true)
                ->whereDate('transactions.settled_at', '>=', $fromDb)
                ->whereDate('transactions.settled_at', '<=', $toDb);
            if ($minimumBillAmount > 0) {
                $discountQ->where('transactions.total_amount', '>=', $minimumBillAmount);
            }
            $totalDiscountGiven = (float) $discountQ->sum('transactions.discount_amount');
            $totalCoinsRedeemed = (int) $discountQ->sum('transactions.redeemed_coins');

            $vQ = ShopVisitor::query()
                ->where('seller_application_id', $application->id)
                ->whereDate('visited_on', '>=', $fromDb)
                ->whereDate('visited_on', '<=', $toDb);
            $totalVisitors = $vQ->count();
            $redeemedVisitors = (clone $vQ)->where('redeemed', true)->count();

            // Chart data (daily)
            $days = min(366, $fromC->diffInDays($toC) + 1);

            $revMap = Transaction::query()
                ->selectRaw('DATE(settled_at) as d, SUM(total_amount) as amount')
                ->where('seller_application_id', $application->id)
                ->where('status', 'SUCCESS')
                ->whereDate('settled_at', '>=', $fromDb)
                ->whereDate('settled_at', '<=', $toDb)
                ->groupBy('d')
                ->pluck('amount', 'd')
                ->toArray();

            $visMap = ShopVisitor::query()
                ->selectRaw('DATE(visited_on) as d, COUNT(*) as c')
                ->where('seller_application_id', $application->id)
                ->whereDate('visited_on', '>=', $fromDb)
                ->whereDate('visited_on', '<=', $toDb)
                ->groupBy('d')
                ->pluck('c', 'd')
                ->toArray();

            $labels = [];
            $revenue = [];
            $visitors = [];
            for ($i = 0; $i < $days; $i++) {
                $d = $fromC->copy()->addDays($i)->toDateString();
                $labels[] = Carbon::parse($d)->format('d-m-Y');
                $revenue[] = (float) ($revMap[$d] ?? 0);
                $visitors[] = (int) ($visMap[$d] ?? 0);
            }

            $charts = [
                'labels' => $labels,
                'revenue' => $revenue,
                'visitors' => $visitors,
                'success' => (int) $successCount,
                'failed' => (int) $failedCount,
            ];
        }

        $commissionAmount = round($totalSales * ($commissionPercent / 100), 2);
        $sellerBalance = round($totalSales - $commissionAmount, 2);
        $kutootBalance = $commissionAmount;
        $conversionPercent = $totalVisitors > 0 ? round(($redeemedVisitors / $totalVisitors) * 100, 2) : 0;

        $formulas = [
            'totalSales' => 'TotalSales = SUM(total_amount) WHERE status=SUCCESS',
            'kutootBalance' => 'KutootBalance = TotalSales * (commissionPercent/100)',
            'storeBalance' => 'StoreBalance = TotalSales - KutootBalance',
            'discountGiven' => 'DiscountGiven = SUM(discount_amount) WHERE status=SUCCESS AND visitor.redeemed=true AND total_amount>=minimum_bill_amount',
            'coinsRedeemed' => 'PerTxnCoins = MIN(UserCoinBalance, FLOOR((total_amount * discountPercent/100) / ' . (float) config('kutoot.coin_value', 0.25) . ')); CoinsRedeemed = SUM(PerTxnCoins)',
            'coinToMoney' => 'DiscountAmount = CoinsRedeemed * ' . (float) config('kutoot.coin_value', 0.25) . ' (₹ per coin)',
            'conversion' => 'ConversionPercent = (redeemedVisitors / totalVisitors) * 100',
        ];

        return view('store.dashboard', [
            'shop' => $application,
            'range' => ['from' => $fromUi, 'to' => $toUi],
            'master' => [
                'commissionPercent' => $commissionPercent,
                'discountPercent' => $discountPercent,
                'minimumBillAmount' => $minimumBillAmount,
            ],
            'formulas' => $formulas,
            'charts' => $charts,
            'kpis' => [
                'totalSales' => $totalSales,
                'sellerBalance' => $sellerBalance,
                'kutootBalance' => $kutootBalance,
                'totalDiscountGiven' => $totalDiscountGiven,
                'totalCoinsRedeemed' => $totalCoinsRedeemed,
                'successCount' => $successCount,
                'failedCount' => $failedCount,
                'conversionPercent' => $conversionPercent,
                'totalVisitors' => $totalVisitors,
                'redeemedVisitors' => $redeemedVisitors,
            ],
        ]);
    }
}


