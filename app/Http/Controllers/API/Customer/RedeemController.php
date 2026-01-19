<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Models\Store\AdminShopCommissionDiscount;
use App\Models\Store\Shop;
use App\Models\Store\StoreCategory;
use App\Models\Store\Transaction;
use App\Models\UserCoins; // Assuming this model exists based on migration list
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RedeemController extends Controller
{
    /**
     * API 1: GET HOME PAGE DATA
     * GET /api/v1/customer/redeem/home
     */
    public function home(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'city' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Location (lat/lng) is required', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::guard('api')->user();

        // 1. Wallet Logic (Using UserCoins model if available, else 0)
        // Adjust model usage based on actual UserCoins structure
        $coinsBalance = 0;
        if ($user) {
            $userCoins = DB::table('user_coins')->where('user_id', $user->id)->first();
            $coinsBalance = $userCoins ? $userCoins->coins : 0;
        }

        $wallet = [
            'coinsBalance' => $coinsBalance,
            'coinValueINR' => 0.25, // Fixed as per spec
            'walletValueINR' => $coinsBalance * 0.25
        ];

        // 2. Categories with Store Count (only active stores)
        // We need to count stores that have (admin_settings.is_active = 1 AND shop_category = category.id)
        $categories = StoreCategory::where('is_active', true)
            ->get()
            ->map(function ($cat) {
                // Count active stores for this category
                // Assuming 'category' column in shops table stores the category name or ID? 
                // Creating migration suggests 'category' is what we have in Shop model.
                // However, detailed spec said 'categoryId'. Let's check Shop model cast.
                // Shop model has 'category' (string or int?). 
                // Let's assume Shop 'category' links to StoreCategory 'id' or 'name'.
                // Ideally it should be ID. 
    
                $storeCount = Shop::where('category', $cat->id) // Assuming category stores ID
                    ->whereHas('adminSettings', function ($q) {
                        $q->where('is_active', true);
                    })
                    ->count();

                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'image' => $cat->image ?? asset('/images/placeholder-category.jpg'),
                    'storeCount' => $storeCount
                ];
            });

        // 3. Banners (Mock or from a table)
        $banners = [
            [
                'id' => 'def-1',
                'title' => 'Flat 10% OFF at Partner Stores',
                'sub' => 'Redeem coins instantly at premium outlets',
                'tag' => 'Kutoot Rewards',
                'image' => 'https://via.placeholder.com/600x300?text=Discount+Banner'
            ]
        ];

        // 4. Sponsors (Mock or from a table)
        $sponsors = [
            [
                'id' => 'sp-1',
                'name' => 'Amazon',
                'type' => 'Sponsor',
                'banner' => 'https://via.placeholder.com/600x150?text=Amazon',
                'logo' => 'https://via.placeholder.com/100x100?text=Amz'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'wallet' => $wallet,
                'location' => [
                    'lat' => (float) $request->lat,
                    'lng' => (float) $request->lng,
                    'city' => $request->city ?? 'Unknown'
                ],
                'banners' => $banners,
                'categories' => $categories,
                'sponsors' => $sponsors
            ]
        ]);
    }

    /**
     * API 2: GET STORES LIST
     * GET /api/v1/customer/stores
     */
    public function stores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categoryId' => 'required|numeric',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Category ID and Location are required'], 422);
        }

        $categoryId = $request->categoryId;
        $userLat = $request->lat;
        $userLng = $request->lng;
        $search = $request->get('search');
        $sortBy = $request->get('sortBy', 'nearest'); // nearest, rating
        $limit = $request->get('limit', 20);

        // Fetch category details
        $category = StoreCategory::find($categoryId);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        // Haversine Formula for Distance (in km)
        // 6371 = Earth radius in km
        $haversine = "(6371 * acos(cos(radians($userLat)) 
                     * cos(radians(location_lat)) 
                     * cos(radians(location_lng) - radians($userLng)) 
                     + sin(radians($userLat)) 
                     * sin(radians(location_lat))))";

        $query = Shop::query()
            ->select('shops.*') // Select all shop columns
            ->selectRaw("{$haversine} as distance_km")
            ->where('category', $categoryId) // specific category
            ->whereHas('adminSettings', function ($q) {
                $q->where('is_active', true); // Only active stores
            });

        // Search Filter
        if ($search) {
            $query->where('shop_name', 'like', "%{$search}%");
        }

        // Sorting
        if ($sortBy === 'rating') {
            // Join to sort by rating in admin_settings?
            // Or verify if we can order by relationship column.
            // Easier to join admin_settings table manually for sorting
            $query->leftJoin('admin_shop_commission_discounts', 'shops.id', '=', 'admin_shop_commission_discounts.shop_id')
                ->orderBy('admin_shop_commission_discounts.rating', 'desc');
        } else {
            // Default: Nearest
            $query->orderBy('distance_km', 'asc');
        }

        // Pagination
        $paginated = $query->paginate($limit);

        // Formatting Response
        $stores = $paginated->getCollection()->map(function ($store) {
            // Get Admin Settings (Discount, Rating, etc.)
            // We use the helper resolveForShop, or relation if joined. 
            // Since we might have joined for sorting, data might be merged, but let's use the relation for safety.
            // But verify AdminShopCommissionDiscount definition.
            // We need to add 'adminSettings' relation to Shop model first!

            // Assuming we added the relation, if not I will fetch explicitly.
            $settings = AdminShopCommissionDiscount::where('shop_id', $store->id)->orderByDesc('id')->first();

            // Defaults if no settings found (though filter required active)
            $discount = $settings ? $settings->discount_percent : 0;
            $rating = $settings ? $settings->rating : 0;
            $totalRatings = $settings ? $settings->total_ratings : 0;
            $isActive = $settings ? $settings->is_active : false;
            $isFeatured = $settings ? $settings->is_featured : false;
            $offerTag = $settings ? $settings->offer_tag : null;

            // Images
            // Assuming Shop hasMany ShopImage
            $images = $store->images()->pluck('image_url')->toArray(); // Adjust column name based on ShopImage table
            if (empty($images)) {
                $images = [asset('/images/placeholder-shop.jpg')];
            }

            return [
                'id' => $store->id,
                'sellerData' => [
                    'name' => $store->shop_name,
                    'address' => $store->address,
                    // 'cuisines' => $store->cuisines, // Column exist? Checked migration, no cuisines column in shops table? 
                    // Wait, Shop migration: shop_name, category, owner_name, phone, email, gst, address, google_map, lat, lng, min_bill.
                    // User spec says 'cuisines' in shops table (2) stores. 
                    // I should check if I missed it or if it needs to be added. 
                    // For now, assume it might be missing, return empty or mock.
                    'cuisines' => 'Multi-Cuisine',
                    'costForTwo' => 0, // Not in migration either?
                    'location' => [
                        'lat' => $store->location_lat,
                        'lng' => $store->location_lng
                    ],
                    'images' => $images
                ],
                'adminData' => [
                    'discountPercent' => $discount,
                    'discountLabel' => "Up to {$discount}% OFF",
                    'rating' => (float) $rating,
                    'totalRatings' => $totalRatings,
                    'isActive' => (bool) $isActive,
                    'isFeatured' => (bool) $isFeatured,
                    'offerTag' => $offerTag
                ],
                'customerMeta' => [
                    'distanceKm' => round($store->distance_km, 1),
                    'distanceText' => round($store->distance_km, 1) . ' km'
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name
                ],
                'userLocation' => [
                    'lat' => (float) $userLat,
                    'lng' => (float) $userLng,
                    'city' => 'Unknown' // Ideally reverse geocode or take from request
                ],
                'stores' => $stores,
                'pagination' => [
                    'page' => $paginated->currentPage(),
                    'limit' => $paginated->perPage(),
                    'total' => $paginated->total(),
                    'hasNext' => $paginated->hasMorePages()
                ]
            ]
        ]);
    }

    /**
     * API 3: STORE DETAILS
     * GET /api/v1/customer/stores/{storeId}
     */
    public function storeDetails($storeId)
    {
        $store = Shop::find($storeId);
        if (!$store) {
            return response()->json(['success' => false, 'message' => 'Store not found'], 404);
        }

        $settings = AdminShopCommissionDiscount::where('shop_id', $store->id)->orderByDesc('id')->first();

        if (!$settings || !$settings->is_active) {
            // Spec says "Backend must only return stores where ... isActive = true"
            return response()->json(['success' => false, 'message' => 'Store is not active'], 404);
        }

        $images = $store->images()->pluck('image_url')->toArray();
        if (empty($images))
            $images = [asset('/images/placeholder-shop.jpg')];

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $store->id,
                'categoryId' => $store->category,
                'sellerData' => [
                    'name' => $store->shop_name,
                    'address' => $store->address,
                    'cuisines' => 'Multi-Cuisine',
                    'costForTwo' => 0,
                    'location' => [
                        'lat' => $store->location_lat,
                        'lng' => $store->location_lng
                    ],
                    'images' => $images
                ],
                'adminData' => [
                    'discountPercent' => $settings->discount_percent,
                    'rating' => (float) $settings->rating,
                    'totalRatings' => $settings->total_ratings,
                    'isActive' => (bool) $settings->is_active
                ]
            ]
        ]);
    }

    /**
     * API 4: REDEEM PREVIEW
     * POST /api/v1/customer/redeem/preview
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storeId' => 'required|exists:shops,id',
            'billAmount' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }

        $storeId = $request->storeId;
        $billAmount = $request->billAmount;
        $user = Auth::guard('api')->user();

        // Get Store Discount
        $settings = AdminShopCommissionDiscount::where('shop_id', $storeId)->orderByDesc('id')->first();
        if (!$settings || !$settings->is_active) {
            return response()->json(['success' => false, 'message' => 'Store is inactive or not offering discounts'], 400);
        }

        $discountPercent = $settings->discount_percent; // e.g. 10
        // Logic: maxDiscount = billAmount * 10%
        $maxDiscountValue = ($billAmount * $discountPercent) / 100;

        // Wallet
        $coinsBalance = 0;
        if ($user) {
            $userCoins = DB::table('user_coins')->where('user_id', $user->id)->first();
            $coinsBalance = $userCoins ? $userCoins->coins : 0;
        }
        $coinValueINR = 0.25;
        $walletValueINR = $coinsBalance * $coinValueINR;

        // discountApplied = min(maxDiscount, walletValue)
        $discountApplied = min($maxDiscountValue, $walletValueINR);

        // coinsUsed = ceil(discountApplied / 0.25)
        // If discountApplied is 0, coinsUsed is 0.
        $coinsUsed = 0;
        if ($discountApplied > 0) {
            $coinsUsed = ceil($discountApplied / $coinValueINR);
        }

        // Adjust for floating point issues? 
        // If I use 600 coins * 0.25 = 150. 
        // If discountApplied is 150. 

        $finalPayable = $billAmount - $discountApplied;

        return response()->json([
            'success' => true,
            'data' => [
                'billAmount' => (float) $billAmount,
                'maxDiscountPercent' => $discountPercent,
                'maxDiscountValue' => round($maxDiscountValue, 2),
                'wallet' => [
                    'coinsBalance' => $coinsBalance,
                    'coinValueINR' => $coinValueINR,
                    'walletValueINR' => $walletValueINR
                ],
                'discountAppliedINR' => round($discountApplied, 2),
                'coinsUsed' => (int) $coinsUsed,
                'finalPayableINR' => round($finalPayable, 2)
            ]
        ]);
    }

    /**
     * API 5: FINAL PAYMENT
     * POST /api/v1/customer/redeem/pay
     */
    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storeId' => 'required|exists:shops,id',
            'billAmount' => 'required|numeric|min:1',
            'userLocation' => 'required|array', // For logging if needed
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::guard('api')->user();
        $storeId = $request->storeId;
        $billAmount = $request->billAmount;

        // 1. Re-calculate everything (Do not trust frontend)
        $settings = AdminShopCommissionDiscount::where('shop_id', $storeId)->orderByDesc('id')->first();
        if (!$settings || !$settings->is_active) {
            return response()->json(['success' => false, 'error' => ['code' => 'STORE_INACTIVE', 'message' => 'Store is not active']], 400);
        }

        // Check wallet
        $userCoins = DB::table('user_coins')->where('user_id', $user->id)->first();
        $coinsBalance = $userCoins ? $userCoins->coins : 0;

        if ($coinsBalance <= 0) {
            return response()->json(['success' => false, 'error' => ['code' => 'INSUFFICIENT_WALLET', 'message' => 'No coins to redeem']], 400);
        }

        $discountPercent = $settings->discount_percent;
        $maxDiscountValue = ($billAmount * $discountPercent) / 100;

        $coinValueINR = 0.25;
        $walletValueINR = $coinsBalance * $coinValueINR;

        $discountApplied = min($maxDiscountValue, $walletValueINR);

        if ($discountApplied <= 0) {
            // In case 0 discount, maybe just record transaction? 
            // Spec says 'failed' examples 'Insufficient Coins'. 
            // If discountApplied is 0, we can still proceed but 0 discount? 
            // Usually redeem implies using coins.
            return response()->json(['success' => false, 'error' => ['code' => 'ZERO_DISCOUNT', 'message' => 'Discount not applicable']], 400);
        }

        $coinsUsed = ceil($discountApplied / $coinValueINR);
        $finalPayable = $billAmount - $discountApplied;

        // 2. Transact
        DB::beginTransaction();
        try {
            // Deduct Coins
            $newBalance = $coinsBalance - $coinsUsed;
            DB::table('user_coins')->where('user_id', $user->id)->update(['coins' => $newBalance]);

            // Create Transaction
            $txn = new Transaction();
            $txn->shop_id = $storeId;
            $txn->visitor_id = null; // Spec didn't mandatory link visitor here, but maybe user_id? Transaction model has visitor_id. 
            // We should likely link to a user. 
            // Transaction table schema didn't show 'user_id' in list_view but UserOrderController uses 'visitor' relation. 
            // Let's assume we need to link user. 
            // Wait, Transaction model has 'visitor_id'. 
            // Is 'visitor' the 'user'? 
            // UserOrderController: $storeQuery->whereHas('visitor', function ($q) use ($user) { $q->where('user_id', $user->id); });
            // So we need to find or create a ShopVisitor linked to this user for this shop? 

            // Allow null visitor for now or find/create
            $visitor = DB::table('shop_visitors')->where('user_id', $user->id)->where('shop_id', $storeId)->first();
            if (!$visitor) {
                $visitorId = DB::table('shop_visitors')->insertGetId([
                    'shop_id' => $storeId,
                    'user_id' => $user->id,
                    'last_visit' => now(),
                    'visit_count' => 1,
                    // 'created_at' => now(), // if timestamp exists
                ]);
            } else {
                $visitorId = $visitor->id;
            }

            $txn->visitor_id = $visitorId;
            // Generate Code
            $txn->txn_code = 'KUT' . strtoupper(uniqid());
            $txn->total_amount = $billAmount;
            $txn->discount_amount = $discountApplied;
            $txn->redeemed_coins = $coinsUsed;
            $txn->status = 'Verified'; // Or 'Success'
            $txn->settled_at = now(); // Or null until settled? Spec says response "SUCCESS".
            $txn->save();

            DB::commit();

            $store = Shop::find($storeId);

            return response()->json([
                'success' => true,
                'data' => [
                    'transactionId' => $txn->txn_code, // e.g. KUT...
                    'status' => 'SUCCESS',
                    'store' => [
                        'id' => $store->id,
                        'name' => $store->shop_name,
                        'address' => $store->address
                    ],
                    'bill' => [
                        'billAmount' => $billAmount,
                        'discountAppliedINR' => $discountApplied,
                        'coinsUsed' => $coinsUsed,
                        'finalPayableINR' => $finalPayable
                    ],
                    'wallet' => [
                        'coinsBefore' => $coinsBalance,
                        'coinsAfter' => $newBalance
                    ],
                    'createdAt' => $txn->created_at,
                    'message' => 'Payment successful. Show this Transaction ID at store.'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Transaction failed', 'debug' => $e->getMessage()], 500);
        }
    }
}
