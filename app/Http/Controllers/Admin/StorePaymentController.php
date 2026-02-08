<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorePayment;
use Illuminate\Http\Request;

/**
 * @group Admin - Store Payments
 *
 * APIs for managing direct store payments (hybrid coin + gateway payments)
 */
class StorePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    /**
     * List all store payments
     *
     * Returns paginated list of all store payments with user and store info.
     *
     * @queryParam status string Filter by status (pending, completed, failed). Example: completed
     * @queryParam store_id integer Filter by store ID (seller_applications.id). Example: 5
     * @queryParam razorpay_account_id string Filter by Razorpay account. Example: acc_ABC123
     * @queryParam user_id integer Filter by user ID. Example: 10
     * @queryParam date_from string Filter by start date (Y-m-d). Example: 2025-01-01
     * @queryParam date_to string Filter by end date (Y-m-d). Example: 2025-12-31
     * @queryParam per_page integer Results per page (default 20, max 100). Example: 20
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = StorePayment::with(['user:id,name,email,phone', 'store:id,store_name,store_image'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by Razorpay account
        if ($request->filled('razorpay_account_id')) {
            $query->where('razorpay_account_id', $request->razorpay_account_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = min((int) ($request->per_page ?? 20), 100);
        $payments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'title' => 'Store Payments',
            'data' => $payments,
        ]);
    }

    /**
     * Get pending store payments
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pending(Request $request)
    {
        $payments = StorePayment::with(['user:id,name,email', 'store:id,store_name'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'title' => 'Pending Store Payments',
            'data' => $payments,
        ]);
    }

    /**
     * Get completed store payments
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function completed(Request $request)
    {
        $payments = StorePayment::with(['user:id,name,email', 'store:id,store_name'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'title' => 'Completed Store Payments',
            'data' => $payments,
        ]);
    }

    /**
     * Get failed store payments
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failed(Request $request)
    {
        $payments = StorePayment::with(['user:id,name,email', 'store:id,store_name'])
            ->where('status', 'failed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'title' => 'Failed Store Payments',
            'data' => $payments,
        ]);
    }

    /**
     * Get single payment details
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $payment = StorePayment::with([
            'user:id,name,email,phone',
            'store:id,store_name,store_address,owner_mobile,owner_email,store_image,razorpay_account_id'
        ])->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }

    /**
     * Get payments by store
     *
     * @param int $storeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byStore($storeId)
    {
        $payments = StorePayment::with('user:id,name,email')
            ->where('store_id', $storeId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate store totals
        $totals = StorePayment::where('store_id', $storeId)
            ->where('status', 'completed')
            ->selectRaw('
                COUNT(*) as total_transactions,
                SUM(total_amount) as total_collected,
                SUM(coins_value) as total_coins_value,
                SUM(gateway_amount) as total_gateway_amount,
                SUM(platform_fee) as total_platform_fee,
                SUM(gst_on_fee) as total_gst,
                SUM(store_net_amount) as total_store_net,
                SUM(platform_debt) as total_platform_debt
            ')
            ->first();

        return response()->json([
            'success' => true,
            'title' => 'Store Payments',
            'store_id' => (int) $storeId,
            'totals' => $totals,
            'data' => $payments,
        ]);
    }

    /**
     * Get payments by Razorpay account
     *
     * Useful when multiple stores share the same Razorpay account.
     *
     * @param string $accountId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byRazorpayAccount($accountId)
    {
        $payments = StorePayment::with(['user:id,name,email', 'store:id,store_name'])
            ->where('razorpay_account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate totals for this Razorpay account
        $totals = StorePayment::where('razorpay_account_id', $accountId)
            ->where('status', 'completed')
            ->selectRaw('
                COUNT(*) as total_transactions,
                COUNT(DISTINCT store_id) as stores_count,
                SUM(total_amount) as total_collected,
                SUM(coins_value) as total_coins_value,
                SUM(gateway_amount) as total_gateway_amount,
                SUM(platform_fee) as total_platform_fee,
                SUM(gst_on_fee) as total_gst,
                SUM(store_net_amount) as total_store_net,
                SUM(platform_debt) as total_platform_debt
            ')
            ->first();

        return response()->json([
            'success' => true,
            'title' => 'Payments by Razorpay Account',
            'razorpay_account_id' => $accountId,
            'totals' => $totals,
            'data' => $payments,
        ]);
    }

    /**
     * Get payment statistics summary
     *
     * Returns overall stats for store payments.
     *
     * @queryParam date_from string Filter by start date (Y-m-d). Example: 2025-01-01
     * @queryParam date_to string Filter by end date (Y-m-d). Example: 2025-12-31
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary(Request $request)
    {
        $query = StorePayment::query();

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Overall stats
        $overall = (clone $query)->selectRaw('
            COUNT(*) as total_transactions,
            COUNT(DISTINCT store_id) as unique_stores,
            COUNT(DISTINCT user_id) as unique_users,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count
        ')->first();

        // Financial stats (completed only)
        $financial = (clone $query)->where('status', 'completed')
            ->selectRaw('
                SUM(total_amount) as total_collected,
                SUM(coins_value) as total_coins_redeemed_value,
                SUM(coins_redeemed) as total_coins_redeemed,
                SUM(gateway_amount) as total_gateway_amount,
                SUM(platform_fee) as total_platform_fee,
                SUM(gst_on_fee) as total_gst_collected,
                SUM(store_net_amount) as total_paid_to_stores,
                SUM(platform_debt) as total_platform_debt
            ')
            ->first();

        // Top stores by transaction count
        $topStores = (clone $query)->where('status', 'completed')
            ->select('store_id', 'store_name')
            ->selectRaw('COUNT(*) as transaction_count, SUM(total_amount) as total_amount')
            ->groupBy('store_id', 'store_name')
            ->orderByDesc('transaction_count')
            ->limit(10)
            ->get();

        // Razorpay accounts with outstanding platform debt
        $pendingSettlements = StorePayment::where('status', 'completed')
            ->whereNotNull('razorpay_account_id')
            ->where('platform_debt', '>', 0)
            ->select('razorpay_account_id')
            ->selectRaw('COUNT(DISTINCT store_id) as stores, SUM(platform_debt) as total_debt')
            ->groupBy('razorpay_account_id')
            ->orderByDesc('total_debt')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'title' => 'Store Payments Summary',
            'data' => [
                'overall' => $overall,
                'financial' => $financial,
                'top_stores' => $topStores,
                'pending_settlements' => $pendingSettlements,
            ],
        ]);
    }

    /**
     * Export store payments
     *
     * @queryParam status string Filter by status. Example: completed
     * @queryParam store_id integer Filter by store ID. Example: 5
     * @queryParam date_from string Filter by start date. Example: 2025-01-01
     * @queryParam date_to string Filter by end date. Example: 2025-12-31
     * @queryParam format string Export format (json, csv). Example: json
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $query = StorePayment::with(['user:id,name,email', 'store:id,store_name'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Limit to prevent memory issues
        $payments = $query->limit(5000)->get();

        // Transform for export
        $exportData = $payments->map(function ($payment) {
            return [
                'payment_id' => $payment->payment_id,
                'date' => $payment->created_at->format('Y-m-d H:i:s'),
                'user_name' => $payment->user->name ?? 'N/A',
                'user_email' => $payment->user->email ?? 'N/A',
                'store_name' => $payment->store_name,
                'store_id' => $payment->store_id,
                'razorpay_account_id' => $payment->razorpay_account_id,
                'total_amount' => $payment->total_amount,
                'coins_redeemed' => $payment->coins_redeemed,
                'coins_value' => $payment->coins_value,
                'gateway_amount' => $payment->gateway_amount,
                'platform_fee' => $payment->platform_fee,
                'gst_on_fee' => $payment->gst_on_fee,
                'store_net_amount' => $payment->store_net_amount,
                'platform_debt' => $payment->platform_debt,
                'payment_mode' => $payment->payment_mode,
                'status' => $payment->status,
                'razorpay_payment_id' => $payment->razorpay_payment_id,
                'razorpay_transfer_id' => $payment->razorpay_transfer_id,
            ];
        });

        return response()->json([
            'success' => true,
            'title' => 'Store Payments Export',
            'count' => $payments->count(),
            'data' => $exportData,
        ]);
    }
}
