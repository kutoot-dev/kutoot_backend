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

        // Check if seller has approved store application
        if (!$seller->hasApprovedApplication()) {
            Auth::guard('store-api')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Your store application is not approved yet. Please wait for approval.',
            ], 403);
        }

        $seller->loadMissing('application');

        $categories = StoreCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->all();

        // Get bank details
        $bank = \App\Models\Store\SellerBankAccount::query()->where('seller_id', $seller->id)->first();
        $maskedAccountNumber = null;
        if ($bank?->account_number) {
            $last4 = substr($bank->account_number, -4);
            $maskedAccountNumber = 'XXXXXX' . $last4;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'categories' => $categories,
                'seller' => [
                    'sellerId' => $seller->seller_code,
                    'shopId' => $seller->application?->shop_code,
                    'shopName' => $seller->application?->store_name,
                    'ownerName' => $seller->owner_name,
                    'email' => $seller->email,
                    'phone' => $seller->phone,
                    'status' => $seller->status ? 'ACTIVE' : 'INACTIVE',
                ],
                'bankDetails' => [
                    'bankName' => $bank?->bank_name,
                    'accountNumber' => $maskedAccountNumber,
                    'ifsc' => $bank?->ifsc,
                    'upiId' => $bank?->upi_id,
                    'beneficiaryName' => $bank?->beneficiary_name,
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
        $seller->loadMissing('application');

        // Get bank details
        $bank = \App\Models\Store\SellerBankAccount::query()->where('seller_id', $seller->id)->first();
        $maskedAccountNumber = null;
        if ($bank?->account_number) {
            $last4 = substr($bank->account_number, -4);
            $maskedAccountNumber = 'XXXXXX' . $last4;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sellerId' => $seller->seller_code,
                'shopId' => $seller->application?->shop_code,
                'shopName' => $seller->application?->store_name,
                'ownerName' => $seller->owner_name,
                'email' => $seller->email,
                'phone' => $seller->phone,
                'bankDetails' => [
                    'bankName' => $bank?->bank_name,
                    'accountNumber' => $maskedAccountNumber,
                    'ifsc' => $bank?->ifsc,
                    'upiId' => $bank?->upi_id,
                    'beneficiaryName' => $bank?->beneficiary_name,
                ],
            ],
        ]);
    }
}
