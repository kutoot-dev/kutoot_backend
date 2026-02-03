<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store\StoreCategory;
use Illuminate\Http\Request;
use Image;
use File;
use Str;

/**
 * @group Store Category
 */
class StoreCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of store categories
     */
    public function index()
    {
        $categories = StoreCategory::orderBy('serial', 'asc')->orderBy('name', 'asc')->get();
        return view('admin.store_category', compact('categories'));
    }

    /**
     * Show the form for creating a new store category
     */
    public function create()
    {
        return view('admin.create_store_category');
    }

    /**
     * Store a newly created store category
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:store_categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'serial' => 'nullable|integer|min:0',
            'is_active' => 'required|in:0,1',
        ];

        $customMessages = [
            'name.required' => trans('admin.Name is required'),
            'name.unique' => trans('admin.Category name already exists'),
        ];

        $this->validate($request, $rules, $customMessages);

        $category = new StoreCategory();
        $category->name = $request->name;
        $category->serial = $request->serial ?? 0;
        $category->is_active = $request->is_active;

        // Handle image upload with optimization
        if ($request->hasFile('image')) {
            $category->image = $this->uploadAndOptimizeImage($request->file('image'), 'image', $request->name);
        }

        // Handle icon upload with optimization
        if ($request->hasFile('icon')) {
            $category->icon = $this->uploadAndOptimizeImage($request->file('icon'), 'icon', $request->name);
        }

        $category->save();

        $notification = trans('admin.Created Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.store-category.index')->with($notification);
    }

    /**
     * Show the form for editing the specified store category
     */
    public function edit($id)
    {
        $category = StoreCategory::findOrFail($id);
        return view('admin.edit_store_category', compact('category'));
    }

    /**
     * Update the specified store category
     */
    public function update(Request $request, $id)
    {
        $category = StoreCategory::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255|unique:store_categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'serial' => 'nullable|integer|min:0',
            'is_active' => 'required|in:0,1',
        ];

        $this->validate($request, $rules);

        $category->name = $request->name;
        $category->serial = $request->serial ?? $category->serial;
        $category->is_active = $request->is_active;

        // Handle image upload with optimization
        if ($request->hasFile('image')) {
            $this->deleteImage($category->image);
            $category->image = $this->uploadAndOptimizeImage($request->file('image'), 'image', $request->name);
        }

        // Handle icon upload with optimization
        if ($request->hasFile('icon')) {
            $this->deleteImage($category->icon);
            $category->icon = $this->uploadAndOptimizeImage($request->file('icon'), 'icon', $request->name);
        }

        $category->save();

        $notification = trans('admin.Updated Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.store-category.index')->with($notification);
    }

    /**
     * Remove the specified store category
     */
    public function destroy($id)
    {
        $category = StoreCategory::findOrFail($id);

        $this->deleteImage($category->image);
        $this->deleteImage($category->icon);

        $category->delete();

        $notification = trans('admin.Deleted Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.store-category.index')->with($notification);
    }

    /**
     * Change store category status
     */
    public function changeStatus($id)
    {
        $category = StoreCategory::findOrFail($id);
        $category->is_active = $category->is_active ? false : true;
        $category->save();

        $message = $category->is_active ? trans('admin.Active Successfully') : trans('admin.Inactive Successfully');
        return response()->json($message);
    }

    /**
     * Upload and optimize image with compression
     */
    private function uploadAndOptimizeImage($file, $type, $name)
    {
        $path = public_path() . '/uploads/store-categories/';

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($name) . '-' . $type . '-' . date('Y-m-d-h-i-s') . '-' . rand(999, 9999);

        // Optimize to WebP for better compression
        $outputExtension = 'webp';
        $fileName = $fileName . '.' . $outputExtension;
        $relativePath = 'uploads/store-categories/' . $fileName;
        $fullPath = $path . $fileName;

        // Different dimensions for icon vs image
        if ($type === 'icon') {
            // Icon: smaller size, max 128x128
            $image = Image::make($file)
                ->resize(128, 128, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 90);
        } else {
            // Image: category banner, max 600x400
            $image = Image::make($file)
                ->resize(600, 400, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85);
        }

        $image->save($fullPath);

        return $relativePath;
    }

    /**
     * Delete image from storage
     */
    private function deleteImage($imagePath)
    {
        if ($imagePath && File::exists(public_path() . '/' . $imagePath)) {
            unlink(public_path() . '/' . $imagePath);
        }
    }
}
