<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store\SellerApplication;
use App\Models\Store\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @group Tag
 */
class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    /**
     * API: Get list of all tags
     * GET /api/admin/tags
     */
    public function index()
    {
        $tags = Tag::orderBy('name')->get()->map(fn($tag) => [
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
        ]);

        return response()->json([
            'success' => true,
            'data' => $tags
        ]);
    }

    /**
     * API: Create a new tag
     * POST /api/admin/tags
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $tag = Tag::create([
            'name' => trim($request->name),
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tag created successfully',
            'data' => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]
        ], 201);
    }

    /**
     * API: Delete a tag
     * DELETE /api/admin/tags/{tagId}
     */
    public function destroy($tagId)
    {
        $tag = Tag::find($tagId);

        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found'
            ], 404);
        }

        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully'
        ]);
    }
}
