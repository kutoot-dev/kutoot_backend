<?php

namespace App\Http\Controllers\WEB\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use App\Models\User;
use App\Models\UserCoins;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class UserLedgerController extends Controller
{
    private function parseIndianOrIsoDateTime(?string $value, ?Carbon $fallback = null): ?Carbon
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return $fallback;
        }

        foreach (['d-m-Y H:i', 'd-m-Y H:i:s', 'd-m-Y', 'Y-m-d\TH:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i', 'Y-m-d H:i:s', 'Y-m-d'] as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $value);
                if ($d !== false) {
                    if ($fmt === 'd-m-Y' || $fmt === 'Y-m-d') {
                        $d = $d->startOfDay();
                    }
                    return $d;
                }
            } catch (\Throwable $e) {
                // keep trying
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function userCoinExpiryColumn(): ?string
    {
        // Legacy migration used `coupon_expires`, but current schema uses `coin_expires`
        if (Schema::hasColumn('table_usercoins', 'coin_expires')) {
            return 'coin_expires';
        }
        if (Schema::hasColumn('table_usercoins', 'coupon_expires')) {
            return 'coupon_expires';
        }
        return null;
    }

    private function getUserCoinBalance(int $userId): int
    {
        $q = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $userId);

        $expiryCol = $this->userCoinExpiryColumn();
        if ($expiryCol) {
            $q->whereDate($expiryCol, '>=', now()->toDateString());
        }

        $coins = $q->first();
        $credit = (int) ($coins->credit ?? 0);
        $debit = (int) ($coins->debit ?? 0);
        return max(0, $credit - $debit);
    }

    private function ensureUserBelongsToStore(int $applicationId, int $userId): bool
    {
        return ShopVisitor::query()
            ->where('seller_application_id', $applicationId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function show(Request $request, User $user)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application || $application->status !== 'APPROVED' || !$this->ensureUserBelongsToStore($application->id, (int) $user->id)) {
            return redirect()->route('store.visitors')->with('error', 'User not found in this store visitors.');
        }

        $balanceCoins = $this->getUserCoinBalance((int) $user->id);

        $totals = Transaction::query()
            ->where('seller_application_id', $application->id)
            ->whereHas('visitor', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->selectRaw('COUNT(*) as txn_count')
            ->selectRaw("SUM(CASE WHEN status = 'SUCCESS' THEN total_amount ELSE 0 END) as total_sales")
            ->selectRaw("SUM(CASE WHEN status = 'SUCCESS' THEN discount_amount ELSE 0 END) as total_discount")
            ->selectRaw("SUM(CASE WHEN status = 'SUCCESS' THEN redeemed_coins ELSE 0 END) as total_coins")
            ->first();

        return view('store.user_ledger', [
            'user' => $user,
            'balanceCoins' => $balanceCoins,
            'totals' => $totals,
            'filters' => $request->all(),
        ]);
    }

    public function data(Request $request, User $user)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $rawLength = (int) $request->input('length', 10);
        $length = $rawLength === -1 ? -1 : min(200, max(10, $rawLength));

        if (!$application || $application->status !== 'APPROVED' || !$this->ensureUserBelongsToStore($application->id, (int) $user->id)) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $base = Transaction::query()
            ->from('transactions as t')
            ->join('shop_visitors as sv', 'sv.id', '=', 't.visitor_id')
            ->leftJoin('table_usercoins as uc', function ($join) use ($user) {
                $join->on('uc.order_id', '=', 't.id')
                    ->where('uc.user_id', '=', (int) $user->id)
                    ->where('uc.type', '=', 'debit');
            })
            ->where('t.seller_application_id', $application->id)
            ->where('sv.user_id', (int) $user->id)
            ->select([
                't.id',
                't.txn_code',
                't.total_amount',
                't.discount_amount',
                't.redeemed_coins',
                't.status',
                't.created_at as visited_at',
                'uc.coins as coins_debited',
            ]);

        // Datetime filters (store time of sale)
        $fromDt = $request->query('from_dt');
        $toDt = $request->query('to_dt');
        if ($fromDt || $toDt) {
            $fromC = $this->parseIndianOrIsoDateTime($fromDt, Carbon::create(2000, 1, 1, 0, 0, 0))
                ?? Carbon::create(2000, 1, 1, 0, 0, 0);
            $toC = $this->parseIndianOrIsoDateTime($toDt, Carbon::now()->addYears(10))
                ?? Carbon::now()->addYears(10);
            if ($fromC->greaterThan($toC)) {
                [$fromC, $toC] = [$toC, $fromC];
            }
            $base->whereBetween('t.created_at', [$fromC->toDateTimeString(), $toC->toDateTimeString()]);
        }

        $recordsTotal = (clone $base)->count('t.id');

        // Search
        $search = (string) data_get($request->input('search'), 'value', '');
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('t.txn_code', 'like', "%{$search}%")
                    ->orWhere('t.status', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('t.total_amount', (float) $search)
                        ->orWhere('t.discount_amount', (float) $search)
                        ->orWhere('t.redeemed_coins', (int) $search);
                }
            });
        }

        $recordsFiltered = (clone $base)->count('t.id');

        if ($length === -1) {
            $start = 0;
            $length = $recordsFiltered;
        }

        // Ordering
        $orderIdx = (int) data_get($request->input('order'), '0.column', 0);
        $orderDir = strtolower((string) data_get($request->input('order'), '0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $columns = (array) $request->input('columns', []);
        $orderKey = (string) data_get($columns, "{$orderIdx}.data", 'visited_at');
        $orderMap = [
            'txn_code' => 't.txn_code',
            'visited_at' => 't.created_at',
            'total_amount' => 't.total_amount',
            'discount_amount' => 't.discount_amount',
            'redeemed_coins' => 't.redeemed_coins',
            'coins_debited' => 'uc.coins',
            'status' => 't.status',
        ];
        $base->orderBy($orderMap[$orderKey] ?? 't.created_at', $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = $rows->values()->map(function ($r, $idx) use ($start) {
            return [
                'sr_no' => $start + $idx + 1,
                'txn_code' => $r->txn_code,
                'visited_at' => $r->visited_at,
                'total_amount' => (float) ($r->total_amount ?? 0),
                'discount_amount' => (float) ($r->discount_amount ?? 0),
                'redeemed_coins' => (int) ($r->redeemed_coins ?? 0),
                'coins_debited' => (int) ($r->coins_debited ?? 0),
                'status' => $r->status,
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function campaignData(Request $request, User $user)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $rawLength = (int) $request->input('length', 10);
        $length = $rawLength === -1 ? -1 : min(200, max(10, $rawLength));

        if (!$application || $application->status !== 'APPROVED' || !$this->ensureUserBelongsToStore($application->id, (int) $user->id)) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        // Query campaign coins (coins earned from campaign purchases)
        // Join: table_usercoins -> table_purchasecoins (on purchased_camp_id)
        // For each purchase, show: campaign title, purchase date, coins earned (credit),
        // coins used (sum of debits), expiry date
        $base = UserCoins::query()
            ->from('table_usercoins as uc')
            ->join('table_purchasecoins as pc', 'pc.id', '=', 'uc.purchased_camp_id')
            ->where('uc.user_id', (int) $user->id)
            ->where('uc.type', 'credit')
            ->where('uc.purchased_camp_id', '!=', null)
            ->select([
                'uc.id',
                'uc.purchased_camp_id',
                'uc.coins as coins_earned',
                'uc.coin_expires',
                'uc.created_at as purchase_date',
                'pc.camp_title',
                'pc.camp_ticket_price',
                'pc.quantity',
                'pc.payment_status',
            ])
            ->distinct();

        // Add coins used (debits) as subquery
        $base->selectRaw(
            "(SELECT COALESCE(SUM(coins), 0) FROM table_usercoins WHERE purchased_camp_id = uc.purchased_camp_id AND user_id = ? AND type = 'debit') as coins_used",
            [(int) $user->id]
        );

        // Datetime filters
        $fromDt = $request->query('from_dt');
        $toDt = $request->query('to_dt');
        if ($fromDt || $toDt) {
            $fromC = $this->parseIndianOrIsoDateTime($fromDt, Carbon::create(2000, 1, 1, 0, 0, 0))
                ?? Carbon::create(2000, 1, 1, 0, 0, 0);
            $toC = $this->parseIndianOrIsoDateTime($toDt, Carbon::now()->addYears(10))
                ?? Carbon::now()->addYears(10);
            if ($fromC->greaterThan($toC)) {
                [$fromC, $toC] = [$toC, $fromC];
            }
            $base->whereBetween('uc.created_at', [$fromC->toDateTimeString(), $toC->toDateTimeString()]);
        }

        $recordsTotal = (clone $base)->count();

        // Search
        $search = (string) data_get($request->input('search'), 'value', '');
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('pc.camp_title', 'like', "%{$search}%")
                    ->orWhere('pc.payment_status', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('uc.coins', (int) $search)
                        ->orWhere('pc.camp_ticket_price', (float) $search);
                }
            });
        }

        $recordsFiltered = (clone $base)->count();

        if ($length === -1) {
            $start = 0;
            $length = $recordsFiltered;
        }

        // Ordering
        $orderIdx = (int) data_get($request->input('order'), '0.column', 0);
        $orderDir = strtolower((string) data_get($request->input('order'), '0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $columns = (array) $request->input('columns', []);
        $orderKey = (string) data_get($columns, "{$orderIdx}.data", 'purchase_date');
        $orderMap = [
            'camp_title' => 'pc.camp_title',
            'purchase_date' => 'uc.created_at',
            'coins_earned' => 'uc.coins',
            'coins_used' => 'coins_used',
            'coin_expires' => 'uc.coin_expires',
            'payment_status' => 'pc.payment_status',
        ];
        $base->orderBy($orderMap[$orderKey] ?? 'uc.created_at', $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = $rows->values()->map(function ($r, $idx) use ($start) {
            $expiresIn = null;
            $isExpired = false;
            if ($r->coin_expires) {
                $expiryDate = Carbon::parse($r->coin_expires);
                $now = Carbon::now();
                $isExpired = $now->isAfter($expiryDate);
                if (!$isExpired) {
                    $expiresIn = $now->diffInDays($expiryDate);
                }
            }

            return [
                'sr_no' => $start + $idx + 1,
                'camp_title' => $r->camp_title ?? '-',
                'purchase_date' => $r->purchase_date,
                'coins_earned' => (int) ($r->coins_earned ?? 0),
                'coins_used' => (int) ($r->coins_used ?? 0),
                'coins_balance' => (int) (($r->coins_earned ?? 0) - ($r->coins_used ?? 0)),
                'coin_expires' => $r->coin_expires,
                'expires_in' => $isExpired ? 'Expired' : ($expiresIn !== null ? $expiresIn . ' days' : '-'),
                'is_expired' => $isExpired,
                'payment_status' => $r->payment_status ?? '-',
                'camp_ticket_price' => (float) ($r->camp_ticket_price ?? 0),
                'quantity' => (int) ($r->quantity ?? 1),
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}


