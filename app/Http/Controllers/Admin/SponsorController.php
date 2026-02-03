<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Image;
use File;
use Str;

/**
 * @group Sponsor
 */
class SponsorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api')->except(['apiIndex']);
    }

    /**
     * Display a listing of sponsors (API)
     */
    public function index()
    {
        $sponsors = Sponsor::orderBy('serial', 'asc')->get();
        return response()->json(['sponsors' => $sponsors], 200);
    }

    /**
     * Store a newly created sponsor
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Sponsor,Co-Sponsor,Special Sponsor,Partner',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'link' => 'nullable|url|max:500',
            'serial' => 'nullable|integer|min:0',
        ];

        $customMessages = [
            'name.required' => trans('Name is required'),
            'type.in' => trans('Invalid sponsor type'),
        ];

        $this->validate($request, $rules, $customMessages);

        $sponsor = new Sponsor();
        $sponsor->name = $request->name;
        $sponsor->type = $request->type;
        $sponsor->link = $request->link;
        $sponsor->serial = $request->serial ?? 0;
        $sponsor->status = 1;

        // Handle logo upload with optimization
        if ($request->hasFile('logo')) {
            $sponsor->logo = $this->uploadAndOptimizeImage($request->file('logo'), 'logo', $request->name);
        }

        // Handle banner upload with optimization
        if ($request->hasFile('banner')) {
            $sponsor->banner = $this->uploadAndOptimizeImage($request->file('banner'), 'banner', $request->name);
        }

        $sponsor->save();

        return response()->json([
            'message' => trans('Sponsor created successfully'),
            'sponsor' => $sponsor
        ], 201);
    }

    /**
     * Display the specified sponsor
     */
    public function show($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        return response()->json(['sponsor' => $sponsor], 200);
    }

    /**
     * Update the specified sponsor
     */
    public function update(Request $request, $id)
    {
        $sponsor = Sponsor::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Sponsor,Co-Sponsor,Special Sponsor,Partner',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'link' => 'nullable|url|max:500',
            'serial' => 'nullable|integer|min:0',
        ];

        $this->validate($request, $rules);

        $sponsor->name = $request->name;
        $sponsor->type = $request->type;
        $sponsor->link = $request->link;
        $sponsor->serial = $request->serial ?? $sponsor->serial;

        // Handle logo upload with optimization
        if ($request->hasFile('logo')) {
            $this->deleteImage($sponsor->logo);
            $sponsor->logo = $this->uploadAndOptimizeImage($request->file('logo'), 'logo', $request->name);
        }

        // Handle banner upload with optimization
        if ($request->hasFile('banner')) {
            $this->deleteImage($sponsor->banner);
            $sponsor->banner = $this->uploadAndOptimizeImage($request->file('banner'), 'banner', $request->name);
        }

        $sponsor->save();

        return response()->json([
            'message' => trans('Sponsor updated successfully'),
            'sponsor' => $sponsor
        ], 200);
    }

    /**
     * Remove the specified sponsor
     */
    public function destroy($id)
    {
        $sponsor = Sponsor::findOrFail($id);

        $this->deleteImage($sponsor->logo);
        $this->deleteImage($sponsor->banner);

        $sponsor->delete();

        return response()->json(['message' => trans('Sponsor deleted successfully')], 200);
    }

    /**
     * Change sponsor status
     */
    public function changeStatus($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->status = $sponsor->status == 1 ? 0 : 1;
        $sponsor->save();

        $message = $sponsor->status == 1 ? trans('Sponsor activated') : trans('Sponsor deactivated');
        return response()->json(['message' => $message, 'sponsor' => $sponsor], 200);
    }

    /**
     * Upload and optimize image with compression
     */
    private function uploadAndOptimizeImage($file, $type, $name)
    {
        $path = public_path() . '/uploads/sponsors/';

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($name) . '-' . $type . '-' . date('Y-m-d-h-i-s') . '-' . rand(999, 9999);

        // Optimize to WebP for better compression
        $outputExtension = 'webp';
        $fileName = $fileName . '.' . $outputExtension;
        $relativePath = 'uploads/sponsors/' . $fileName;
        $fullPath = $path . $fileName;

        // Different dimensions and quality for logo vs banner
        if ($type === 'logo') {
            // Logo: smaller size, maintain aspect ratio, max 300x150
            $image = Image::make($file)
                ->resize(300, 150, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85);
        } else {
            // Banner: larger size for cards, max 800x500
            $image = Image::make($file)
                ->resize(800, 500, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80);
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

    /**
     * Public API endpoint for store panel
     */
    public function apiIndex()
    {
        $sponsors = Sponsor::active()->ordered()->get()->map(function ($sponsor) {
            return [
                'id' => $sponsor->id,
                'name' => $sponsor->name,
                'type' => $sponsor->type,
                'logo' => $sponsor->logo ? asset($sponsor->logo) : null,
                'banner' => $sponsor->banner ? asset($sponsor->banner) : null,
                'link' => $sponsor->link,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $sponsors
        ], 200);
    }

    // ==================== VIEW METHODS ====================

    /**
     * Display sponsors list view
     */
    public function indexView()
    {
        $sponsors = Sponsor::orderBy('serial', 'asc')->get();
        return view('admin.sponsor.index', compact('sponsors'));
    }

    /**
     * Display create sponsor form
     */
    public function createView()
    {
        return view('admin.sponsor.create');
    }

    /**
     * Display edit sponsor form
     */
    public function editView($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        return view('admin.sponsor.edit', compact('sponsor'));
    }
}
