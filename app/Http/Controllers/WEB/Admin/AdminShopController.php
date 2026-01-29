<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store\SellerApplication;
use App\Models\Store\ShopImage;
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

        $applications = SellerApplication::query()
            ->where('status', 'APPROVED')
            ->with(['shopImages', 'seller'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('store_name', 'like', "%{$search}%")
                        ->orWhere('shop_code', 'like', "%{$search}%")
                        ->orWhere('store_address', 'like', "%{$search}%");
                });
            })
            ->when($category, fn($q) => $q->where('store_type', $category))
            ->when($state, fn($q) => $q->where('state', $state))
            ->when($city, fn($q) => $q->where('city', $city))
            ->when($country, fn($q) => $q->where('country', $country))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'shopCode' => $app->shop_code,
                    'shopName' => $app->store_name,
                    'ownerName' => $app->owner_name,
                    'phone' => $app->owner_mobile,
                    'email' => $app->owner_email,
                    'category' => $app->store_type,
                    'address' => $app->store_address,
                    'state' => $app->state,
                    'city' => $app->city,
                    'country' => $app->country,
                    'googleMapUrl' => $app->google_map_url,
                    'locationLat' => $app->lat,
                    'locationLng' => $app->lng,
                    'minBillAmount' => $app->min_bill_amount,
                    'images' => $app->shopImages->map(fn($img) => [
                        'id' => $img->id,
                        'url' => $img->image_url,
                    ]),
                    'commissionPercent' => $app->commission_percent,
                    'discountPercent' => $app->discount_percent,
                    'rating' => $app->rating,
                    'totalRatings' => $app->total_ratings,
                    'isActive' => $app->is_active ?? true,
                    'isFeatured' => $app->is_featured ?? false,
                    'offerTag' => $app->offer_tag,
                    'createdAt' => $app->created_at->toIso8601String(),
                ];
            }),
            'pagination' => [
                'currentPage' => $applications->currentPage(),
                'lastPage' => $applications->lastPage(),
                'perPage' => $applications->perPage(),
                'total' => $applications->total(),
            ]
        ]);
    }

    /**
     * API: Get single store details
     * GET /api/admin/shops/{applicationId}
     */
    public function show($applicationId)
    {
        $app = SellerApplication::with(['shopImages', 'seller'])
            ->where('status', 'APPROVED')
            ->find($applicationId);

        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $app->id,
                'shopCode' => $app->shop_code,
                'shopName' => $app->store_name,
                'ownerName' => $app->owner_name,
                'phone' => $app->owner_mobile,
                'email' => $app->owner_email,
                'category' => $app->store_type,
                'gstNumber' => $app->gst_number,
                'address' => $app->store_address,
                'state' => $app->state,
                'city' => $app->city,
                'country' => $app->country,
                'googleMapUrl' => $app->google_map_url,
                'locationLat' => $app->lat,
                'locationLng' => $app->lng,
                'minBillAmount' => $app->min_bill_amount,
                'razorpayAccountId' => $app->razorpay_account_id,
                'images' => $app->shopImages->map(fn($img) => [
                    'id' => $img->id,
                    'url' => $img->image_url,
                ]),
                'seller' => $app->seller ? [
                    'id' => $app->seller->id,
                    'sellerCode' => $app->seller->seller_code,
                    'username' => $app->seller->username,
                    'ownerName' => $app->seller->owner_name,
                    'email' => $app->seller->email,
                    'phone' => $app->seller->phone,
                    'status' => $app->seller->status,
                ] : null,
                'settings' => [
                    'commissionPercent' => $app->commission_percent,
                    'discountPercent' => $app->discount_percent,
                    'minimumBillAmount' => $app->min_bill_amount,
                    'rating' => $app->rating,
                    'totalRatings' => $app->total_ratings,
                    'isActive' => $app->is_active,
                    'isFeatured' => $app->is_featured,
                    'offerTag' => $app->offer_tag,
                    'lastUpdatedOn' => $app->last_updated_on,
                ],
                'createdAt' => $app->created_at->toIso8601String(),
                'updatedAt' => $app->updated_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * API: Update store settings (commission, discount, rating, etc.)
     * PUT /api/admin/shops/{applicationId}
     */
    public function update(Request $request, $applicationId)
    {
        $app = SellerApplication::where('status', 'APPROVED')->find($applicationId);

        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
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
            // Store basic info
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

        // Build update data
        $updateData = [];
        if ($request->has('shopName')) $updateData['store_name'] = $request->shopName;
        if ($request->has('address')) $updateData['store_address'] = $request->address;
        if ($request->has('state')) $updateData['state'] = $request->state;
        if ($request->has('city')) $updateData['city'] = $request->city;
        if ($request->has('country')) $updateData['country'] = $request->country;
        if ($request->has('googleMapUrl')) $updateData['google_map_url'] = $request->googleMapUrl;
        if ($request->has('commissionPercent')) $updateData['commission_percent'] = $request->commissionPercent;
        if ($request->has('discountPercent')) $updateData['discount_percent'] = $request->discountPercent;
        if ($request->has('rating')) $updateData['rating'] = $request->rating;
        if ($request->has('isActive')) $updateData['is_active'] = $request->isActive;
        if ($request->has('isFeatured')) $updateData['is_featured'] = $request->isFeatured;
        if ($request->has('offerTag')) $updateData['offer_tag'] = $request->offerTag;
        if ($request->has('minimumBillAmount')) $updateData['min_bill_amount'] = $request->minimumBillAmount;

        if (!empty($updateData)) {
            $updateData['last_updated_on'] = now();
            $app->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully'
        ]);
    }

    /**
     * API: Upload store images
     * POST /api/admin/shops/{applicationId}/images
     */
    public function uploadImages(Request $request, $applicationId)
    {
        $app = SellerApplication::where('status', 'APPROVED')->find($applicationId);

        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
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
            $filename = 'store_' . $app->id . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->move(public_path('uploads/shops'), $filename);
            $imageUrl = '/uploads/shops/' . $filename;

            $shopImage = ShopImage::create([
                'seller_application_id' => $app->id,
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
     * DELETE /api/admin/shops/{applicationId}/images/{imageId}
     */
    public function deleteImage($applicationId, $imageId)
    {
        $app = SellerApplication::where('status', 'APPROVED')->find($applicationId);

        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        }

        $image = ShopImage::where('seller_application_id', $applicationId)->where('id', $imageId)->first();

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
        $baseQuery = SellerApplication::where('status', 'APPROVED');

        $categories = (clone $baseQuery)
            ->whereNotNull('store_type')
            ->distinct()
            ->pluck('store_type')
            ->filter()
            ->values();

        $states = (clone $baseQuery)
            ->whereNotNull('state')
            ->distinct()
            ->pluck('state')
            ->filter()
            ->values();

        $cities = (clone $baseQuery)
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->filter()
            ->values();

        $countries = (clone $baseQuery)
            ->whereNotNull('country')
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
