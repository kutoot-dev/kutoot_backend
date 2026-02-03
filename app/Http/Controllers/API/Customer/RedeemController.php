<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Models\Store\SellerApplication;
use App\Models\Store\StoreCategory;
use App\Models\Store\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\RazorpayPayment;
use Razorpay\Api\Api;

/**
 * @group Redeem
 */
class RedeemController extends Controller
{

    /**
     * API 1: GET HOME PAGE DATA
     * GET /api/v1/customer/redeem/home
     */
    public function home(Request $request)
    {
        // 2. Categories
        $categories = StoreCategory::where('is_active', true)
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'image' => $cat->image ? asset($cat->image) : asset('/images/placeholder-category.jpg'),
                    'is_active' => (bool) $cat->is_active
                ];
            });

        // 3. Banners (Mock data)
        $banners = [
            [
                'id' => 11,
                'title' => 'Flat Deals at Partner Stores',
                'sub_title' => 'Redeem coins instantly at premium outlets',
                'tag' => 'Kutoot Rewards',
                'image' => asset('/media/banners/banner1.jpg'),
                'redirect_type' => 'category',
                'redirect_id' => 1,
                'is_active' => true
            ]
        ];

        // 4. Sponsors (Mock)
        $sponsors = [
            [
                'id' => 21,
                'name' => 'Amazon',
                'type' => 'Sponsor',
                'banner' => asset('/media/sponsors/amazon_banner.jpg'),
                'logo' => asset('/media/sponsors/amazon_logo.png'),
                'is_active' => true
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => "Redeem home data fetched",
            'data' => [
                'categories' => $categories,
                'banners' => $banners,
                'sponsors' => $sponsors
            ]
        ]);
    }

    /**
     * API 3: FILTERS (State + City dropdown)
     * GET /api/user/stores/filters
     */
    public function getFilters()
    {
        $statesRaw = \Nnjeim\World\Models\State::with('cities')->get();

        $states = $statesRaw->map(function ($state) {
            return [
                'name' => $state->name,
                'cities' => $state->cities->pluck('name')->toArray()
            ];
        });

        return response()->json([
            'success' => true,
            'message' => "Filters fetched",
            'data' => [
                'states' => $states
            ]
        ]);
    }

    /**
     * API 4: STORES LISTING BY CATEGORY + FILTERS
     * GET /api/user/stores
     */
    public function stores(Request $request)
    {
        $categoryId = $request->category_id;
        $state = $request->state;
        $city = $request->city;
        $search = $request->search;
        $sortBy = $request->sort_by; // rating / name
        $openNow = $request->open_now;
        $page = $request->page ?? 1;
        $limit = $request->limit ?? 12;

        if (!$categoryId) {
            return response()->json(['success' => false, 'message' => 'Category ID is required'], 422);
        }

        // Query approved seller applications instead of shops
        $query = SellerApplication::query()
            ->where('status', SellerApplication::STATUS_APPROVED)
            ->where('store_type', $categoryId)
            ->where('is_active', true);

        if ($state)
            $query->where('store_address', 'like', "%{$state}%");
        if ($city)
            $query->where('store_address', 'like', "%{$city}%");

        if ($search)
            $query->where('store_name', 'like', "%{$search}%");

        // Sort
        if ($sortBy === 'rating') {
            $query->orderBy('rating', 'desc');
        } elseif ($sortBy === 'name') {
            $query->orderBy('store_name', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $paginated = $query->paginate($limit);

        // Category Detail
        $category = StoreCategory::find($categoryId);

        $stores = collect($paginated->items())->map(function ($store) {
            $images = [];
            if ($store->images) {
                $imageArray = is_array($store->images) ? $store->images : json_decode($store->images, true);
                if ($imageArray) {
                    foreach ($imageArray as $img) {
                        $images[] = str_starts_with($img, 'http') ? $img : asset($img);
                    }
                }
            }
            if (empty($images))
                $images = [asset('/images/placeholder-shop.jpg')];

            return [
                'id' => $store->id,
                'name' => $store->store_name,
                'category_id' => $store->store_type,
                'state' => $store->state ?? "Karnataka",
                'city' => $store->city ?? "Bengaluru",
                'address' => $store->store_address,
                'lat' => $store->lat,
                'lng' => $store->lng,
                'cuisines' => 'Supermarket',
                'cost_for_two' => 0,
                'discount_percent' => $store->discount_percent ?? 0,
                'rating' => (float) ($store->rating ?? 0),
                'total_ratings' => $store->total_ratings ?? 0,
                'open_now' => true,
                'images' => $images,
                'is_active' => (bool) $store->is_active
            ];
        });

        return response()->json([
            'success' => true,
            'message' => "Stores fetched",
            'data' => [
                'category' => [
                    'id' => $category->id ?? $categoryId,
                    'name' => $category->name ?? 'Unknown'
                ],
                'pagination' => [
                    'page' => $paginated->currentPage(),
                    'limit' => $paginated->perPage(),
                    'total' => $paginated->total()
                ],
                'stores' => $stores
            ]
        ]);
    }

    /**
     * API 5: STORE DETAILS
     * GET /api/user/stores/:store_id
     */
    public function storeDetails($storeId)
    {
        $store = SellerApplication::where('status', SellerApplication::STATUS_APPROVED)
            ->where('id', $storeId)
            ->first();

        if (!$store) {
            return response()->json(['success' => false, 'message' => 'Store not found'], 404);
        }

        $images = [];
        if ($store->images) {
            $imageArray = is_array($store->images) ? $store->images : json_decode($store->images, true);
            if ($imageArray) {
                foreach ($imageArray as $img) {
                    $images[] = str_starts_with($img, 'http') ? $img : asset($img);
                }
            }
        }
        if (empty($images))
            $images = [asset('/images/placeholder-shop.jpg')];

        return response()->json([
            'success' => true,
            'message' => "Store details fetched",
            'data' => [
                'id' => $store->id,
                'name' => $store->store_name,
                'category_id' => $store->store_type,
                'discount_percent' => $store->discount_percent ?? 0,
                'min_bill_amount' => $store->min_bill_amount ?? 100,
                'max_discount_amount' => null,
                'coin_redeem_allowed' => true,
                'open_now' => true,
                'timings' => "10:00 AM - 10:00 PM",
                'terms' => [
                    "Discount is valid only for billed items.",
                    "Cannot be combined with other offers."
                ],
                'images' => $images,
                'address' => $store->store_address,
                'lat' => $store->lat,
                'lng' => $store->lng
            ]
        ]);
    }

    /**
     * API 6: APPLY COINS PREVIEW
     * POST /api/user/redeem/preview
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:seller_applications,id',
            'bill_amount' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }

        $storeId = $request->store_id;
        $billAmount = $request->bill_amount;
        $user = Auth::guard('api')->user();

        // Get Store (SellerApplication)
        $store = SellerApplication::where('status', SellerApplication::STATUS_APPROVED)
            ->where('id', $storeId)
            ->first();

        if (!$store) {
            return response()->json(['success' => false, 'message' => 'Store not found'], 404);
        }

        $discountPercent = $store->discount_percent ?? 0;

        // 2. Calculate Max Discount
        $maxDiscountByPercent = ($billAmount * $discountPercent) / 100;

        // 3. User Wallet
        $walletCoins = (float) $user->wallet_balance;
        $coinValueINR = (float) config('kutoot.coin_value', 0.25);
        $walletValueINR = $walletCoins * $coinValueINR;

        // 4. Final Logic
        $discountAppliedINR = min($maxDiscountByPercent, $walletValueINR);
        $coinsUsed = ceil($discountAppliedINR / $coinValueINR);
        $finalPayableINR = $billAmount - $discountAppliedINR;

        return response()->json([
            'success' => true,
            'message' => "Redemption preview calculated",
            'data' => [
                'store_id' => $storeId,
                'bill_amount' => (float) $billAmount,
                'discount_percent' => $discountPercent,
                'max_discount_by_percent' => (float) $maxDiscountByPercent,
                'wallet_coins' => (int) $walletCoins,
                'coin_value_inr' => $coinValueINR,
                'wallet_value_inr' => (float) $walletValueINR,
                'discount_applied_inr' => (float) $discountAppliedINR,
                'coins_used' => (int) $coinsUsed,
                'final_payable_inr' => (float) $finalPayableINR
            ]
        ]);
    }

    /**
     * API: INITIALIZE PAYMENT (RAZORPAY)
     * POST /api/v1/customer/redeem/pay
     */
    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:seller_applications,id',
            'bill_amount' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::guard('api')->user();
        $storeId = $request->store_id;
        $billAmount = $request->bill_amount;

        // Get Store (SellerApplication)
        $store = SellerApplication::where('status', SellerApplication::STATUS_APPROVED)
            ->where('id', $storeId)
            ->first();

        if (!$store) {
            return response()->json(['success' => false, 'message' => 'Store not found'], 404);
        }

        $discountPercent = $store->discount_percent ?? 0;
        $commissionPercent = $store->commission_percent ?? 0;

        $walletCoins = (float) $user->wallet_balance;
        $coinValueINR = (float) config('kutoot.coin_value', 0.25);
        $walletValueINR = $walletCoins * $coinValueINR;

        $maxDiscountByPercent = ($billAmount * $discountPercent) / 100;
        $discountAppliedINR = min($maxDiscountByPercent, $walletValueINR);
        $finalPayableINR = $billAmount - $discountAppliedINR;

        if ($finalPayableINR <= 0) {
            return response()->json([
                'success' => true,
                'message' => 'Zero amount to pay, use coins fully',
                'data' => ['razorpay_order_id' => null, 'amount' => 0]
            ]);
        }

        $razorpaySettings = RazorpayPayment::first();
        if (!$razorpaySettings) {
            return response()->json(['success' => false, 'message' => 'Razorpay not configured'], 500);
        }

        $api = new Api($razorpaySettings->key, $razorpaySettings->secret_key);

        // Calculate Splits
        $commissionAmount = ($finalPayableINR * $commissionPercent) / 100;
        $storeAmount = $finalPayableINR - $commissionAmount;

        $orderData = [
            'receipt' => 'RED-' . time() . '-' . $user->id,
            'amount' => (int) round($finalPayableINR * 100),
            'currency' => 'INR',
        ];

        // ADD RAZORPAY ROUTE SPLIT
        if ($store->razorpay_account_id) {
            $orderData['transfers'] = [
                [
                    'account' => $store->razorpay_account_id,
                    'amount' => (int) round($storeAmount * 100),
                    'currency' => 'INR',
                    'on_hold' => 0
                ]
            ];
        }

        try {
            $order = $api->order->create($orderData);

            return response()->json([
                'success' => true,
                'data' => [
                    'razorpay_order_id' => $order->id,
                    'amount' => $finalPayableINR,
                    'razorpay_key' => $razorpaySettings->key,
                    'commission_amount' => $commissionAmount,
                    'store_amount' => $storeAmount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Razorpay error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 7: CONFIRM PAYMENT
     * POST /api/user/redeem/confirm
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:seller_applications,id',
            'bill_amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_signature' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }

        // Check if order already processed
        if ($request->razorpay_order_id) {
            $existing = Transaction::where('razorpay_order_id', $request->razorpay_order_id)->first();
            if ($existing) {
                return response()->json(['success' => true, 'message' => 'Payment already verified', 'data' => $existing]);
            }
        }

        $user = Auth::guard('api')->user();
        $storeId = $request->store_id;
        $billAmount = $request->bill_amount;

        // Verify Razorpay Payment if order_id provided
        if ($request->razorpay_order_id) {
            $razorpaySettings = RazorpayPayment::first();
            $api = new Api($razorpaySettings->key, $razorpaySettings->secret_key);
            try {
                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                ];
                $api->utility->verifyPaymentSignature($attributes);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Payment verification failed: ' . $e->getMessage()], 400);
            }
        }

        DB::beginTransaction();
        try {
            // Get Store (SellerApplication)
            $store = SellerApplication::where('status', SellerApplication::STATUS_APPROVED)
                ->where('id', $storeId)
                ->first();

            if (!$store) {
                throw new \Exception("Store not found");
            }

            $discountPercent = $store->discount_percent ?? 0;
            $commissionPercent = $store->commission_percent ?? 0;

            $walletCoins = (float) $user->wallet_balance;
            $coinValueINR = (float) config('kutoot.coin_value', 0.25);
            $walletValueINR = $walletCoins * $coinValueINR;

            $maxDiscountByPercent = ($billAmount * $discountPercent) / 100;
            $discountAppliedINR = min($maxDiscountByPercent, $walletValueINR);
            $coinsUsed = ceil($discountAppliedINR / $coinValueINR);
            $finalPayableINR = $billAmount - $discountAppliedINR;

            if ($coinsUsed > $walletCoins) {
                throw new \Exception("Insufficient wallet balance");
            }

            // Calculate Split Details for recording
            $commissionAmount = ($finalPayableINR * $commissionPercent) / 100;
            $storeAmount = $finalPayableINR - $commissionAmount;

            // Create Transaction with seller_application_id
            $txn = new Transaction();
            $txn->seller_application_id = $storeId;

            // Link user via visitor
            $visitor = \App\Models\Store\ShopVisitor::where('user_id', $user->id)
                ->where('seller_application_id', $storeId)
                ->first();
            if (!$visitor) {
                $visitorId = \App\Models\Store\ShopVisitor::insertGetId([
                    'seller_application_id' => $storeId,
                    'user_id' => $user->id,
                    'last_visit' => now(),
                    'visit_count' => 1
                ]);
            } else {
                $visitorId = $visitor->id;
            }
            $txn->visitor_id = $visitorId;
            $txn->txn_code = 'KUT' . date('Ymd') . '-' . rand(1000, 9999);
            $txn->total_amount = $billAmount;
            $txn->discount_amount = $discountAppliedINR;
            $txn->commission_amount = $commissionAmount;
            $txn->shop_amount = $storeAmount;
            $txn->razorpay_payment_id = $request->razorpay_payment_id;
            $txn->razorpay_order_id = $request->razorpay_order_id;
            $txn->redeemed_coins = $coinsUsed;
            $txn->status = 'Verified';
            $txn->settled_at = now()->toDateString();
            $txn->save();

            // DEDUCT COINS (FIFO LEDGER)
            if ($coinsUsed > 0) {
                $coinService = app(\App\Services\CoinLedgerService::class);
                $coinService->redeem($user->id, $coinsUsed, $txn->txn_code);
            }

            DB::commit();

            $balanceAfter = $walletCoins - $coinsUsed;

            return response()->json([
                'success' => true,
                'message' => "Redemption successful",
                'data' => [
                    'transaction_id' => $txn->txn_code,
                    'store_id' => $store->id,
                    'store_name' => $store->store_name,
                    'bill_amount' => $billAmount,
                    'discount_applied_inr' => $discountAppliedINR,
                    'coins_used' => $coinsUsed,
                    'final_payable_inr' => $finalPayableINR,
                    'commission_amount' => $commissionAmount,
                    'store_amount' => $storeAmount,
                    'balance_coins_after' => $balanceAfter,
                    'created_at' => $txn->created_at->toIso8601String()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 9: SETTINGS
     * GET /api/user/redeem/settings
     */
    public function settings()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'coin_value_inr' => 0.25,
                'max_discount_percent' => 90,
                'min_bill_amount_global' => 50
            ]
        ]);
    }
}
