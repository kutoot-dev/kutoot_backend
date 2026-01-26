<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store\Seller;
use App\Models\Store\SellerApplication;
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
        $seller->loadMissing('application.shopImages');

        /** @var SellerApplication|null $application */
        $application = $seller->application;

        if (!$application || $application->status !== SellerApplication::STATUS_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found for this seller',
            ], 404);
        }

        $images = $application->shopImages->map(function ($img) {
            $v = (string) $img->image_url;
            if ($v === '') {
                return null;
            }
            return str_starts_with($v, 'http') ? $v : asset($v);
        })->filter()->values();

        return response()->json([
            'success' => true,
            'data' => [
                'shopId' => $application->shop_code,
                'shopName' => $application->store_name,
                'category' => $application->store_type,
                'ownerName' => $application->owner_name,
                'phone' => $application->owner_mobile,
                'email' => $application->owner_email,
                'gstNumber' => $application->gst_number,
                'address' => $application->store_address,
                'googleMapUrl' => $application->google_map_url,
                'location' => [
                    'lat' => $application->lat,
                    'lng' => $application->lng,
                ],
                'masterAdmin' => [
                    'discountPercent' => $application->discount_percent ?? 0,
                    'commissionPercent' => $application->commission_percent ?? 0,
                    'minimumBillAmount' => (float) ($application->min_bill_amount ?? 0),
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
        $seller->loadMissing('application');

        /** @var SellerApplication|null $application */
        $application = $seller->application;
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found for this seller',
            ], 404);
        }

        // Map request fields directly to application columns
        $updateData = [];

        if ($request->has('shopName')) {
            $updateData['store_name'] = $request->input('shopName');
        }
        if ($request->has('category')) {
            $updateData['store_type'] = $request->input('category');
        }
        if ($request->has('ownerName')) {
            $updateData['owner_name'] = $request->input('ownerName');
        }
        if ($request->has('phone')) {
            $updateData['owner_mobile'] = $request->input('phone');
        }
        if ($request->has('email')) {
            $updateData['owner_email'] = $request->input('email');
        }
        if ($request->has('gstNumber')) {
            $updateData['gst_number'] = $request->input('gstNumber');
        }
        if ($request->has('address')) {
            $updateData['store_address'] = $request->input('address');
        }
        if ($request->has('googleMapUrl')) {
            $updateData['google_map_url'] = $request->input('googleMapUrl');
        }
        if ($request->has('location.lat')) {
            $updateData['lat'] = $request->input('location.lat');
        }
        if ($request->has('location.lng')) {
            $updateData['lng'] = $request->input('location.lng');
        }

        // Update application directly
        $application->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Store profile updated successfully',
        ]);
    }

    public function uploadImages(Request $request)
    {
        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found for this seller',
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

        $shopCode = $application->shop_code ?? $application->application_id;

        foreach ($files as $file) {
            $dir = public_path("uploads/store/shops/{$shopCode}");
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $fileName = uniqid('shop_', true).'.'.$file->getClientOriginalExtension();
            $file->move($dir, $fileName);

            $relativePath = "uploads/store/shops/{$shopCode}/{$fileName}";
            $url = asset($relativePath);

            ShopImage::query()->create([
                'seller_application_id' => $application->id,
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
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found for this seller',
            ], 404);
        }

        $imageUrl = $request->input('imageUrl');

        // Accept either stored relative path (uploads/...) or full URL
        $needle = $imageUrl;
        if (str_contains($needle, '/uploads/')) {
            $needle = ltrim(substr($needle, strpos($needle, 'uploads/')), '/');
        }

        $img = ShopImage::query()
            ->where('seller_application_id', $application->id)
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


