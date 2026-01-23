<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store\Shop;
use App\Models\Store\ShopImage;
use App\Models\Store\AdminShopCommissionDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    /**
     * API: Get list of approved stores
     * GET /api/admin/approved-stores
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 20);
        $search = $request->query('search');
        $category = $request->query('category');
        $state = $request->query('state');
        $city = $request->query('city');
        $country = $request->query('country');

        $shops = Shop::query()
            ->with(['images', 'activeAdminSettings', 'seller'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('shop_name', 'like', "%{$search}%")
                        ->orWhere('shop_code', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($category, fn($q) => $q->where('category', $category))
            ->when($state, fn($q) => $q->where('state', $state))
            ->when($city, fn($q) => $q->where('city', $city))
            ->when($country, fn($q) => $q->where('country', $country))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $shops->map(function ($shop) {
                $settings = $shop->activeAdminSettings;
                return [
                    'id' => $shop->id,
                    'shopCode' => $shop->shop_code,
                    'shopName' => $shop->shop_name,
                    'ownerName' => $shop->owner_name,
                    'phone' => $shop->phone,
                    'email' => $shop->email,
                    'category' => $shop->category,
                    'address' => $shop->address,
                    'state' => $shop->state,
                    'city' => $shop->city,
                    'country' => $shop->country,
                    'googleMapUrl' => $shop->google_map_url,
                    'locationLat' => $shop->location_lat,
                    'locationLng' => $shop->location_lng,
                    'minBillAmount' => $shop->min_bill_amount,
                    'images' => $shop->images->map(fn($img) => [
                        'id' => $img->id,
                        'url' => $img->image_url,
                    ]),
                    'commissionPercent' => $settings?->commission_percent,
                    'discountPercent' => $settings?->discount_percent,
                    'rating' => $settings?->rating,
                    'totalRatings' => $settings?->total_ratings,
                    'isActive' => $settings?->is_active ?? true,
                    'isFeatured' => $settings?->is_featured ?? false,
                    'offerTag' => $settings?->offer_tag,
                    'createdAt' => $shop->created_at->toIso8601String(),
                ];
            }),
            'pagination' => [
                'currentPage' => $shops->currentPage(),
                'lastPage' => $shops->lastPage(),
                'perPage' => $shops->perPage(),
                'total' => $shops->total(),
            ]
        ]);
    }

    /**
     * API: Get single shop details
     * GET /api/admin/shops/{shopId}
     */
    public function show($shopId)
    {
        $shop = Shop::with(['images', 'activeAdminSettings', 'seller'])->find($shopId);

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        $settings = $shop->activeAdminSettings;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $shop->id,
                'shopCode' => $shop->shop_code,
                'shopName' => $shop->shop_name,
                'ownerName' => $shop->owner_name,
                'phone' => $shop->phone,
                'email' => $shop->email,
                'category' => $shop->category,
                'gstNumber' => $shop->gst_number,
                'address' => $shop->address,
                'state' => $shop->state,
                'city' => $shop->city,
                'country' => $shop->country,
                'googleMapUrl' => $shop->google_map_url,
                'locationLat' => $shop->location_lat,
                'locationLng' => $shop->location_lng,
                'minBillAmount' => $shop->min_bill_amount,
                'razorpayAccountId' => $shop->razorpay_account_id,
                'images' => $shop->images->map(fn($img) => [
                    'id' => $img->id,
                    'url' => $img->image_url,
                ]),
                'seller' => $shop->seller ? [
                    'id' => $shop->seller->id,
                    'sellerCode' => $shop->seller->seller_code,
                    'username' => $shop->seller->username,
                    'ownerName' => $shop->seller->owner_name,
                    'email' => $shop->seller->email,
                    'phone' => $shop->seller->phone,
                    'status' => $shop->seller->status,
                ] : null,
                'settings' => $settings ? [
                    'id' => $settings->id,
                    'commissionPercent' => $settings->commission_percent,
                    'discountPercent' => $settings->discount_percent,
                    'minimumBillAmount' => $settings->minimum_bill_amount,
                    'rating' => $settings->rating,
                    'totalRatings' => $settings->total_ratings,
                    'isActive' => $settings->is_active,
                    'isFeatured' => $settings->is_featured,
                    'offerTag' => $settings->offer_tag,
                    'lastUpdatedOn' => $settings->last_updated_on,
                ] : null,
                'createdAt' => $shop->created_at->toIso8601String(),
                'updatedAt' => $shop->updated_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * API: Update shop settings (commission, discount, rating, etc.)
     * PUT /api/admin/shops/{shopId}
     */
    public function update(Request $request, $shopId)
    {
        $shop = Shop::find($shopId);

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'commissionPercent' => 'nullable|numeric|min:0|max:100',
            'discountPercent' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'isActive' => 'nullable|boolean',
            'isFeatured' => 'nullable|boolean',
            'offerTag' => 'nullable|string|max:100',
            'minimumBillAmount' => 'nullable|numeric|min:0',
            // Shop basic info
            'shopName' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'googleMapUrl' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update shop basic info
        $shopUpdateData = [];
        if ($request->has('shopName')) $shopUpdateData['shop_name'] = $request->shopName;
        if ($request->has('address')) $shopUpdateData['address'] = $request->address;
        if ($request->has('state')) $shopUpdateData['state'] = $request->state;
        if ($request->has('city')) $shopUpdateData['city'] = $request->city;
        if ($request->has('country')) $shopUpdateData['country'] = $request->country;
        if ($request->has('googleMapUrl')) $shopUpdateData['google_map_url'] = $request->googleMapUrl;

        if (!empty($shopUpdateData)) {
            $shop->update($shopUpdateData);
        }

        // Update or create admin settings
        $settings = AdminShopCommissionDiscount::where('shop_id', $shop->id)
            ->orderByDesc('id')
            ->first();

        $settingsData = [];
        if ($request->has('commissionPercent')) $settingsData['commission_percent'] = $request->commissionPercent;
        if ($request->has('discountPercent')) $settingsData['discount_percent'] = $request->discountPercent;
        if ($request->has('rating')) $settingsData['rating'] = $request->rating;
        if ($request->has('isActive')) $settingsData['is_active'] = $request->isActive;
        if ($request->has('isFeatured')) $settingsData['is_featured'] = $request->isFeatured;
        if ($request->has('offerTag')) $settingsData['offer_tag'] = $request->offerTag;
        if ($request->has('minimumBillAmount')) $settingsData['minimum_bill_amount'] = $request->minimumBillAmount;

        if (!empty($settingsData)) {
            $settingsData['last_updated_on'] = now();

            if ($settings) {
                $settings->update($settingsData);
            } else {
                // Create new settings record
                $settingsData['shop_id'] = $shop->id;
                $settingsData['commission_percent'] = $settingsData['commission_percent'] ?? 10;
                $settingsData['discount_percent'] = $settingsData['discount_percent'] ?? 0;
                $settingsData['rating'] = $settingsData['rating'] ?? 0;
                $settingsData['total_ratings'] = 0;
                $settingsData['is_active'] = $settingsData['is_active'] ?? true;
                $settingsData['is_featured'] = $settingsData['is_featured'] ?? false;
                AdminShopCommissionDiscount::create($settingsData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Shop updated successfully'
        ]);
    }

    /**
     * API: Upload store images
     * POST /api/admin/shops/{shopId}/images
     */
    public function uploadImages(Request $request, $shopId)
    {
        $shop = Shop::find($shopId);

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $filename = 'shop_' . $shop->id . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->move(public_path('uploads/shops'), $filename);
            $imageUrl = '/uploads/shops/' . $filename;

            $shopImage = ShopImage::create([
                'shop_id' => $shop->id,
                'image_url' => $imageUrl,
            ]);

            $uploadedImages[] = [
                'id' => $shopImage->id,
                'url' => $imageUrl,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully',
            'data' => $uploadedImages
        ]);
    }

    /**
     * API: Delete a store image
     * DELETE /api/admin/shops/{shopId}/images/{imageId}
     */
    public function deleteImage($shopId, $imageId)
    {
        $shop = Shop::find($shopId);

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        $image = ShopImage::where('shop_id', $shopId)->where('id', $imageId)->first();

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        // Delete file from disk
        $filePath = public_path($image->image_url);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    /**
     * API: Get list of unique filter values for stores
     * GET /api/admin/shops/filters
     */
    public function getFilters()
    {
        $categories = Shop::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $states = Shop::whereNotNull('state')
            ->distinct()
            ->pluck('state')
            ->filter()
            ->values();

        $cities = Shop::whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->filter()
            ->values();

        $countries = Shop::whereNotNull('country')
            ->distinct()
            ->pluck('country')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'states' => $states,
                'cities' => $cities,
                'countries' => $countries,
            ]
        ]);
    }
}
