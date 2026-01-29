<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store\Seller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitorsController extends Controller
{
    public function index(Request $request)
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

        $from = $request->query('from');
        $to = $request->query('to');
        $search = $request->query('search');
        $page = max(1, (int) $request->query('page', 1));
        $limit = min(100, max(1, (int) $request->query('limit', 20)));

        $q = ShopVisitor::query()->where('seller_application_id', $application->id)->with('user');

        if ($from) {
            $q->whereDate('visited_on', '>=', $from);
        }
        if ($to) {
            $q->whereDate('visited_on', '<=', $to);
        }
        if ($search) {
            $q->where(function ($sub) use ($search) {
                $sub
                    ->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                    });
                if (is_numeric($search)) {
                    $sub->orWhere('id', (int) $search);
                }
            });
        }

        $total = (clone $q)->count();
        $rows = (clone $q)
            ->orderByDesc('visited_on')
            ->forPage($page, $limit)
            ->get();

        $visitorIds = $rows->pluck('id')->all();
        $txByVisitor = Transaction::query()
            ->where('seller_application_id', $application->id)
            ->whereIn('visitor_id', $visitorIds)
            ->get()
            ->keyBy('visitor_id');

        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);

        $outRows = $rows->map(function (ShopVisitor $v) use ($txByVisitor, $minimumBillAmount) {
            $tx = $txByVisitor->get($v->id);
            $belowMin = $tx && $minimumBillAmount > 0 && (float) $tx->total_amount < $minimumBillAmount;
            $eligibleForDiscount = (bool) $v->redeemed && !$belowMin;

            return [
                'visitorId' => (int) $v->id,
                'name' => $v->user?->name,
                'phone' => $v->masked_phone,
                'visitedOn' => optional($v->visited_on)->toDateString(),
                'redeemed' => (bool) $v->redeemed,
                'transaction' => [
                    'txnId' => $tx?->txn_code,
                    'totalAmount' => (float) ($tx?->total_amount ?? 0),
                    'discountAmount' => $eligibleForDiscount ? (float) ($tx?->discount_amount ?? 0) : 0.0,
                    'redeemedCoins' => $eligibleForDiscount ? (int) ($tx?->redeemed_coins ?? 0) : 0,
                    'status' => $tx?->status,
                ],
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'rows' => $outRows,
            ],
        ]);
    }
}


