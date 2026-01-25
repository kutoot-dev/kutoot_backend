<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImageItem;
use App\Models\ImageType;
use Illuminate\Support\Facades\Storage;

class ImageItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 public function index(Request $request)
{
    $query = ImageItem::with('type')->latest();

    if ($request->has('type') && $request->type != '') {
        $query->where('image_type_id', $request->type);
    }

    $imageItems = $query->get();
    $types = \App\Models\ImageType::all();

    return view('admin.image-items.index', compact('imageItems', 'types'));
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = ImageType::all();
        return view('admin.image-items.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'images' => 'required|array|min:1',
        'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'image_type_id' => 'nullable|exists:image_types,id',
        'new_type' => 'nullable|string|max:255',
    ]);

    // Determine which type ID to use
 if ($request->filled('new_type')) {
    $newTypeName = trim($request->new_type);
    $type = ImageType::firstOrCreate(['name' => $newTypeName]);
      $type->refresh();

    if (!$type || !$type->id) {
        return back()->withErrors(['new_type' => 'Failed to create new type.']);
    }
    $typeId = $type->id;

} elseif ($request->filled('image_type_id')) {
    $typeId = $request->image_type_id;

} else {
    return back()->withErrors(['image_type_id' => 'Please select or enter an image type.']);
}


    // Save each image
    foreach ($request->file('images') as $image) {
        $path = $image->store('uploads/images', 'public');

        ImageItem::create([
            'title' => $request->title,
            'description' => $request->description ?? '',
            'image_type_id' => $typeId,
            'image_path' => $path,
        ]);
    }

    return redirect()->route('admin.image-items.index')->with('success', 'Images uploaded successfully!');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function show($id)
{
    $item = ImageItem::with('type')->findOrFail($id);
    return view('admin.image-items.show', compact('item'));
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
public function edit($id)
{
    $imageItem = ImageItem::findOrFail($id);
    $types = ImageType::all();
    return view('admin.image-items.edit', compact('imageItem', 'types'));
}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'image_type_id' => 'nullable|exists:image_types,id',
        'new_type' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $item = ImageItem::findOrFail($id);

    // Determine the correct type ID
    if ($request->filled('new_type')) {
        $type = ImageType::firstOrCreate(['name' => trim($request->new_type)]);
        $typeId = $type->id;
    } else {
        $typeId = $request->image_type_id;
    }

    // Handle image update
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
            Storage::disk('public')->delete($item->image_path);
        }

        // Store new image
        $path = $request->file('image')->store('uploads/images', 'public');
        $item->image_path = $path;
    }

    // Update other fields
    $item->title = $request->title;
    $item->description = $request->description;
    $item->image_type_id = $typeId;

    $item->save(); // Save everything

    return redirect()->route('admin.image-items.index')->with('success', 'Image updated successfully.');
}



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function destroy($id)
{
    $item = ImageItem::findOrFail($id);
    Storage::disk('public')->delete($item->image_path); // remove image file
    $item->delete();

    return redirect()->route('admin.image-items.index')->with('success', 'Image deleted successfully.');
}



 public function apiIndex()
    {
        $items = ImageItem::all();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    // Return image items filtered by type ID or name (without type relationship)
    public function apiByType(Request $request)
    {
        $typeId = $request->input('type_id');
        $typeName = $request->input('type');

        if ($typeId) {
            $items = ImageItem::where('image_type_id', $typeId)->get();
        } elseif ($typeName) {
            // Use firstOrCreate to auto-create the type if it doesn't exist
            $type = ImageType::firstOrCreate(['name' => $typeName]);
            $items = ImageItem::where('image_type_id', $type->id)->get();
        } else {
            return response()->json(['success' => false, 'message' => 'Type ID or Name is required'], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

}
