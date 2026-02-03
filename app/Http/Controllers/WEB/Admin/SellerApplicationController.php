<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store\SellerApplication;
use App\Models\Store\Seller;
use App\Mail\SellerApplicationApproved;
use App\Mail\SellerApplicationRejected;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Nnjeim\World\World;

/**
 * @group Seller Application
 */
class SellerApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display list of seller applications (Blade view)
     * GET /admin/seller-applications
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $applications = SellerApplication::query()
            ->when($status, fn($q) => $q->where('status', strtoupper($status)))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statusCounts = [
            'all' => SellerApplication::count(),
            'pending' => SellerApplication::where('status', SellerApplication::STATUS_PENDING)->count(),
            'verified' => SellerApplication::where('status', SellerApplication::STATUS_VERIFIED)->count(),
            'approved' => SellerApplication::where('status', SellerApplication::STATUS_APPROVED)->count(),
            'rejected' => SellerApplication::where('status', SellerApplication::STATUS_REJECTED)->count(),
        ];

        return view('admin.seller_applications', compact('applications', 'statusCounts', 'status'));
    }

    /**
     * Show create form for a new application
     * GET /admin/seller-applications/create
     */
    public function create()
    {
        $countries = World::countries()->data;
        return view('admin.create_seller_application', compact('countries'));
    }

    /**
     * Store a new application (web form)
     * POST /admin/seller-applications
     */
    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_mobile' => 'required|string|max:15',
            'owner_email' => 'nullable|email|max:255',
            'store_type' => 'required|string|max:100',
            'store_address' => 'required|string|max:500',
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'min_bill_amount' => 'nullable|numeric|min:0',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'store_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'gst_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'beneficiary_name' => 'nullable|string|max:255',
            'upi_id' => 'nullable|string|max:255',
        ]);

        // Handle store image upload
        $storeImagePath = null;
        if ($request->hasFile('store_image')) {
            $image = $request->file('store_image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/stores'), $imageName);
            $storeImagePath = 'uploads/stores/' . $imageName;
        }

        // Handle multiple images upload
        $imagesArray = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/stores'), $imageName);
                $imagesArray[] = 'uploads/stores/' . $imageName;
            }
        }

        // Fetch country, state, city names from the World package
        $countryName = null;
        $stateName = null;
        $cityName = null;

        if ($request->country_id) {
            $country = World::countries(['filters' => ['id' => $request->country_id]])->data->first();
            $countryName = $country ? (is_array($country) ? $country['name'] : $country->name) : null;
        }

        if ($request->state_id) {
            $state = World::states(['filters' => ['id' => $request->state_id]])->data->first();
            $stateName = $state ? (is_array($state) ? $state['name'] : $state->name) : null;
        }

        if ($request->city_id) {
            $city = World::cities(['filters' => ['id' => $request->city_id]])->data->first();
            $cityName = $city ? (is_array($city) ? $city['name'] : $city->name) : null;
        }

        $application = SellerApplication::create([
            'application_id' => SellerApplication::generateApplicationId(),
            'store_name' => $request->store_name,
            'owner_mobile' => $request->owner_mobile,
            'owner_email' => $request->owner_email,
            'store_type' => $request->store_type,
            'store_address' => $request->store_address,
            'country' => $countryName,
            'country_id' => $request->country_id,
            'state' => $stateName,
            'state_id' => $request->state_id,
            'city' => $cityName,
            'city_id' => $request->city_id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'min_bill_amount' => $request->min_bill_amount ?? 0,
            'commission_percent' => $request->commission_percent,
            'discount_percent' => $request->discount_percent,
            'rating' => $request->rating,
            'store_image' => $storeImagePath,
            'images' => !empty($imagesArray) ? $imagesArray : null,
            'gst_number' => $request->gst_number,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code ? strtoupper($request->ifsc_code) : null,
            'beneficiary_name' => $request->beneficiary_name,
            'upi_id' => $request->upi_id,
            'status' => SellerApplication::STATUS_PENDING,
        ]);

        return redirect()->route('admin.seller-applications.show', $application->id)
            ->with('success', 'Application created successfully with ID: ' . $application->application_id);
    }

    /**
     * Show single application details (Blade view)
     * GET /admin/seller-applications/{id}
     */
    public function show($id)
    {
        $application = SellerApplication::with(['verifier', 'approver', 'rejecter', 'seller'])
            ->findOrFail($id);

        return view('admin.show_seller_application', compact('application'));
    }

    /**
     * Show edit form for an application
     * GET /admin/seller-applications/{id}/edit
     */
    public function edit($id)
    {
        $application = SellerApplication::findOrFail($id);
        $countries = World::countries()->data;

        $states = [];
        $cities = [];

        // Get states if country is set
        if ($application->country_id) {
            $states = World::states([
                'filters' => [
                    'country_id' => $application->country_id,
                ],
            ])->data ?? [];
        }

        // Get cities if state is set
        if ($application->state_id) {
            $cities = World::cities([
                'filters' => [
                    'state_id' => $application->state_id,
                ],
            ])->data ?? [];
        }

        return view('admin.edit_seller_application', compact('application', 'countries', 'states', 'cities'));
    }

    /**
     * Update an application
     * PUT /admin/seller-applications/{id}
     *
     * Note: For approved applications, changes will automatically sync to the Shop table
     * via the StoreDetailsSyncObserver.
     */
    public function update(Request $request, $id)
    {
        $application = SellerApplication::findOrFail($id);

        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_mobile' => 'required|string|max:15',
            'owner_email' => 'nullable|email|max:255',
            'store_type' => 'required|string|max:100',
            'store_address' => 'required|string|max:500',
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'min_bill_amount' => 'nullable|numeric|min:0',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'store_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'gst_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'beneficiary_name' => 'nullable|string|max:255',
            'upi_id' => 'nullable|string|max:255',
            'remove_images' => 'nullable|array',
        ]);

        // Handle store image upload
        $storeImagePath = $application->store_image;
        if ($request->hasFile('store_image')) {
            // Delete old image if exists
            if ($application->store_image && file_exists(public_path($application->store_image))) {
                unlink(public_path($application->store_image));
            }
            $image = $request->file('store_image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/stores'), $imageName);
            $storeImagePath = 'uploads/stores/' . $imageName;
        }

        // Handle multiple images - keep existing ones and add new ones
        $existingImages = $application->images ?? [];

        // Remove images if requested
        if ($request->has('remove_images') && is_array($request->remove_images)) {
            foreach ($request->remove_images as $removeImage) {
                if (file_exists(public_path($removeImage))) {
                    unlink(public_path($removeImage));
                }
                $existingImages = array_filter($existingImages, fn($img) => $img !== $removeImage);
            }
            $existingImages = array_values($existingImages); // Re-index array
        }

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/stores'), $imageName);
                $existingImages[] = 'uploads/stores/' . $imageName;
            }
        }

        // Fetch country, state, city names from the World package
        $countryName = null;
        $stateName = null;
        $cityName = null;

        if ($request->country_id) {
            $country = World::countries(['filters' => ['id' => $request->country_id]])->data->first();
            $countryName = $country ? (is_array($country) ? $country['name'] : $country->name) : null;
        }

        if ($request->state_id) {
            $state = World::states(['filters' => ['id' => $request->state_id]])->data->first();
            $stateName = $state ? (is_array($state) ? $state['name'] : $state->name) : null;
        }

        if ($request->city_id) {
            $city = World::cities(['filters' => ['id' => $request->city_id]])->data->first();
            $cityName = $city ? (is_array($city) ? $city['name'] : $city->name) : null;
        }

        $application->update([
            'store_name' => $request->store_name,
            'owner_mobile' => $request->owner_mobile,
            'owner_email' => $request->owner_email,
            'store_type' => $request->store_type,
            'store_address' => $request->store_address,
            'country' => $countryName,
            'country_id' => $request->country_id,
            'state' => $stateName,
            'state_id' => $request->state_id,
            'city' => $cityName,
            'city_id' => $request->city_id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'min_bill_amount' => $request->min_bill_amount ?? 0,
            'commission_percent' => $request->commission_percent,
            'discount_percent' => $request->discount_percent,
            'rating' => $request->rating,
            'store_image' => $storeImagePath,
            'images' => !empty($existingImages) ? $existingImages : null,
            'gst_number' => $request->gst_number,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code ? strtoupper($request->ifsc_code) : null,
            'beneficiary_name' => $request->beneficiary_name,
            'upi_id' => $request->upi_id,
        ]);

        return redirect()->route('admin.seller-applications.show', $id)
            ->with('success', 'Application updated successfully');
    }

    /**
     * Delete an application
     * DELETE /admin/seller-applications/{id}
     */
    public function destroy($id)
    {
        $application = SellerApplication::findOrFail($id);

        if ($application->isApproved() && $application->seller_id) {
            return redirect()->back()->with('error', 'Cannot delete approved application with active seller account');
        }

        $application->delete();

        return redirect()->route('admin.seller-applications.index')
            ->with('success', 'Application deleted successfully');
    }

    // ==================== API ENDPOINTS ====================

    /**
     * API: Create a new seller application (admin-created)
     * POST /api/admin/seller-applications
     */
    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storeName' => 'required|string|max:255',
            'ownerMobile' => 'required|string|max:15',
            'ownerEmail' => 'nullable|email|max:255',
            'storeType' => 'required|string|max:100',
            'storeAddress' => 'required|string|max:500',
            'state' => 'nullable|string|max:100',
            'stateId' => 'nullable|integer',
            'city' => 'nullable|string|max:100',
            'cityId' => 'nullable|integer',
            'country' => 'nullable|string|max:100',
            'countryId' => 'nullable|integer',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'minBillAmount' => 'nullable|numeric|min:0',
            'gstNumber' => 'nullable|string|max:20',
            'bankName' => 'nullable|string|max:255',
            'accountNumber' => 'nullable|string|max:50',
            'ifscCode' => 'nullable|string|max:20',
            'beneficiaryName' => 'nullable|string|max:255',
            'upiId' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $application = SellerApplication::create([
            'application_id' => SellerApplication::generateApplicationId(),
            'store_name' => $request->storeName,
            'owner_mobile' => $request->ownerMobile,
            'owner_email' => $request->ownerEmail,
            'store_type' => $request->storeType,
            'store_address' => $request->storeAddress,
            'state' => $request->state,
            'state_id' => $request->stateId,
            'city' => $request->city,
            'city_id' => $request->cityId,
            'country' => $request->country,
            'country_id' => $request->countryId,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'min_bill_amount' => $request->minBillAmount ?? 0,
            'gst_number' => $request->gstNumber,
            'bank_name' => $request->bankName,
            'account_number' => $request->accountNumber,
            'ifsc_code' => $request->ifscCode ? strtoupper($request->ifscCode) : null,
            'beneficiary_name' => $request->beneficiaryName,
            'upi_id' => $request->upiId,
            'status' => SellerApplication::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application created successfully',
            'data' => $this->formatApplicationResponse($application)
        ], 201);
    }

    /**
     * API: Delete a seller application
     * DELETE /api/admin/seller-applications/{applicationId}
     */
    public function apiDestroy($applicationId)
    {
        $application = SellerApplication::where('application_id', $applicationId)->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        if ($application->isApproved() && $application->seller_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete approved application with active seller account'
            ], 400);
        }

        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully'
        ]);
    }

    /**
     * API: Get list of seller applications
     * GET /api/admin/seller-applications
     */
    public function apiIndex(Request $request)
    {
        $status = $request->query('status');

        $applications = SellerApplication::query()
            ->when($status, fn($q) => $q->where('status', strtoupper($status)))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications->map(fn($app) => $this->formatApplicationResponse($app))
        ]);
    }

    /**
     * API: Get single application details
     * GET /api/admin/seller-applications/{applicationId}
     */
    public function apiShow($applicationId)
    {
        $application = SellerApplication::where('application_id', $applicationId)->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $data = $this->formatApplicationResponse($application);
        $data['verificationNotes'] = $application->verification_notes;
        $data['rejectionReason'] = $application->rejection_reason;
        $data['sellerEmail'] = $application->seller_email;
        $data['updatedAt'] = $application->updated_at->toIso8601String();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * API: Update application info (admin edits)
     * PATCH /api/admin/seller-applications/{applicationId}
     *
     * Note: For approved applications, changes will automatically sync to the Shop table.
     */
    public function apiUpdate(Request $request, $applicationId)
    {
        $application = SellerApplication::where('application_id', $applicationId)->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        // Approved applications can be updated - changes sync to Shop automatically
        $validator = Validator::make($request->all(), [
            'storeName' => 'sometimes|string|max:255',
            'ownerMobile' => 'sometimes|string|max:15',
            'ownerEmail' => 'sometimes|email|max:255',
            'storeType' => 'sometimes|string|max:100',
            'storeAddress' => 'sometimes|string|max:500',
            'state' => 'sometimes|string|max:100',
            'stateId' => 'sometimes|integer',
            'city' => 'sometimes|string|max:100',
            'cityId' => 'sometimes|integer',
            'country' => 'sometimes|string|max:100',
            'countryId' => 'sometimes|integer',
            'lat' => 'sometimes|numeric',
            'lng' => 'sometimes|numeric',
            'minBillAmount' => 'sometimes|numeric|min:0',
            'gstNumber' => 'sometimes|string|max:20',
            'bankName' => 'sometimes|string|max:255',
            'accountNumber' => 'sometimes|string|max:50',
            'ifscCode' => 'sometimes|string|max:20',
            'beneficiaryName' => 'sometimes|string|max:255',
            'upiId' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];
        if ($request->has('storeName'))
            $updateData['store_name'] = $request->storeName;
        if ($request->has('ownerMobile'))
            $updateData['owner_mobile'] = $request->ownerMobile;
        if ($request->has('ownerEmail'))
            $updateData['owner_email'] = $request->ownerEmail;
        if ($request->has('storeType'))
            $updateData['store_type'] = $request->storeType;
        if ($request->has('storeAddress'))
            $updateData['store_address'] = $request->storeAddress;
        if ($request->has('state'))
            $updateData['state'] = $request->state;
        if ($request->has('stateId'))
            $updateData['state_id'] = $request->stateId;
        if ($request->has('city'))
            $updateData['city'] = $request->city;
        if ($request->has('cityId'))
            $updateData['city_id'] = $request->cityId;
        if ($request->has('country'))
            $updateData['country'] = $request->country;
        if ($request->has('countryId'))
            $updateData['country_id'] = $request->countryId;
        if ($request->has('lat'))
            $updateData['lat'] = $request->lat;
        if ($request->has('lng'))
            $updateData['lng'] = $request->lng;
        if ($request->has('minBillAmount'))
            $updateData['min_bill_amount'] = $request->minBillAmount;
        if ($request->has('gstNumber'))
            $updateData['gst_number'] = $request->gstNumber;
        if ($request->has('bankName'))
            $updateData['bank_name'] = $request->bankName;
        if ($request->has('accountNumber'))
            $updateData['account_number'] = $request->accountNumber;
        if ($request->has('ifscCode'))
            $updateData['ifsc_code'] = $request->ifscCode ? strtoupper($request->ifscCode) : null;
        if ($request->has('beneficiaryName'))
            $updateData['beneficiary_name'] = $request->beneficiaryName;
        if ($request->has('upiId'))
            $updateData['upi_id'] = $request->upiId;

        if (!empty($updateData)) {
            $application->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application updated successfully',
            'data' => $this->formatApplicationResponse($application)
        ]);
    }

    /**
     * API: Verify application (after manual call)
     * PATCH /api/admin/seller-applications/{applicationId}/verify
     */
    public function apiVerify(Request $request, $applicationId)
    {
        $application = SellerApplication::where('application_id', $applicationId)->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        if (!$application->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Only PENDING applications can be verified'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'verifiedBy' => 'required|string',
            'verificationNotes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $application->update([
            'status' => SellerApplication::STATUS_VERIFIED,
            'verified_by' => Auth::guard('admin')->id() ?? $request->verifiedBy,
            'verification_notes' => $request->verificationNotes,
            'verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seller verified successfully',
            'status' => $application->status
        ]);
    }

    /**
     * API: Approve application and create seller account
     * PATCH /api/admin/seller-applications/{applicationId}/approve
     */
    public function apiApprove(Request $request, $applicationId)
    {
        $application = SellerApplication::where('application_id', $applicationId)->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        if (!$application->isVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Only VERIFIED applications can be approved'
            ], 400);
        }

        // Check if already approved and seller exists
        if ($application->isApproved() && $application->seller_id) {
            $existingSeller = Seller::find($application->seller_id);
            if ($existingSeller) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application already approved. Seller account already exists.'
                ], 400);
            }
        }

        $validator = Validator::make($request->all(), [
            'approvedBy' => 'required|string',
            'sellerEmail' => 'required|email',
            'commissionPercent' => 'nullable|numeric|min:0|max:100',
            'discountPercent' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate seller
        $username = 'seller_' . $application->owner_mobile;
        $existingSeller = Seller::where('username', $username)
            ->orWhere('phone', $application->owner_mobile)
            ->orWhere('email', $request->sellerEmail)
            ->first();

        if ($existingSeller) {
            // Link existing seller to application
            $application->update([
                'status' => SellerApplication::STATUS_APPROVED,
                'approved_by' => Auth::guard('admin')->id() ?? $request->approvedBy,
                'seller_email' => $request->sellerEmail,
                'approved_at' => now(),
                'seller_id' => $existingSeller->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Seller account already exists with this username/phone/email. Application linked to existing seller.'
            ], 400);
        }

        // Use database transaction to ensure atomicity
        try {
            \DB::beginTransaction();

            // Generate credentials
            $temporaryPassword = 'Kutoot@' . rand(100, 999);
            $sellerCode = 'SELL-' . date('Y') . rand(10, 99);

            // Create seller account
            $seller = Seller::create([
                'seller_code' => $sellerCode,
                'username' => $username,
                'password' => Hash::make($temporaryPassword),
                'owner_name' => $application->store_name,
                'email' => $request->sellerEmail,
                'phone' => $application->owner_mobile,
                'status' => 1,
            ]);

            if (!$seller || !$seller->id) {
                throw new \Exception('Failed to create seller account');
            }

            // Get commission/discount values from request or use defaults
            $commissionPercent = $request->has('commissionPercent') && $request->commissionPercent !== null
                ? $request->commissionPercent
                : ($application->commission_percent ?? 10);

            $discountPercent = $request->has('discountPercent') && $request->discountPercent !== null
                ? $request->discountPercent
                : ($application->discount_percent ?? 0);

            $rating = $request->has('rating') && $request->rating !== null
                ? $request->rating
                : ($application->rating ?? 0);

            // Update application with all settings (single source of truth)
            $application->update([
                'status' => SellerApplication::STATUS_APPROVED,
                'approved_by' => Auth::guard('admin')->id() ?? $request->approvedBy,
                'seller_email' => $request->sellerEmail,
                'approved_at' => now(),
                'seller_id' => $seller->id,
                'shop_code' => SellerApplication::generateShopCode(),
                'commission_percent' => $commissionPercent,
                'discount_percent' => $discountPercent,
                'rating' => $rating,
                'is_active' => true,
            ]);

            \DB::commit();

            // Send email with credentials (outside transaction)
            try {
                MailHelper::setEnvMailConfig();
                $loginUrl = "https://www.kutoot.com/store";
                Mail::to($request->sellerEmail)->send(
                    new SellerApplicationApproved(
                        $application->store_name,
                        $username,
                        $temporaryPassword,
                        $loginUrl
                    )
                );
            } catch (\Exception $e) {
                // Log but don't fail the request
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Seller approved & credentials sent',
                'status' => SellerApplication::STATUS_APPROVED,
                'sellerId' => $sellerCode,
                'credentials' => [
                    'username' => $username,
                    'temporaryPassword' => $temporaryPassword,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to approve seller application: ' . $e->getMessage(), [
                'application_id' => $application->application_id,
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Reject application
     * PATCH /api/admin/seller-applications/{applicationId}/reject
     */
    public function apiReject(Request $request, $applicationId)
    {
        $application = SellerApplication::where('application_id', $applicationId)->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        if ($application->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot reject an already approved application'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'rejectedBy' => 'required|string',
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $application->update([
            'status' => SellerApplication::STATUS_REJECTED,
            'rejected_by' => Auth::guard('admin')->id() ?? $request->rejectedBy,
            'rejection_reason' => $request->reason,
            'rejected_at' => now(),
        ]);

        // Send rejection email if we have an email
        if ($application->seller_email) {
            try {
                MailHelper::setEnvMailConfig();
                Mail::to($application->seller_email)->send(
                    new SellerApplicationRejected(
                        $application->store_name,
                        $request->reason
                    )
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send rejection email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Seller rejected & reason sent to seller',
            'status' => SellerApplication::STATUS_REJECTED
        ]);
    }

    // ==================== WEB ACTIONS (for Blade forms) ====================

    /**
     * Verify application via web form
     * POST /admin/seller-applications/{id}/verify
     */
    public function webVerify(Request $request, $id)
    {
        $application = SellerApplication::findOrFail($id);

        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'Only PENDING applications can be verified');
        }

        $application->update([
            'status' => SellerApplication::STATUS_VERIFIED,
            'verified_by' => Auth::guard('admin')->id(),
            'verification_notes' => $request->verification_notes,
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Application verified successfully');
    }

    /**
     * Approve application via web form
     * POST /admin/seller-applications/{id}/approve
     */
    public function webApprove(Request $request, $id)
    {
        $application = SellerApplication::findOrFail($id);

        if (!$application->isVerified()) {
            return redirect()->back()->with('error', 'Only VERIFIED applications can be approved');
        }

        // Check if already approved and seller exists
        if ($application->isApproved() && $application->seller_id) {
            $existingSeller = Seller::find($application->seller_id);
            if ($existingSeller) {
                return redirect()->back()->with('error', 'Application already approved. Seller account already exists.');
            }
        }

        $request->validate([
            'seller_email' => 'required|email',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        // Check for duplicate seller
        $username = 'seller_' . $application->owner_mobile;
        $existingSeller = Seller::where('username', $username)
            ->orWhere('phone', $application->owner_mobile)
            ->orWhere('email', $request->seller_email)
            ->first();

        if ($existingSeller) {
            // Link existing seller to application
            $application->update([
                'status' => SellerApplication::STATUS_APPROVED,
                'approved_by' => Auth::guard('admin')->id(),
                'seller_email' => $request->seller_email,
                'approved_at' => now(),
                'seller_id' => $existingSeller->id,
            ]);
            return redirect()->back()->with('error', 'Seller account already exists with this username/phone/email. Application linked to existing seller.');
        }

        // Use database transaction to ensure atomicity
        try {
            \DB::beginTransaction();

            // Generate credentials
            $temporaryPassword = 'Kutoot@' . rand(100, 999);
            $sellerCode = 'SELL-' . date('Y') . rand(10, 99);

            // Create seller account
            $seller = Seller::create([
                'seller_code' => $sellerCode,
                'username' => $username,
                'password' => Hash::make($temporaryPassword),
                'owner_name' => $application->store_name,
                'email' => $request->seller_email,
                'phone' => $application->owner_mobile,
                'status' => 1,
            ]);

            if (!$seller || !$seller->id) {
                throw new \Exception('Failed to create seller account');
            }

            // Get commission/discount values from request or use defaults
            $commissionPercent = $request->has('commission_percent') && $request->commission_percent !== null
                ? $request->commission_percent
                : ($application->commission_percent ?? 10);

            $discountPercent = $request->has('discount_percent') && $request->discount_percent !== null
                ? $request->discount_percent
                : ($application->discount_percent ?? 0);

            $rating = $request->has('rating') && $request->rating !== null
                ? $request->rating
                : ($application->rating ?? 0);

            // Update application with all settings (single source of truth)
            $application->update([
                'status' => SellerApplication::STATUS_APPROVED,
                'approved_by' => Auth::guard('admin')->id(),
                'seller_email' => $request->seller_email,
                'approved_at' => now(),
                'seller_id' => $seller->id,
                'shop_code' => SellerApplication::generateShopCode(),
                'commission_percent' => $commissionPercent,
                'discount_percent' => $discountPercent,
                'rating' => $rating,
                'is_active' => true,
            ]);

            \DB::commit();

            // Send email (outside transaction - don't fail if email fails)
            $emailSent = false;
            try {
                MailHelper::setEnvMailConfig();
                $loginUrl = "https://www.kutoot.com/store";
                Mail::to($request->seller_email)->send(
                    new SellerApplicationApproved(
                        $application->store_name,
                        $username,
                        $temporaryPassword,
                        $loginUrl
                    )
                );
                $emailSent = true;
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }

            $message = 'Seller approved successfully! Credentials: Username: ' . $username . ', Password: ' . $temporaryPassword;
            if (!$emailSent) {
                $message .= ' (Email could not be sent)';
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to approve seller application: ' . $e->getMessage(), [
                'application_id' => $application->application_id,
                'exception' => $e
            ]);
            return redirect()->back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }
    }

    /**
     * Reject application via web form
     * POST /admin/seller-applications/{id}/reject
     */
    public function webReject(Request $request, $id)
    {
        $application = SellerApplication::findOrFail($id);

        if ($application->isApproved()) {
            return redirect()->back()->with('error', 'Cannot reject an already approved application');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $application->update([
            'status' => SellerApplication::STATUS_REJECTED,
            'rejected_by' => Auth::guard('admin')->id(),
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
        ]);

        // Send rejection email if we have an email
        if ($request->seller_email) {
            try {
                MailHelper::setEnvMailConfig();
                Mail::to($request->seller_email)->send(
                    new SellerApplicationRejected(
                        $application->store_name,
                        $request->rejection_reason
                    )
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send rejection email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Application rejected successfully');
    }

    /**
     * Format application response for API with structured address and images
     */
    private function formatApplicationResponse($application)
    {
        $baseUrl = config('app.url', url('/'));

        // Format images as full URLs
        $images = [];
        if ($application->images) {
            $imageArray = is_array($application->images) ? $application->images : json_decode($application->images, true);
            if ($imageArray) {
                foreach ($imageArray as $image) {
                    if ($image) {
                        $images[] = str_starts_with($image, 'http') ? $image : $baseUrl . '/' . ltrim($image, '/');
                    }
                }
            }
        }

        // Add store_image to images array if exists
        if ($application->store_image) {
            $storeImageUrl = str_starts_with($application->store_image, 'http')
                ? $application->store_image
                : $baseUrl . '/' . ltrim($application->store_image, '/');
            if (!in_array($storeImageUrl, $images)) {
                array_unshift($images, $storeImageUrl);
            }
        }

        return [
            'applicationId' => $application->application_id,
            'storeName' => $application->store_name,
            'ownerMobile' => $application->owner_mobile,
            'ownerEmail' => $application->owner_email,
            'storeType' => $application->store_type,
            'storeImage' => $application->store_image ? $baseUrl . '/' . ltrim($application->store_image, '/') : null,
            'images' => $images,
            'address' => [
                'storeAddress' => $application->store_address,
                'city' => $application->city,
                'cityId' => $application->city_id,
                'state' => $application->state,
                'stateId' => $application->state_id,
                'country' => $application->country,
                'countryId' => $application->country_id,
                'lat' => $application->lat,
                'lng' => $application->lng,
            ],
            'minBillAmount' => $application->min_bill_amount,
            'gstNumber' => $application->gst_number,
            'bankDetails' => [
                'bankName' => $application->bank_name,
                'accountNumber' => $application->account_number,
                'ifscCode' => $application->ifsc_code,
                'beneficiaryName' => $application->beneficiary_name,
                'upiId' => $application->upi_id,
            ],
            'status' => $application->status,
            'createdAt' => $application->created_at->toIso8601String(),
        ];
    }

    /**
     * Resend credentials email to seller (uses existing password - just sends reminder)
     * POST /admin/seller-applications/{id}/resend-credentials
     */
    public function resendCredentials(Request $request, $id)
    {
        $application = SellerApplication::with('seller')->findOrFail($id);

        if (!$application->isApproved() || !$application->seller) {
            return redirect()->back()->with('error', 'Application must be approved with a seller account to resend credentials');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $seller = $application->seller;

        try {
            MailHelper::setEnvMailConfig();
            $loginUrl = "https://www.kutoot.com/store";

            // Send email with username only (password not stored in plain text)
            Mail::to($request->email)->send(
                new \App\Mail\SellerCredentialsReminder(
                    $application->store_name,
                    $seller->username,
                    $loginUrl
                )
            );

            // Update seller email if different
            if ($request->email !== $application->seller_email) {
                $application->update(['seller_email' => $request->email]);
                $seller->update(['email' => $request->email]);
            }

            return redirect()->back()->with('success', 'Credentials reminder sent to ' . $request->email . '. Username: ' . $seller->username);

        } catch (\Exception $e) {
            \Log::error('Failed to resend credentials: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Reset seller password and send new credentials
     * POST /admin/seller-applications/{id}/reset-password
     */
    public function resetAndResendPassword(Request $request, $id)
    {
        $application = SellerApplication::with('seller')->findOrFail($id);

        if (!$application->isApproved() || !$application->seller) {
            return redirect()->back()->with('error', 'Application must be approved with a seller account to reset password');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $seller = $application->seller;

        try {
            // Generate new password
            $newPassword = 'Kutoot@' . rand(100, 999);

            // Update seller password
            $seller->update([
                'password' => Hash::make($newPassword),
            ]);

            // Update email if changed
            if ($request->email !== $application->seller_email) {
                $application->update(['seller_email' => $request->email]);
                $seller->update(['email' => $request->email]);
            }

            // Send email with new credentials
            MailHelper::setEnvMailConfig();
            $loginUrl = "https://www.kutoot.com/store";

            Mail::to($request->email)->send(
                new SellerApplicationApproved(
                    $application->store_name,
                    $seller->username,
                    $newPassword,
                    $loginUrl
                )
            );

            return redirect()->back()->with('success', 'Password reset successfully! New credentials sent to ' . $request->email . '. Username: ' . $seller->username . ', New Password: ' . $newPassword);

        } catch (\Exception $e) {
            \Log::error('Failed to reset password: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reset password: ' . $e->getMessage());
        }
    }
}
