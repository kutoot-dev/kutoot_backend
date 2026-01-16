<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store\AdminShopCommissionDiscount;
use App\Models\Store\Seller;
use App\Models\Store\Shop;
use App\Models\Store\ShopImage;
use App\Models\Store\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class StoreProfileController extends Controller
{
    public function show()
    {
        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('shop.images');

        if (!$seller->shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found for this seller',
            ], 404);
        }

        $master = AdminShopCommissionDiscount::resolveForShop($seller->shop?->id);

        $images = $seller->shop->images->map(function ($img) {
            $v = (string) $img->image_url;
            if ($v === '') {
                return null;
            }
            return str_starts_with($v, 'http') ? $v : asset($v);
        })->filter()->values();

        return response()->json([
            'success' => true,
            'data' => [
                'shopId' => $seller->shop->shop_code,
                'shopName' => $seller->shop->shop_name,
                'category' => $seller->shop->category,
                'ownerName' => $seller->shop->owner_name,
                'phone' => $seller->shop->phone,
                'email' => $seller->shop->email,
                'gstNumber' => $seller->shop->gst_number,
                'address' => $seller->shop->address,
                'googleMapUrl' => $seller->shop->google_map_url,
                'location' => [
                    'lat' => $seller->shop->location_lat,
                    'lng' => $seller->shop->location_lng,
                ],
                'masterAdmin' => [
                    'discountPercent' => $master?->discount_percent ?? 0,
                    'commissionPercent' => $master?->commission_percent ?? 0,
                    'minimumBillAmount' => (float) ($master?->minimum_bill_amount ?? 0),
                ],
                'images' => $images,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $categoryNames = StoreCategory::query()
            ->where('is_active', true)
            ->pluck('name')
            ->all();

        $validator = Validator::make($request->all(), [
            'shopName' => ['sometimes', 'string', 'max:255'],
            'category' => array_values(array_filter([
                'sometimes',
                'nullable',
                'string',
                'max:255',
                $categoryNames ? ('in:'.implode(',', $categoryNames)) : null,
            ])),
            'ownerName' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'gstNumber' => ['sometimes', 'nullable', 'string', 'max:50'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'googleMapUrl' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'location.lat' => ['sometimes', 'nullable', 'numeric'],
            'location.lng' => ['sometimes', 'nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('shop');

        /** @var Shop|null $shop */
        $shop = $seller->shop;
        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found for this seller',
            ], 404);
        }

        $shop->fill([
            'shop_name' => $request->input('shopName', $shop->shop_name),
            'category' => $request->input('category', $shop->category),
            'owner_name' => $request->input('ownerName', $shop->owner_name),
            'phone' => $request->input('phone', $shop->phone),
            'email' => $request->input('email', $shop->email),
            'gst_number' => $request->input('gstNumber', $shop->gst_number),
            'address' => $request->input('address', $shop->address),
            'google_map_url' => $request->input('googleMapUrl', $shop->google_map_url),
            'location_lat' => data_get($request->input('location'), 'lat', $shop->location_lat),
            'location_lng' => data_get($request->input('location'), 'lng', $shop->location_lng),
        ]);
        $shop->save();

        return response()->json([
            'success' => true,
            'message' => 'Store profile updated successfully',
        ]);
    }

    public function uploadImages(Request $request)
    {
        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('shop');
        $shop = $seller->shop;

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found for this seller',
            ], 404);
        }

        $files = $request->file('images') ?? $request->file('image');
        if (!$files) {
            return response()->json([
                'success' => false,
                'message' => 'No images provided',
            ], 422);
        }

        $files = is_array($files) ? $files : [$files];
        $uploaded = [];

        foreach ($files as $file) {
            $dir = public_path("uploads/store/shops/{$shop->shop_code}");
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $fileName = uniqid('shop_', true).'.'.$file->getClientOriginalExtension();
            $file->move($dir, $fileName);

            $relativePath = "uploads/store/shops/{$shop->shop_code}/{$fileName}";
            $url = asset($relativePath);

            ShopImage::query()->create([
                'shop_id' => $shop->id,
                'image_url' => $relativePath,
            ]);

            $uploaded[] = $url;
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded',
            'data' => [
                'uploaded' => $uploaded,
            ],
        ]);
    }

    public function deleteImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'imageUrl' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('shop');
        $shop = $seller->shop;

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found for this seller',
            ], 404);
        }

        $imageUrl = $request->input('imageUrl');

        // Accept either stored relative path (uploads/...) or full URL
        $needle = $imageUrl;
        if (str_contains($needle, '/uploads/')) {
            $needle = ltrim(substr($needle, strpos($needle, 'uploads/')), '/');
        }

        $img = ShopImage::query()
            ->where('shop_id', $shop->id)
            ->where(function ($q) use ($imageUrl, $needle) {
                $q->where('image_url', $imageUrl)->orWhere('image_url', $needle);
            })
            ->first();

        if ($img) {
            if ($img->image_url && !str_starts_with($img->image_url, 'http')) {
                $fullPath = public_path($img->image_url);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }

            $img->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
        ]);
    }
}


