<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreBanner;
use App\Helpers\ImageHelper;
use Illuminate\Support\Str;

class StoreBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of banners
     */
    public function index()
    {
        $banners = StoreBanner::orderBy('serial', 'asc')->get();
        return view('admin.store_banner', compact('banners'));
    }

    /**
     * Show the form for creating a new banner
     */
    public function create()
    {
        return view('admin.create_store_banner');
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
            'banner_image.required' => trans('admin_validation.Banner image is required'),
            'banner_image.image' => trans('admin_validation.File must be an image'),
            'banner_image.max' => trans('admin_validation.Image size must be less than 5MB'),
            'title.required' => trans('admin_validation.Title is required'),
            'status.required' => trans('admin_validation.Status is required'),
            'serial.required' => trans('admin_validation.Serial is required'),
            'end_date.after_or_equal' => trans('admin_validation.End date must be after or equal to start date'),
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

        $notification = trans('admin_validation.Created Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.store-banner.index')->with($notification);
    }

    /**
     * Display the specified banner
     */
    public function show($id)
    {
        $banner = StoreBanner::find($id);

        if (!$banner) {
            $notification = trans('admin_validation.Banner not found');
            $notification = array('messege' => $notification, 'alert-type' => 'error');
            return redirect()->back()->with($notification);
        }

        return view('admin.show_store_banner', compact('banner'));
    }

    /**
     * Show the form for editing the specified banner
     */
    public function edit($id)
    {
        $banner = StoreBanner::find($id);

        if (!$banner) {
            $notification = trans('admin_validation.Banner not found');
            $notification = array('messege' => $notification, 'alert-type' => 'error');
            return redirect()->back()->with($notification);
        }

        return view('admin.edit_store_banner', compact('banner'));
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
            'banner_image.image' => trans('admin_validation.File must be an image'),
            'banner_image.max' => trans('admin_validation.Image size must be less than 5MB'),
            'title.required' => trans('admin_validation.Title is required'),
            'status.required' => trans('admin_validation.Status is required'),
            'serial.required' => trans('admin_validation.Serial is required'),
            'end_date.after_or_equal' => trans('admin_validation.End date must be after or equal to start date'),
        ];

        $this->validate($request, $rules, $customMessages);

        $banner = StoreBanner::find($id);

        if (!$banner) {
            $notification = trans('admin_validation.Banner not found');
            $notification = array('messege' => $notification, 'alert-type' => 'error');
            return redirect()->back()->with($notification);
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

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.store-banner.index')->with($notification);
    }

    /**
     * Remove the specified banner
     */
    public function destroy($id)
    {
        $banner = StoreBanner::find($id);

        if (!$banner) {
            $notification = trans('admin_validation.Banner not found');
            $notification = array('messege' => $notification, 'alert-type' => 'error');
            return redirect()->back()->with($notification);
        }

        // Delete associated images
        $banner->deleteImages();
        $banner->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->back()->with($notification);
    }

    /**
     * Toggle banner status
     */
    public function changeStatus($id)
    {
        $banner = StoreBanner::find($id);

        if (!$banner) {
            return response()->json(['error' => trans('admin_validation.Banner not found')], 404);
        }

        if ($banner->status == 1) {
            $banner->status = 0;
            $banner->save();
            $message = trans('admin_validation.Inactive Successfully');
        } else {
            $banner->status = 1;
            $banner->save();
            $message = trans('admin_validation.Active Successfully');
        }

        return response()->json($message);
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

        $notification = trans('admin_validation.Order Updated Successfully');
        return response()->json(['notification' => $notification], 200);
    }
}
