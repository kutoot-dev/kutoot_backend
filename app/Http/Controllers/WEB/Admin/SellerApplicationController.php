<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store\SellerApplication;
use App\Models\Store\Seller;
use App\Models\Store\Shop;
use App\Models\Store\AdminShopCommissionDiscount;
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
        ]);

        // Handle store image upload
        $storeImagePath = null;
        if ($request->hasFile('store_image')) {
            $image = $request->file('store_image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/stores'), $imageName);
            $storeImagePath = 'uploads/stores/' . $imageName;
        }

        // Fetch country, state, city names from the World package
        $countryName = null;
        $stateName = null;
        $cityName = null;

        if ($request->country_id) {
            $country = World::countries(['filters' => ['id' => $request->country_id]])->data->first();
            $countryName = $country ? $country->name : null;
        }

        if ($request->state_id) {
            $state = World::states(['filters' => ['id' => $request->state_id]])->data->first();
            $stateName = $state ? $state->name : null;
        }

        if ($request->city_id) {
            $city = World::cities(['filters' => ['id' => $request->city_id]])->data->first();
            $cityName = $city ? $city->name : null;
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

        // Fetch country, state, city names from the World package
        $countryName = null;
        $stateName = null;
        $cityName = null;

        if ($request->country_id) {
            $country = World::countries(['filters' => ['id' => $request->country_id]])->data->first();
            $countryName = $country ? $country->name : null;
        }

        if ($request->state_id) {
            $state = World::states(['filters' => ['id' => $request->state_id]])->data->first();
            $stateName = $state ? $state->name : null;
        }

        if ($request->city_id) {
            $city = World::cities(['filters' => ['id' => $request->city_id]])->data->first();
            $cityName = $city ? $city->name : null;
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
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'minBillAmount' => 'nullable|numeric|min:0',
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
            'city' => $request->city,
            'country' => $request->country,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'min_bill_amount' => $request->minBillAmount ?? 0,
            'status' => SellerApplication::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application created successfully',
            'data' => [
                'applicationId' => $application->application_id,
                'storeName' => $application->store_name,
                'ownerMobile' => $application->owner_mobile,
                'ownerEmail' => $application->owner_email,
                'storeType' => $application->store_type,
                'storeAddress' => $application->store_address,
                'state' => $application->state,
                'city' => $application->city,
                'country' => $application->country,
                'lat' => $application->lat,
                'lng' => $application->lng,
                'minBillAmount' => $application->min_bill_amount,
                'status' => $application->status,
                'createdAt' => $application->created_at->toIso8601String(),
            ]
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
            'data' => $applications->map(fn($app) => [
                'applicationId' => $app->application_id,
                'storeName' => $app->store_name,
                'ownerMobile' => $app->owner_mobile,
                'ownerEmail' => $app->owner_email,
                'storeType' => $app->store_type,
                'storeAddress' => $app->store_address,
                'state' => $app->state,
                'city' => $app->city,
                'country' => $app->country,
                'lat' => $app->lat,
                'lng' => $app->lng,
                'minBillAmount' => $app->min_bill_amount,
                'status' => $app->status,
                'createdAt' => $app->created_at->toIso8601String(),
            ])
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

        return response()->json([
            'success' => true,
            'data' => [
                'applicationId' => $application->application_id,
                'storeName' => $application->store_name,
                'ownerMobile' => $application->owner_mobile,
                'ownerEmail' => $application->owner_email,
                'storeType' => $application->store_type,
                'storeAddress' => $application->store_address,
                'state' => $application->state,
                'city' => $application->city,
                'country' => $application->country,
                'lat' => $application->lat,
                'lng' => $application->lng,
                'minBillAmount' => $application->min_bill_amount,
                'status' => $application->status,
                'verificationNotes' => $application->verification_notes,
                'rejectionReason' => $application->rejection_reason,
                'sellerEmail' => $application->seller_email,
                'createdAt' => $application->created_at->toIso8601String(),
                'updatedAt' => $application->updated_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * API: Update application info (admin edits)
     * PATCH /api/admin/seller-applications/{applicationId}
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

        if ($application->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update an already approved application'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'storeName' => 'sometimes|string|max:255',
            'ownerMobile' => 'sometimes|string|max:15',
            'ownerEmail' => 'sometimes|email|max:255',
            'storeType' => 'sometimes|string|max:100',
            'storeAddress' => 'sometimes|string|max:500',
            'state' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'lat' => 'sometimes|numeric',
            'lng' => 'sometimes|numeric',
            'minBillAmount' => 'sometimes|numeric|min:0',
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
        if ($request->has('city'))
            $updateData['city'] = $request->city;
        if ($request->has('country'))
            $updateData['country'] = $request->country;
        if ($request->has('lat'))
            $updateData['lat'] = $request->lat;
        if ($request->has('lng'))
            $updateData['lng'] = $request->lng;
        if ($request->has('minBillAmount'))
            $updateData['min_bill_amount'] = $request->minBillAmount;

        if (!empty($updateData)) {
            $application->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application updated successfully',
            'data' => [
                'applicationId' => $application->application_id,
                'storeName' => $application->store_name,
                'ownerMobile' => $application->owner_mobile,
                'ownerEmail' => $application->owner_email,
                'storeType' => $application->store_type,
                'storeAddress' => $application->store_address,
                'state' => $application->state,
                'city' => $application->city,
                'country' => $application->country,
                'lat' => $application->lat,
                'lng' => $application->lng,
                'minBillAmount' => $application->min_bill_amount,
                'status' => $application->status,
            ]
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

            // Create shop
            $shopCode = 'SHOP-' . date('Y') . str_pad($seller->id, 4, '0', STR_PAD_LEFT);
            $shop = Shop::create([
                'seller_id' => $seller->id,
                'shop_code' => $shopCode,
                'shop_name' => $application->store_name,
                'owner_name' => $application->store_name,
                'phone' => $application->owner_mobile,
                'email' => $request->sellerEmail,
                'address' => $application->store_address,
                'state' => $application->state,
                'city' => $application->city,
                'country' => $application->country,
                'location_lat' => $application->lat,
                'location_lng' => $application->lng,
                'category' => $application->store_type,
                'min_bill_amount' => $application->min_bill_amount ?? 0,
            ]);

            if (!$shop || !$shop->id) {
                throw new \Exception('Failed to create shop');
            }

            // Create admin commission/discount settings for this shop
            // Use provided values or fallback to global defaults
            $globalSettings = AdminShopCommissionDiscount::whereNull('shop_id')->orderByDesc('id')->first();

            $commissionPercent = $request->has('commissionPercent') && $request->commissionPercent !== null
                ? $request->commissionPercent
                : ($globalSettings->commission_percent ?? 10);

            $discountPercent = $request->has('discountPercent') && $request->discountPercent !== null
                ? $request->discountPercent
                : ($globalSettings->discount_percent ?? 0);

            $rating = $request->has('rating') && $request->rating !== null
                ? $request->rating
                : ($globalSettings->rating ?? 0);

            AdminShopCommissionDiscount::create([
                'shop_id' => $shop->id,
                'commission_percent' => $commissionPercent,
                'discount_percent' => $discountPercent,
                'minimum_bill_amount' => $application->min_bill_amount ?? 0,
                'rating' => $rating,
                'total_ratings' => 0,
                'is_active' => true,
                'is_featured' => false,
                'last_updated_on' => now(),
            ]);

            // Update application
            $application->update([
                'status' => SellerApplication::STATUS_APPROVED,
                'approved_by' => Auth::guard('admin')->id() ?? $request->approvedBy,
                'seller_email' => $request->sellerEmail,
                'approved_at' => now(),
                'seller_id' => $seller->id,
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

            // Create shop
            $shopCode = 'SHOP-' . date('Y') . str_pad($seller->id, 4, '0', STR_PAD_LEFT);
            $shop = Shop::create([
                'seller_id' => $seller->id,
                'shop_code' => $shopCode,
                'shop_name' => $application->store_name,
                'owner_name' => $application->store_name,
                'phone' => $application->owner_mobile,
                'email' => $request->seller_email,
                'address' => $application->store_address,
                'state' => $application->state,
                'city' => $application->city,
                'country' => $application->country,
                'location_lat' => $application->lat,
                'location_lng' => $application->lng,
                'category' => $application->store_type,
                'min_bill_amount' => $application->min_bill_amount ?? 0,
            ]);

            if (!$shop || !$shop->id) {
                throw new \Exception('Failed to create shop');
            }

            // Create admin commission/discount settings for this shop
            // Use provided values or fallback to global defaults
            $globalSettings = AdminShopCommissionDiscount::whereNull('shop_id')->orderByDesc('id')->first();

            $commissionPercent = $request->has('commission_percent') && $request->commission_percent !== null
                ? $request->commission_percent
                : ($globalSettings->commission_percent ?? 10);

            $discountPercent = $request->has('discount_percent') && $request->discount_percent !== null
                ? $request->discount_percent
                : ($globalSettings->discount_percent ?? 0);

            $rating = $request->has('rating') && $request->rating !== null
                ? $request->rating
                : ($globalSettings->rating ?? 0);

            AdminShopCommissionDiscount::create([
                'shop_id' => $shop->id,
                'commission_percent' => $commissionPercent,
                'discount_percent' => $discountPercent,
                'minimum_bill_amount' => $application->min_bill_amount ?? 0,
                'rating' => $rating,
                'total_ratings' => 0,
                'is_active' => true,
                'is_featured' => false,
                'last_updated_on' => now(),
            ]);

            // Update application
            $application->update([
                'status' => SellerApplication::STATUS_APPROVED,
                'approved_by' => Auth::guard('admin')->id(),
                'seller_email' => $request->seller_email,
                'approved_at' => now(),
                'seller_id' => $seller->id,
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
}

