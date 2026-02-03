<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreBanner;
use App\Helpers\ImageHelper;
use Illuminate\Support\Str;

/**
 * @group Store Banner
 */
class StoreBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api')->except(['apiIndex']);
    }

    /**
     * Display a listing of banners
     */
    public function index()
    {
        $banners = StoreBanner::orderBy('serial', 'asc')->get();
        return response()->json(['banners' => $banners], 200);
    }

    /**
     * Store a newly created banner
     */
    public function store(Request $request)
    {
        $rules = [
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'title' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'serial' => 'required|integer',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        $customMessages = [
            'banner_image.required' => trans('Banner image is required'),
            'banner_image.image' => trans('File must be an image'),
            'banner_image.max' => trans('Image size must be less than 5MB'),
            'title.required' => trans('Title is required'),
            'status.required' => trans('Status is required'),
            'serial.required' => trans('Serial is required'),
            'end_date.after_or_equal' => trans('End date must be after or equal to start date'),
        ];

        $this->validate($request, $rules, $customMessages);

        $banner = new StoreBanner();

        // Handle responsive image upload
        if ($request->hasFile('banner_image')) {
            $images = ImageHelper::uploadResponsive(
                $request->file('banner_image'),
                'store-banners',
                'banner-' . Str::slug($request->title),
                'banner',
                80,
                null,
                true
            );

            $banner->image = $images['desktop'];
            $banner->image_tablet = $images['tablet'];
            $banner->image_mobile = $images['mobile'];
        }

        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->link = $request->link;
        $banner->button_text = $request->button_text;
        $banner->location = $request->location;
        $banner->serial = $request->serial;
        $banner->status = $request->status;
        $banner->start_date = $request->start_date;
        $banner->end_date = $request->end_date;
        $banner->save();

        $notification = trans('Created Successfully');
        return response()->json(['notification' => $notification, 'banner' => $banner], 200);
    }

    /**
     * Display the specified banner
     */
    public function show($id)
    {
        $banner = StoreBanner::find($id);

        if (!$banner) {
            return response()->json(['error' => trans('Banner not found')], 404);
        }

        return response()->json(['banner' => $banner], 200);
    }

    /**
     * Update the specified banner
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'title' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'serial' => 'required|integer',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        $customMessages = [
            'banner_image.image' => trans('File must be an image'),
            'banner_image.max' => trans('Image size must be less than 5MB'),
            'title.required' => trans('Title is required'),
            'status.required' => trans('Status is required'),
            'serial.required' => trans('Serial is required'),
            'end_date.after_or_equal' => trans('End date must be after or equal to start date'),
        ];

        $this->validate($request, $rules, $customMessages);

        $banner = StoreBanner::find($id);

        if (!$banner) {
            return response()->json(['error' => trans('Banner not found')], 404);
        }

        // Handle responsive image upload
        if ($request->hasFile('banner_image')) {
            $oldImages = $banner->getOldImagePaths();

            $images = ImageHelper::uploadResponsive(
                $request->file('banner_image'),
                'store-banners',
                'banner-' . Str::slug($request->title),
                'banner',
                80,
                array_values($oldImages),
                true
            );

            $banner->image = $images['desktop'];
            $banner->image_tablet = $images['tablet'];
            $banner->image_mobile = $images['mobile'];
        }

        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->link = $request->link;
        $banner->button_text = $request->button_text;
        $banner->location = $request->location;
        $banner->serial = $request->serial;
        $banner->status = $request->status;
        $banner->start_date = $request->start_date;
        $banner->end_date = $request->end_date;
        $banner->save();

        $notification = trans('Update Successfully');
        return response()->json(['notification' => $notification, 'banner' => $banner], 200);
    }

    /**
     * Remove the specified banner
     */
    public function destroy($id)
    {
        $banner = StoreBanner::find($id);

        if (!$banner) {
            return response()->json(['error' => trans('Banner not found')], 404);
        }

        // Delete associated images
        $banner->deleteImages();
        $banner->delete();

        $notification = trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    /**
     * Toggle banner status
     */
    public function changeStatus($id)
    {
        $banner = StoreBanner::find($id);

        if (!$banner) {
            return response()->json(['error' => trans('Banner not found')], 404);
        }

        if ($banner->status == 1) {
            $banner->status = 0;
            $banner->save();
            $message = trans('Inactive Successfully');
        } else {
            $banner->status = 1;
            $banner->save();
            $message = trans('Active Successfully');
        }

        return response()->json(['message' => $message, 'status' => $banner->status]);
    }

    /**
     * Get banners for a specific location (public API)
     */
    public function getByLocation($location)
    {
        $banners = StoreBanner::active()
            ->valid()
            ->location($location)
            ->orderBy('serial', 'asc')
            ->get();

        return response()->json(['banners' => $banners], 200);
    }

    /**
     * Public API: Get all active store banners with optional location filter
     */
    public function apiIndex(Request $request)
    {
        $query = StoreBanner::active()->valid()->orderBy('serial', 'asc');

        // Filter by location if provided
        if ($request->has('location') && !empty($request->location)) {
            $query->location($request->location);
        }

        $banners = $query->get()->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'description' => $banner->description,
                'image' => $banner->image ? asset($banner->image) : null,
                'image_tablet' => $banner->image_tablet ? asset($banner->image_tablet) : null,
                'image_mobile' => $banner->image_mobile ? asset($banner->image_mobile) : null,
                'link' => $banner->link,
                'button_text' => $banner->button_text,
                'location' => $banner->location,
                'serial' => $banner->serial,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $banners
        ], 200);
    }

    /**
     * Get all active banners (public API)
     */
    public function getActiveBanners()
    {
        $banners = StoreBanner::active()
            ->valid()
            ->orderBy('serial', 'asc')
            ->get();

        return response()->json(['banners' => $banners], 200);
    }

    /**
     * Update banner order (serial)
     */
    public function updateOrder(Request $request)
    {
        $rules = [
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:store_banners,id',
            'banners.*.serial' => 'required|integer',
        ];

        $this->validate($request, $rules);

        foreach ($request->banners as $bannerData) {
            StoreBanner::where('id', $bannerData['id'])
                ->update(['serial' => $bannerData['serial']]);
        }

        $notification = trans('Order Updated Successfully');
        return response()->json(['notification' => $notification], 200);
    }
}
