<?php

namespace App\Http\Controllers\WEB\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use App\Models\User;
use App\Models\UserCoins;
use App\Services\CoinLedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Visitors
 */
class VisitorsController extends Controller
{
    private function parseIndianOrIsoDateTime(?string $value, ?Carbon $fallback = null): ?Carbon
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return $fallback;
        }

        // Prefer Indian format (dd-mm-yyyy HH:MM), but accept ISO inputs too.
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

    public function index(Request $request)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application || $application->status !== 'APPROVED') {
            return view('store.visitors', [
                'rows' => collect(),
                'filters' => $request->all(),
                'total' => 0,
            ]);
        }

        $q = ShopVisitor::query()->where('seller_application_id', $application->id)->with('user');

        if ($request->filled('from')) {
            $q->whereDate('visited_on', '>=', $request->query('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('visited_on', '<=', $request->query('to'));
        }
        if ($request->filled('search')) {
            $search = $request->query('search');
            $q->where(function ($sub) use ($search) {
                $sub
                    ->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                if (is_numeric($search)) {
                    $sub->orWhere('id', (int) $search);
                }
            });
        }

        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);

        return view('store.visitors', [
            'filters' => $request->all(),
            'minimumBillAmount' => $minimumBillAmount,
        ]);
    }

    public function data(Request $request)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $rawLength = (int) $request->input('length', 10);
        // DataTables uses -1 for "All"
        $length = $rawLength === -1 ? -1 : min(200, max(10, $rawLength));

        if (!$application || $application->status !== 'APPROVED') {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);

        $base = ShopVisitor::query()
            ->from('shop_visitors as sv')
            ->leftJoin('users as u', 'u.id', '=', 'sv.user_id')
            ->leftJoin('transactions as t', function ($join) use ($application) {
                $join->on('t.visitor_id', '=', 'sv.id')->where('t.seller_application_id', '=', $application->id);
            })
            ->where('sv.seller_application_id', $application->id)
            ->select([
                'sv.id',
                'sv.user_id',
                'u.name as user_name',
                'u.phone as user_phone',
                'sv.visited_on',
                'sv.created_at as visited_at',
                'sv.redeemed',
                't.txn_code',
                't.total_amount',
                't.discount_amount',
                't.redeemed_coins',
                't.status',
            ]);

        // Optional date filters from the page filter form
        // Prefer datetime range (visited_at == sv.created_at). Fallback to date-only if provided.
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
            $base->whereBetween('sv.created_at', [$fromC->toDateTimeString(), $toC->toDateTimeString()]);
        } else {
            if ($request->filled('from')) {
                $base->whereDate('sv.visited_on', '>=', $request->query('from'));
            }
            if ($request->filled('to')) {
                $base->whereDate('sv.visited_on', '<=', $request->query('to'));
            }
        }

        $recordsTotal = (clone $base)->count('sv.id');

        // Search
        $search = (string) data_get($request->input('search'), 'value', '');
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('u.name', 'like', "%{$search}%")
                    ->orWhere('u.phone', 'like', "%{$search}%")
                    ->orWhere('t.txn_code', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('sv.id', (int) $search);
                }
            });
        }

        $recordsFiltered = (clone $base)->count('sv.id');

        // If UI asked for "All", return all filtered rows (reset paging).
        if ($length === -1) {
            $start = 0;
            $length = $recordsFiltered;
        }

        // Ordering
        $orderIdx = (int) data_get($request->input('order'), '0.column', 0);
        $orderDir = strtolower((string) data_get($request->input('order'), '0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $columns = (array) $request->input('columns', []);
        $orderKey = (string) data_get($columns, "{$orderIdx}.data", 'visited_on');

        $orderMap = [
            'visitor_id' => 'sv.id',
            'name' => 'u.name',
            'phone' => 'u.phone',
            'visited_at' => 'sv.created_at',
            'redeemed' => 'sv.redeemed',
            'txn_code' => 't.txn_code',
            'total_amount' => 't.total_amount',
            'discount_amount' => 't.discount_amount',
            'redeemed_coins' => 't.redeemed_coins',
            'status' => 't.status',
        ];
        $base->orderBy($orderMap[$orderKey] ?? 'sv.created_at', $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $maskPhone = function (?string $phone) {
            $phone = (string) ($phone ?? '');
            if ($phone === '') {
                return null;
            }
            $len = strlen($phone);
            if ($len <= 3) {
                return $phone;
            }
            return substr($phone, 0, 3) . str_repeat('X', $len - 3);
        };

        $data = $rows->values()->map(function ($r, $idx) use ($minimumBillAmount, $maskPhone, $start) {
            $totalAmount = (float) ($r->total_amount ?? 0);
            $belowMin = $minimumBillAmount > 0 && $totalAmount < $minimumBillAmount;
            $eligible = (bool) $r->redeemed && !$belowMin && ($r->status === 'SUCCESS');

            return [
                'sr_no' => $start + $idx + 1,
                'visitor_id' => (int) $r->id,
                'user_id' => $r->user_id ? (int) $r->user_id : null,
                'name' => $r->user_name,
                'phone' => $maskPhone($r->user_phone),
                'visited_on' => $r->visited_on,
                'visited_at' => $r->visited_at,
                'redeemed' => (bool) $r->redeemed,
                'txn_code' => $r->txn_code ?? null,
                'total_amount' => $totalAmount,
                'discount_amount' => $eligible ? (float) ($r->discount_amount ?? 0) : 0.0,
                'redeemed_coins' => $eligible ? (int) ($r->redeemed_coins ?? 0) : 0,
                'status' => $r->status ?? null,
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Calculate transaction details for a visitor
     * GET /store/visitors/{visitorId}/calculate-transaction
     */
    public function calculateTransaction($visitorId, Request $request)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application || $application->status !== 'APPROVED') {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'bill_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $visitor = ShopVisitor::where('id', $visitorId)
            ->where('seller_application_id', $application->id)
            ->with('user')
            ->first();

        if (!$visitor) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor not found'
            ], 404);
        }

        if (!$visitor->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor has no associated user'
            ], 400);
        }

        $billAmount = (float) $request->bill_amount;
        $discountPercent = (float) ($application->discount_percent ?? 0);
        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);
        $coinValue = (float) config('kutoot.coin_value', 0.25);

        // Get user's available coins
        $coins = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $visitor->user_id)
            ->whereDate('coin_expires', '>=', now()->toDateString())
            ->first();

        $creditCoins = $coins->credit ?? 0;
        $debitCoins = $coins->debit ?? 0;
        $availableCoins = max(0, $creditCoins - $debitCoins);

        // Check eligibility
        $isEligible = $visitor->redeemed && $billAmount >= $minimumBillAmount;

        // Calculate max discount based on discount percent
        $maxDiscountByPercent = ($billAmount * $discountPercent) / 100;

        // Calculate max coins that can be redeemed
        $maxCoinsByDiscount = floor($maxDiscountByPercent / $coinValue);
        $redeemableCoins = min($availableCoins, $maxCoinsByDiscount);

        // Calculate actual discount amount
        $discountAmount = $redeemableCoins * $coinValue;

        // Calculate final amount
        $finalAmount = $billAmount - $discountAmount;

        return response()->json([
            'success' => true,
            'data' => [
                'visitor' => [
                    'id' => $visitor->id,
                    'user_id' => $visitor->user_id,
                    'user_name' => $visitor->user->name ?? 'N/A',
                    'user_phone' => $visitor->user->phone ?? 'N/A',
                    'redeemed' => $visitor->redeemed,
                ],
                'shop' => [
                    'id' => $application->id,
                    'shop_name' => $application->store_name,
                ],
                'settings' => [
                    'discount_percent' => $discountPercent,
                    'minimum_bill_amount' => $minimumBillAmount,
                    'coin_value' => $coinValue,
                ],
                'calculation' => [
                    'bill_amount' => $billAmount,
                    'available_coins' => (int) $availableCoins,
                    'max_discount_by_percent' => round($maxDiscountByPercent, 2),
                    'max_coins_by_discount' => (int) $maxCoinsByDiscount,
                    'redeemable_coins' => (int) $redeemableCoins,
                    'discount_amount' => round($discountAmount, 2),
                    'final_amount' => round($finalAmount, 2),
                    'is_eligible' => $isEligible,
                    'eligibility_message' => !$visitor->redeemed
                        ? 'Visitor has not redeemed yet'
                        : ($billAmount < $minimumBillAmount
                            ? "Bill amount must be at least {$minimumBillAmount}"
                            : 'Eligible for discount'),
                ],
            ],
        ]);
    }

    /**
     * Calculate transaction details for a user (before creating visitor)
     * GET /store/visitors/calculate-by-user
     */
    public function calculateByUser(Request $request)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application || $application->status !== 'APPROVED') {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'bill_amount' => 'required|numeric|min:0',
            'redeemed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user_id;
        $billAmount = (float) $request->bill_amount;
        $redeemed = $request->filled('redeemed') ? (bool) $request->redeemed : false;

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $discountPercent = (float) ($application->discount_percent ?? 0);
        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);
        $coinValue = (float) config('kutoot.coin_value', 0.25);

        // Get user's available coins
        $coins = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $userId)
            ->whereDate('coin_expires', '>=', now()->toDateString())
            ->first();

        $creditCoins = $coins->credit ?? 0;
        $debitCoins = $coins->debit ?? 0;
        $availableCoins = max(0, $creditCoins - $debitCoins);

        // Check eligibility (using redeemed flag from request)
        $isEligible = $redeemed && $billAmount >= $minimumBillAmount;

        // Calculate max discount based on discount percent
        $maxDiscountByPercent = ($billAmount * $discountPercent) / 100;

        // Calculate max coins that can be redeemed
        $maxCoinsByDiscount = floor($maxDiscountByPercent / $coinValue);
        $redeemableCoins = min($availableCoins, $maxCoinsByDiscount);

        // Calculate actual discount amount
        $discountAmount = $redeemableCoins * $coinValue;

        // Calculate final amount
        $finalAmount = $billAmount - $discountAmount;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name ?? 'N/A',
                    'user_phone' => $user->phone ?? 'N/A',
                    'redeemed' => $redeemed,
                ],
                'shop' => [
                    'id' => $application->id,
                    'shop_name' => $application->store_name,
                ],
                'settings' => [
                    'discount_percent' => $discountPercent,
                    'minimum_bill_amount' => $minimumBillAmount,
                    'coin_value' => $coinValue,
                ],
                'calculation' => [
                    'bill_amount' => $billAmount,
                    'available_coins' => (int) $availableCoins,
                    'max_discount_by_percent' => round($maxDiscountByPercent, 2),
                    'max_coins_by_discount' => (int) $maxCoinsByDiscount,
                    'redeemable_coins' => (int) $redeemableCoins,
                    'discount_amount' => round($discountAmount, 2),
                    'final_amount' => round($finalAmount, 2),
                    'is_eligible' => $isEligible,
                    'eligibility_message' => !$redeemed
                        ? 'Visitor has not redeemed yet'
                        : ($billAmount < $minimumBillAmount
                            ? "Bill amount must be at least {$minimumBillAmount}"
                            : 'Eligible for discount'),
                ],
            ],
        ]);
    }

    /**
     * Create a new transaction for a visitor
     * POST /store/visitors/{visitorId}/create-transaction
     */
    public function createTransaction($visitorId, Request $request)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application || $application->status !== 'APPROVED') {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'bill_amount' => 'required|numeric|min:0',
            'txn_code' => 'required|string|max:255|unique:transactions,txn_code',
        ], [
            'bill_amount.required' => 'Bill amount is required',
            'bill_amount.numeric' => 'Bill amount must be a number',
            'bill_amount.min' => 'Bill amount must be at least 0',
            'txn_code.required' => 'Transaction ID is required',
            'txn_code.string' => 'Transaction ID must be a string',
            'txn_code.max' => 'Transaction ID must not exceed 255 characters',
            'txn_code.unique' => 'Transaction ID already exists. Please use a different ID.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $visitor = ShopVisitor::where('id', $visitorId)
            ->where('seller_application_id', $application->id)
            ->with('user')
            ->first();

        if (!$visitor) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor not found'
            ], 404);
        }

        if (!$visitor->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor has no associated user'
            ], 400);
        }

        $billAmount = (float) $request->bill_amount;
        $discountPercent = (float) ($application->discount_percent ?? 0);
        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);
        $coinValue = (float) config('kutoot.coin_value', 0.25);

        // Get user's available coins
        $coins = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $visitor->user_id)
            ->whereDate('coin_expires', '>=', now()->toDateString())
            ->first();

        $creditCoins = $coins->credit ?? 0;
        $debitCoins = $coins->debit ?? 0;
        $availableCoins = max(0, $creditCoins - $debitCoins);

        // Calculate discount and coins
        $isEligible = $visitor->redeemed && $billAmount >= $minimumBillAmount;
        $maxDiscountByPercent = ($billAmount * $discountPercent) / 100;
        $maxCoinsByDiscount = floor($maxDiscountByPercent / $coinValue);
        $redeemableCoins = $isEligible ? min($availableCoins, $maxCoinsByDiscount) : 0;
        $discountAmount = $redeemableCoins * $coinValue;

        try {
            DB::beginTransaction();

            // Get transaction code (required field)
            $txnCode = trim($request->txn_code);

            // Double-check uniqueness (race condition protection)
            $exists = Transaction::where('txn_code', $txnCode)->exists();
            if ($exists) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction ID already exists. Please use a different ID.'
                ], 400);
            }

            // Update visitor's visited_on to current date
            $visitor->visited_on = now()->toDateString();
            $visitor->save();

            // Create transaction
            $transaction = Transaction::create([
                'seller_application_id' => $application->id,
                'visitor_id' => $visitor->id,
                'txn_code' => $txnCode,
                'total_amount' => $billAmount,
                'discount_amount' => round($discountAmount, 2),
                'redeemed_coins' => (int) $redeemableCoins,
                'status' => 'SUCCESS',
                'settled_at' => now(),
            ]);

            // Deduct coins from user if coins were redeemed
            if ($redeemableCoins > 0 && $isEligible) {
                $coinService = app(CoinLedgerService::class);
                $coinService->redeem($visitor->user_id, (int) $redeemableCoins, $txnCode);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => [
                    'transaction' => [
                        'id' => $transaction->id,
                        'txn_code' => $transaction->txn_code,
                        'total_amount' => $transaction->total_amount,
                        'discount_amount' => $transaction->discount_amount,
                        'redeemed_coins' => $transaction->redeemed_coins,
                        'status' => $transaction->status,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create transaction: ' . $e->getMessage(), [
                'visitor_id' => $visitorId,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search users by name, phone, or email
     * GET /store/visitors/search-users
     */
    public function searchUsers(Request $request)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application || $application->status !== 'APPROVED') {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        }

        $search = $request->query('search', '');

        if (strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $users = User::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'phone', 'email')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Add transaction with user (creates visitor if needed)
     * POST /store/visitors/add-transaction-with-user
     */
    public function addTransactionWithUser(Request $request)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application || $application->status !== 'APPROVED') {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'bill_amount' => 'required|numeric|min:0',
            'txn_code' => 'required|string|max:255|unique:transactions,txn_code',
            'visited_on' => 'nullable|date',
            'redeemed' => 'nullable|boolean',
        ], [
            'user_id.required' => 'User is required',
            'user_id.exists' => 'Selected user does not exist',
            'bill_amount.required' => 'Bill amount is required',
            'bill_amount.numeric' => 'Bill amount must be a number',
            'bill_amount.min' => 'Bill amount must be at least 0',
            'txn_code.required' => 'Transaction ID is required',
            'txn_code.string' => 'Transaction ID must be a string',
            'txn_code.max' => 'Transaction ID must not exceed 255 characters',
            'txn_code.unique' => 'Transaction ID already exists. Please use a different ID.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user_id;
        $billAmount = (float) $request->bill_amount;
        $txnCode = trim($request->txn_code);
        $visitedOn = $request->filled('visited_on')
            ? Carbon::parse($request->visited_on)->toDateString()
            : now()->toDateString();
        $redeemed = $request->filled('redeemed') ? (bool) $request->redeemed : false;

        // Get or create visitor
        $visitor = ShopVisitor::firstOrCreate(
            [
                'seller_application_id' => $application->id,
                'user_id' => $userId,
            ],
            [
                'visited_on' => $visitedOn,
                'redeemed' => $redeemed,
            ]
        );

        // Update visitor if already exists
        if ($visitor->wasRecentlyCreated === false) {
            $visitor->visited_on = $visitedOn;
            $visitor->redeemed = $redeemed;
            $visitor->save();
        }

        // Load user relationship
        $visitor->load('user');

        // Check if user exists
        if (!$visitor->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor has no associated user'
            ], 400);
        }

        // Double-check transaction code uniqueness (race condition protection)
        $exists = Transaction::where('txn_code', $txnCode)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction ID already exists. Please use a different ID.'
            ], 400);
        }

        // Calculate transaction details
        $discountPercent = (float) ($application->discount_percent ?? 0);
        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);
        $coinValue = (float) config('kutoot.coin_value', 0.25);

        // Get user's available coins
        $coins = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $visitor->user_id)
            ->whereDate('coin_expires', '>=', now()->toDateString())
            ->first();

        $creditCoins = $coins->credit ?? 0;
        $debitCoins = $coins->debit ?? 0;
        $availableCoins = max(0, $creditCoins - $debitCoins);

        // Calculate discount and coins
        $isEligible = $visitor->redeemed && $billAmount >= $minimumBillAmount;
        $maxDiscountByPercent = ($billAmount * $discountPercent) / 100;
        $maxCoinsByDiscount = floor($maxDiscountByPercent / $coinValue);
        $redeemableCoins = $isEligible ? min($availableCoins, $maxCoinsByDiscount) : 0;
        $discountAmount = $redeemableCoins * $coinValue;

        try {
            DB::beginTransaction();

            // Create transaction
            $transaction = Transaction::create([
                'seller_application_id' => $application->id,
                'visitor_id' => $visitor->id,
                'txn_code' => $txnCode,
                'total_amount' => $billAmount,
                'discount_amount' => round($discountAmount, 2),
                'redeemed_coins' => (int) $redeemableCoins,
                'status' => 'SUCCESS',
                'settled_at' => now(),
            ]);

            // Deduct coins from user if coins were redeemed
            if ($redeemableCoins > 0 && $isEligible) {
                $coinService = app(CoinLedgerService::class);
                $coinService->redeem($visitor->user_id, (int) $redeemableCoins, $txnCode);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => [
                    'visitor' => [
                        'id' => $visitor->id,
                        'user_id' => $visitor->user_id,
                        'visited_on' => $visitor->visited_on,
                        'redeemed' => $visitor->redeemed,
                    ],
                    'transaction' => [
                        'id' => $transaction->id,
                        'txn_code' => $transaction->txn_code,
                        'total_amount' => $transaction->total_amount,
                        'discount_amount' => $transaction->discount_amount,
                        'redeemed_coins' => $transaction->redeemed_coins,
                        'status' => $transaction->status,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create transaction with user: ' . $e->getMessage(), [
                'user_id' => $userId,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction: ' . $e->getMessage()
            ], 500);
        }
    }
}


