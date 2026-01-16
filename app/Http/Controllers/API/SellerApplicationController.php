<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Store\SellerApplication;
use App\Models\Store\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SellerApplicationController extends Controller
{
    /**
     * Get store categories for dropdown
     * GET /api/store-categories
     */
    public function getCategories()
    {
        $categories = StoreCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Submit seller application
     * POST /api/seller/apply
     */
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storeName' => 'required|string|max:255',
            'ownerMobile' => 'required|string|max:15',
            'ownerEmail' => 'required|email|max:255',
            'storeType' => 'required|string|max:100',
            'storeAddress' => 'required|string|max:500',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'minBillAmount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request / missing fields',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if mobile already has a pending/verified application
        $existingApplication = SellerApplication::where('owner_mobile', $request->ownerMobile)
            ->whereIn('status', [SellerApplication::STATUS_PENDING, SellerApplication::STATUS_VERIFIED])
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'An application with this mobile number is already pending review',
                'applicationId' => $existingApplication->application_id,
                'status' => $existingApplication->status
            ], 409);
        }

        $application = SellerApplication::create([
            'application_id' => SellerApplication::generateApplicationId(),
            'store_name' => $request->storeName,
            'owner_mobile' => $request->ownerMobile,
            'owner_email' => $request->ownerEmail,
            'store_type' => $request->storeType,
            'store_address' => $request->storeAddress,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'min_bill_amount' => $request->minBillAmount,
            'status' => SellerApplication::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seller application submitted successfully',
            'applicationId' => $application->application_id,
            'status' => $application->status
        ], 201);
    }

    /**
     * Check application status by mobile number
     * GET /api/seller/application-status
     */
    public function checkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number is required'
            ], 422);
        }

        $application = SellerApplication::where('owner_mobile', $request->mobile)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'No application found for this mobile number'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'applicationId' => $application->application_id,
                'storeName' => $application->store_name,
                'status' => $application->status,
                'createdAt' => $application->created_at->toIso8601String(),
                'rejectionReason' => $application->rejection_reason,
            ]
        ]);
    }
}

