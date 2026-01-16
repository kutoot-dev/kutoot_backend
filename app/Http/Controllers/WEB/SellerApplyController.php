<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Store\SellerApplication;
use App\Models\Store\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SellerApplyController extends Controller
{
    /**
     * Show the seller application form
     * GET /become-a-seller
     */
    public function showForm()
    {
        $categories = StoreCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('seller_apply', compact('categories'));
    }

    /**
     * Send OTP to mobile number
     * POST /become-a-seller/send-otp
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number'
            ], 422);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP in cache for 10 minutes
        Cache::put('seller_apply_otp_' . $request->phone, $otp, now()->addMinutes(10));

        // In production, send SMS here
        // For now, we'll just return success (and log the OTP for testing)
        \Log::info('Seller Apply OTP for ' . $request->phone . ': ' . $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            // Remove this in production - only for testing
            'debug_otp' => config('app.debug') ? $otp : null
        ]);
    }

    /**
     * Verify OTP
     * POST /become-a-seller/verify-otp
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input'
            ], 422);
        }

        $storedOtp = Cache::get('seller_apply_otp_' . $request->phone);

        if (!$storedOtp || $storedOtp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP invalid or expired'
            ], 400);
        }

        // Mark as verified in cache
        Cache::put('seller_apply_verified_' . $request->phone, true, now()->addMinutes(30));

        return response()->json([
            'success' => true,
            'message' => 'OTP verified'
        ]);
    }

    /**
     * Submit the seller application
     * POST /become-a-seller
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|string|max:255',
            'owner_mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'owner_email' => 'required|email|max:255',
            'store_type' => 'required|string|max:100',
            'store_address' => 'required|string|max:500',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'min_bill_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify OTP was completed
        $isVerified = Cache::get('seller_apply_verified_' . $request->owner_mobile);
        if (!$isVerified) {
            return redirect()->back()
                ->with('error', 'Please verify your mobile number with OTP first')
                ->withInput();
        }

        // Check for existing application
        $existingApplication = SellerApplication::where('owner_mobile', $request->owner_mobile)
            ->whereIn('status', [SellerApplication::STATUS_PENDING, SellerApplication::STATUS_VERIFIED])
            ->first();

        if ($existingApplication) {
            return redirect()->back()
                ->with('error', 'An application with this mobile number is already pending review (ID: ' . $existingApplication->application_id . ')')
                ->withInput();
        }

        // Create application
        $application = SellerApplication::create([
            'application_id' => SellerApplication::generateApplicationId(),
            'store_name' => $request->store_name,
            'owner_mobile' => $request->owner_mobile,
            'owner_email' => $request->owner_email,
            'store_type' => $request->store_type,
            'store_address' => $request->store_address,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'min_bill_amount' => $request->min_bill_amount,
            'status' => SellerApplication::STATUS_PENDING,
        ]);

        // Clear verification cache
        Cache::forget('seller_apply_verified_' . $request->owner_mobile);
        Cache::forget('seller_apply_otp_' . $request->owner_mobile);

        return redirect()->route('seller.apply.success')
            ->with('application_id', $application->application_id);
    }

    /**
     * Show success page
     * GET /become-a-seller/success
     */
    public function success()
    {
        $applicationId = session('application_id');
        
        if (!$applicationId) {
            return redirect()->route('seller.apply');
        }

        return view('seller_apply_success', compact('applicationId'));
    }
}

