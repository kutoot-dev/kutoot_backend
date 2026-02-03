<?php

namespace App\Http\Controllers\WEB\Admin;

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
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of sponsors
     */
    public function index()
    {
        $sponsors = Sponsor::orderBy('serial', 'asc')->get();
        return view('admin.sponsor', compact('sponsors'));
    }

    /**
     * Show the form for creating a new sponsor
     */
    public function create()
    {
        return view('admin.create_sponsor');
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
            'status' => 'required|in:0,1',
        ];

        $customMessages = [
            'name.required' => trans('admin.Name is required'),
            'type.in' => trans('admin.Invalid sponsor type'),
        ];

        $this->validate($request, $rules, $customMessages);

        $sponsor = new Sponsor();
        $sponsor->name = $request->name;
        $sponsor->type = $request->type;
        $sponsor->link = $request->link;
        $sponsor->serial = $request->serial ?? 0;
        $sponsor->status = $request->status;

        // Handle logo upload with optimization
        if ($request->hasFile('logo')) {
            $sponsor->logo = $this->uploadAndOptimizeImage($request->file('logo'), 'logo', $request->name);
        }

        // Handle banner upload with optimization
        if ($request->hasFile('banner')) {
            $sponsor->banner = $this->uploadAndOptimizeImage($request->file('banner'), 'banner', $request->name);
        }

        $sponsor->save();

        $notification = trans('admin.Created Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.sponsor.index')->with($notification);
    }

    /**
     * Show the form for editing the specified sponsor
     */
    public function edit($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        return view('admin.edit_sponsor', compact('sponsor'));
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
            'status' => 'required|in:0,1',
        ];

        $this->validate($request, $rules);

        $sponsor->name = $request->name;
        $sponsor->type = $request->type;
        $sponsor->link = $request->link;
        $sponsor->serial = $request->serial ?? $sponsor->serial;
        $sponsor->status = $request->status;

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

        $notification = trans('admin.Updated Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.sponsor.index')->with($notification);
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

        $notification = trans('admin.Deleted Successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->route('admin.sponsor.index')->with($notification);
    }

    /**
     * Change sponsor status
     */
    public function changeStatus($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->status = $sponsor->status == 1 ? 0 : 1;
        $sponsor->save();

        $message = $sponsor->status == 1 ? trans('admin.Active Successfully') : trans('admin.Inactive Successfully');
        return response()->json($message);
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
}
