<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store\StoreCategory;

/**
 * @group Store Categories
 */
class StoreCategoriesController extends Controller
{
    public function index()
    {
        $categories = StoreCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
            ],
        ]);
    }
}


