<?php

namespace App\Http\Controllers\WEB\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\StoreCategory;
use App\Models\Store\ShopImage;
use App\Repositories\Store\StoreDetailsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class StoreProfileController extends Controller
{
    protected StoreDetailsRepository $storeRepository;

    public function __construct(StoreDetailsRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    public function edit()
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application.shopImages');

        $application = $seller->application;
        $categories = StoreCategory::query()->where('is_active', true)->orderBy('name')->get();

        // All details are fetched directly from the store application
        return view('store.store_profile', [
            'seller' => $seller,
            'shop' => $application,
            'images' => $application?->shopImages ?? collect(),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request)
    {
        $categoryNames = StoreCategory::query()
            ->where('is_active', true)
            ->pluck('name')
            ->all();

        $validator = Validator::make($request->all(), [
            'shop_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255', 'in:'.implode(',', $categoryNames)],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'google_map_url' => ['nullable', 'string', 'max:2048'],
            'location_lat' => ['nullable', 'numeric'],
            'location_lng' => ['nullable', 'numeric'],
            'min_bill_amount' => ['nullable', 'numeric', 'min:0'],
            'images.*' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application) {
            return redirect()->back()->withErrors(['shop' => 'Store application not found for this seller']);
        }

        // Update using repository (single source of truth)
        $this->storeRepository->update($application, $request->only([
            'shop_name', 'category', 'owner_name', 'phone', 'email',
            'gst_number', 'address', 'google_map_url', 'location_lat',
            'location_lng', 'min_bill_amount'
        ]));

        // Optional image upload from same form
        if ($request->hasFile('images')) {
            foreach ((array) $request->file('images') as $file) {
                if (!$file) {
                    continue;
                }
                $dir = public_path("uploads/store/shops/{$application->shop_code}");
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                $fileName = uniqid('shop_', true).'.'.$file->getClientOriginalExtension();
                $file->move($dir, $fileName);

                $relativePath = "uploads/store/shops/{$application->shop_code}/{$fileName}";
                ShopImage::query()->create([
                    'seller_application_id' => $application->id,
                    'image_url' => $relativePath,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Store profile updated successfully');
    }

    public function deleteImage($id)
    {
        $seller = Auth::guard('store')->user();
        $seller->loadMissing('application');
        $application = $seller->application;

        if (!$application) {
            return redirect()->back()->withErrors(['shop' => 'Store application not found for this seller']);
        }

        $img = ShopImage::query()->where('seller_application_id', $application->id)->where('id', $id)->first();
        if ($img) {
            if ($img->image_url && !str_starts_with($img->image_url, 'http')) {
                $fullPath = public_path($img->image_url);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
            $img->delete();
        }

        return redirect()->back()->with('success', 'Image deleted successfully');
    }
}


