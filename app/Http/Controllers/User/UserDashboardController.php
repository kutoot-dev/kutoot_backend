<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\PurchasedCoins;
use App\Models\UserCoins;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Validator;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribeNewsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscriptions,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subscription = NewsletterSubscription::create([
            'email' => $request->email,
            'status' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Successfully subscribed to newsletter',
            'data' => $subscription
        ], 201);
    }

    /**
     * My Dashboard - Get user statistics
     */
    public function myDashboard()
    {
        $user = Auth::guard('api')->user();

        // Total purchased campaigns count
        $totalCampaigns = PurchasedCoins::where('user_id', $user->id)
            ->where('payment_status', 'success')
            ->count();

        // Credit and debit coins
        $coins = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $user->id)
            ->whereDate('coin_expires', '>=', now()->toDateString())
            ->first();

        $creditCoins = $coins->credit ?? 0;
        $debitCoins = $coins->debit ?? 0;
        $balanceCoins = $creditCoins - $debitCoins;

        // Recent orders (latest 5 purchased campaigns)
        $recentOrders = PurchasedCoins::with(['basedetails', 'coupons'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'total_campaigns' => $totalCampaigns,
                'credit_coins' => intval($creditCoins),
                'debit_coins' => intval($debitCoins),
                'balance_coins' => intval($balanceCoins),
                'recent_orders' => $recentOrders
            ]
        ]);
    }

    /**
     * My Campaigns - Get all purchased campaigns
     */
    public function myCampaigns(Request $request)
    {
        $user = Auth::guard('api')->user();

        $campaigns = PurchasedCoins::with(['basedetails', 'coupons'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $campaigns
        ]);
    }

    /**
     * Deactivate user account
     */
    public function deactivateAccount()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Update user status to inactive
        $user->status = 0;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Account deactivated successfully'
        ]);
    }
}
