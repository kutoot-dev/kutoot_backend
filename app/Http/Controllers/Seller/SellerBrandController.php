<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Auth;
use Image;
use File;

class SellerBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $seller = Auth::guard('api')->user()->seller;
        $brands = Brand::where('seller_id', $seller->id)
                        ->orderBy('id', 'desc')
                        ->get();

        return response()->json(['brands' => $brands], 200);
    }

    public function create()
    {
        return response()->json(['message' => 'Create brand form data'], 200);
    }

    public function store(Request $request)
    {
        $seller = Auth::guard('api')->user()->seller;

        $rules = [
            'name' => 'required|unique:brands,name',
            'slug' => 'required|unique:brands,slug',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $customMessages = [
            'name.required' => trans('Brand name is required'),
            'name.unique' => trans('Brand name already exists'),
            'slug.required' => trans('Brand slug is required'),
            'slug.unique' => trans('Brand slug already exists'),
            'logo.required' => trans('Brand logo is required'),
            'logo.image' => trans('Logo must be an image'),
            'logo.mimes' => trans('Logo must be jpeg, png, jpg or gif'),
            'logo.max' => trans('Logo size must not exceed 2MB'),
        ];

        $this->validate($request, $rules, $customMessages);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->seller_id = $seller->id;
        $brand->status = $request->status ?? 1;

        if ($request->logo) {
            $upload_path = public_path('uploads/brands');
            if (!File::isDirectory($upload_path)) {
                File::makeDirectory($upload_path, 0755, true, true);
            }

            $extension = $request->logo->getClientOriginalExtension();
            $logo_name = 'seller-brand-' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.' . $extension;
            $logo_name = 'uploads/brands/' . $logo_name;
            Image::make($request->logo)->save(public_path() . '/' . $logo_name);
            $brand->logo = $logo_name;
        }

        $brand->save();

        $notification = trans('Brand created successfully');
        return response()->json(['message' => $notification, 'brand' => $brand], 201);
    }

    public function show($id)
    {
        $seller = Auth::guard('api')->user()->seller;
        $brand = Brand::find($id);

        if (!$brand || $brand->seller_id != $seller->id) {
            $notification = trans('Brand not found');
            return response()->json(['notification' => $notification], 404);
        }

        return response()->json(['brand' => $brand], 200);
    }

    public function edit($id)
    {
        $seller = Auth::guard('api')->user()->seller;
        $brand = Brand::find($id);

        if (!$brand || $brand->seller_id != $seller->id) {
            $notification = trans('Brand not found');
            return response()->json(['notification' => $notification], 404);
        }

        return response()->json(['brand' => $brand], 200);
    }

    public function update(Request $request, $id)
    {
        $seller = Auth::guard('api')->user()->seller;
        $brand = Brand::find($id);

        if (!$brand || $brand->seller_id != $seller->id) {
            $notification = trans('Brand not found');
            return response()->json(['notification' => $notification], 404);
        }

        $rules = [
            'name' => 'required|unique:brands,name,' . $brand->id,
            'slug' => 'required|unique:brands,slug,' . $brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $customMessages = [
            'name.required' => trans('Brand name is required'),
            'name.unique' => trans('Brand name already exists'),
            'slug.required' => trans('Brand slug is required'),
            'slug.unique' => trans('Brand slug already exists'),
            'logo.image' => trans('Logo must be an image'),
            'logo.mimes' => trans('Logo must be jpeg, png, jpg or gif'),
            'logo.max' => trans('Logo size must not exceed 2MB'),
        ];

        $this->validate($request, $rules, $customMessages);

        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status ?? $brand->status;

        if ($request->logo) {
            $upload_path = public_path('uploads/brands');
            if (!File::isDirectory($upload_path)) {
                File::makeDirectory($upload_path, 0755, true, true);
            }

            $exist_logo = $brand->logo;
            $extension = $request->logo->getClientOriginalExtension();
            $logo_name = 'seller-brand-' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.' . $extension;
            $logo_name = 'uploads/brands/' . $logo_name;
            Image::make($request->logo)->save(public_path() . '/' . $logo_name);
            $brand->logo = $logo_name;

            if ($exist_logo && File::exists(public_path() . '/' . $exist_logo)) {
                unlink(public_path() . '/' . $exist_logo);
            }
        }

        $brand->save();

        $notification = trans('Brand updated successfully');
        return response()->json(['message' => $notification, 'brand' => $brand], 200);
    }

    public function destroy($id)
    {
        $seller = Auth::guard('api')->user()->seller;
        $brand = Brand::find($id);

        if (!$brand || $brand->seller_id != $seller->id) {
            $notification = trans('Brand not found');
            return response()->json(['notification' => $notification], 404);
        }

        $logo = $brand->logo;
        $brand->delete();

        if ($logo && File::exists(public_path() . '/' . $logo)) {
            unlink(public_path() . '/' . $logo);
        }

        $notification = trans('Brand deleted successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function changeStatus($id)
    {
        $seller = Auth::guard('api')->user()->seller;
        $brand = Brand::find($id);

        if (!$brand || $brand->seller_id != $seller->id) {
            $notification = trans('Brand not found');
            return response()->json(['notification' => $notification], 404);
        }

        if ($brand->status == 1) {
            $brand->status = 0;
        } else {
            $brand->status = 1;
        }

        $brand->save();

        $notification = trans('Brand status changed successfully');
        return response()->json(['message' => $notification, 'brand' => $brand], 200);
    }
}
