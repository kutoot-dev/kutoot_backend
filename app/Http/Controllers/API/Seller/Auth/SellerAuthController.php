<?php

namespace App\Http\Controllers\API\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store\Seller;
use App\Models\Store\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SellerAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $credentials = $request->only('username', 'password');

        if (! $token = Auth::guard('store-api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('shop');

        $categories = StoreCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'categories' => $categories,
                'seller' => [
                    'sellerId' => $seller->seller_code,
                    'shopId' => $seller->shop?->shop_code,
                    'shopName' => $seller->shop?->shop_name,
                    'ownerName' => $seller->owner_name,
                    'email' => $seller->email,
                    'phone' => $seller->phone,
                    'status' => $seller->status ? 'ACTIVE' : 'INACTIVE',
                ],
            ],
        ]);
    }

    public function logout()
    {
        Auth::guard('store-api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function me()
    {
        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $seller->loadMissing('shop');

        return response()->json([
            'success' => true,
            'data' => [
                'sellerId' => $seller->seller_code,
                'shopId' => $seller->shop?->shop_code,
                'shopName' => $seller->shop?->shop_name,
                'ownerName' => $seller->owner_name,
                'email' => $seller->email,
                'phone' => $seller->phone,
            ],
        ]);
    }
}


