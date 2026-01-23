<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store\StoreCategory;
use Illuminate\Http\Request;
use Image;
use File;
use Str;

class StoreCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api')->except(['apiIndex', 'apiShow']);
    }

    /**
     * Display a listing of store categories (Admin)
     */
    public function index()
    {
        $categories = StoreCategory::orderBy('serial', 'asc')->orderBy('name', 'asc')->get();
        return response()->json(['categories' => $categories], 200);
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
        ];

        $customMessages = [
            'name.required' => trans('admin.Name is required'),
            'name.unique' => trans('admin.Category name already exists'),
        ];

        $this->validate($request, $rules, $customMessages);

        $category = new StoreCategory();
        $category->name = $request->name;
        $category->serial = $request->serial ?? 0;
        $category->is_active = true;

        // Handle image upload with optimization
        if ($request->hasFile('image')) {
            $category->image = $this->uploadAndOptimizeImage($request->file('image'), 'image', $request->name);
        }

        // Handle icon upload with optimization
        if ($request->hasFile('icon')) {
            $category->icon = $this->uploadAndOptimizeImage($request->file('icon'), 'icon', $request->name);
        }

        $category->save();

        return response()->json([
            'message' => trans('admin.Created Successfully'),
            'category' => $category
        ], 201);
    }

    /**
     * Display the specified store category
     */
    public function show($id)
    {
        $category = StoreCategory::findOrFail($id);
        return response()->json(['category' => $category], 200);
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
        ];

        $this->validate($request, $rules);

        $category->name = $request->name;
        $category->serial = $request->serial ?? $category->serial;

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

        return response()->json([
            'message' => trans('admin.Updated Successfully'),
            'category' => $category
        ], 200);
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

        return response()->json(['message' => trans('admin.Deleted Successfully')], 200);
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
        return response()->json(['message' => $message, 'category' => $category], 200);
    }

    /**
     * Public API: Get all active store categories with search
     */
    public function apiIndex(Request $request)
    {
        $query = StoreCategory::active()->ordered();

        // Search by category name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $categories = $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'image' => $category->image ? asset($category->image) : null,
                'icon' => $category->icon ? asset($category->icon) : null,
                'serial' => $category->serial,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    /**
     * Public API: Get single store category with stores
     */
    public function apiShow($id)
    {
        $category = StoreCategory::with('stores')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'image' => $category->image ? asset($category->image) : null,
                'icon' => $category->icon ? asset($category->icon) : null,
                'stores_count' => $category->stores->count(),
            ]
        ], 200);
    }

    /**
     * Public API: Get stores by category with filters
     */
    public function apiStoresByCategory(Request $request, $categoryId)
    {
        $category = StoreCategory::active()->findOrFail($categoryId);

        // Get shops that match the category name
        $query = \App\Models\Store\Shop::with('images')->where('category', $category->name);

        // Filter by city
        if ($request->has('city') && !empty($request->city)) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filter by state
        if ($request->has('state') && !empty($request->state)) {
            $query->where('state', 'like', '%' . $request->state . '%');
        }

        // Filter by country
        if ($request->has('country') && !empty($request->country)) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        // Filter by tags (comma-separated or array)
        if ($request->has('tags') && !empty($request->tags)) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $tags = array_map('trim', $tags);

            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        // Search by shop name, address, or owner name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shop_name', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('owner_name', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $stores = $query->paginate($perPage);

        $data = $stores->getCollection()->map(function ($shop) {
            $images = $shop->images->map(function ($img) {
                $v = (string) $img->image_url;
                if ($v === '') {
                    return null;
                }
                return str_starts_with($v, 'http') ? $v : asset($v);
            })->filter()->values();

            return [
                'id' => $shop->id,
                'shop_code' => $shop->shop_code,
                'shop_name' => $shop->shop_name,
                'owner_name' => $shop->owner_name,
                'phone' => $shop->phone,
                'email' => $shop->email,
                'address' => $shop->address,
                'city' => $shop->city,
                'state' => $shop->state,
                'country' => $shop->country,
                'tags' => $shop->tags ?? [],
                'location' => [
                    'lat' => $shop->location_lat,
                    'lng' => $shop->location_lng,
                ],
                'google_map_url' => $shop->google_map_url,
                'images' => $images,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image ? asset($category->image) : null,
                    'icon' => $category->icon ? asset($category->icon) : null,
                ],
                'stores' => $data,
                'pagination' => [
                    'total' => $stores->total(),
                    'per_page' => $stores->perPage(),
                    'current_page' => $stores->currentPage(),
                    'last_page' => $stores->lastPage(),
                    'from' => $stores->firstItem(),
                    'to' => $stores->lastItem(),
                ],
            ],
        ], 200);
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
