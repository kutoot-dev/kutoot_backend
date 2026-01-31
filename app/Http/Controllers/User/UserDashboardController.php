<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PurchasedCoins;
use App\Models\CoinLedger;
use App\Models\UserCoins;
use App\Models\NewsletterSubscription;
use App\Services\CoinLedgerService;
use Illuminate\Support\Facades\Validator;

class UserDashboardController extends Controller
{
    protected CoinLedgerService $coinLedgerService;

    public function __construct(CoinLedgerService $coinLedgerService)
    {
        $this->middleware('auth:api');
        $this->coinLedgerService = $coinLedgerService;
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
     * Uses CoinLedger system (single source of truth) with expiration-aware balances.
     */
    public function myDashboard()
    {
        $user = Auth::guard('api')->user();

        // Get balance breakdown from CoinLedger (expiration-aware)
        $breakdown = $this->coinLedgerService->getBalanceBreakdown($user->id);

        // Calculate total credits and debits from CoinLedger
        $creditCoins = CoinLedger::where('user_id', $user->id)
            ->where('coins_in', '>', 0)
            ->sum('coins_in');

        $debitCoins = CoinLedger::where('user_id', $user->id)
            ->where('coins_out', '>', 0)
            ->sum('coins_out');

        // Redemption count (number of redemption transactions)
        $redemptionCount = CoinLedger::where('user_id', $user->id)
            ->where('entry_type', CoinLedger::TYPE_REDEEM)
            ->count();

        // Get coin value from config
        $coinValueINR = config('coins.value_inr', 0.25);

        return response()->json([
            'success' => true,
            'message' => "Dashboard fetched successfully",
            'data' => [
                'balance_coins' => (int) $breakdown['total'],
                'credit_coins' => (int) $creditCoins,
                'debit_coins' => (int) $debitCoins,
                'paid_coins' => (int) $breakdown['paid'],
                'reward_coins' => (int) $breakdown['reward'],
                'coin_value_inr' => $coinValueINR,
                'redemption_count' => (int) $redemptionCount,
                'expiring_soon' => [
                    'coins' => (int) $breakdown['next_expiry']['coins'],
                    'date' => $breakdown['next_expiry']['date'],
                    'days_left' => $breakdown['next_expiry']['days_left'],
                ],
            ]
        ]);
    }

    /**
     * API 8: USER TRANSACTION HISTORY
     * GET /api/user/coin-transactions
     */
    public function coinTransactions(Request $request)
    {
        $user = Auth::guard('api')->user();
        $limit = $request->get('limit', 10);
        $type = $request->get('type'); // credit, debit, or null (all)

        $query = UserCoins::where('user_id', $user->id);

        if ($type && in_array($type, ['credit', 'debit'])) {
            $query->where('type', $type);
        }

        // Eager load if possible. Reference logic:
        // Debits link to Order or Transaction? Plan says we will use order_id for context.
        // Credits link to PurchasedCampaign?
        $query->orderBy('created_at', 'desc');

        $paginated = $query->paginate($limit);

        $transactions = $paginated->getCollection()->map(function ($item) {
            $title = "Transaction";
            $storeId = null;
            $transactionId = null;
            $amountINR = 0; // Derived if possible

            // Infer details based on type
            if ($item->type === 'credit') {
                $title = "Coins Added";
                // Could be "Allocated by Admin" or "Purchased"
                // Check if purchased_camp_id exists
                if ($item->purchased_camp_id) {
                    $title = "Coin Pack Purchased";
                }
                // Determine Amount INR? Hard to know exactly without price log, but maybe estimate:
                $amountINR = $item->coins * 0.25; // Approximate value
            } elseif ($item->type === 'debit') {
                $title = "Redeemed Coins";
                // If we linked transaction via order_id or similar
                // We will implement `confirm` in RedeemController to save transaction_id or store_id in `order_id` or a new field.
                // For now, let's assume order_id might hold the Transaction ID or Store ID?
                // Let's rely on standard logic.

                // If we have a Transaction model linked?
                // The current UserCoins model has `order_id` relation to Order.
                // But our Redemption uses Transaction model.
                // We should try to store Transaction ID in order_id if it fits (likely int so Transaction->id).

                if ($item->order_id) {
                    // Try finding transaction
                    $txn = \App\Models\Store\Transaction::with('sellerApplication')->find($item->order_id);
                    if ($txn) {
                        $title = "Redeemed at " . ($txn->sellerApplication->store_name ?? 'Store');
                        $storeId = $txn->seller_application_id;
                        $transactionId = $txn->txn_code;
                        $amountINR = $txn->discount_amount;
                    }
                }
            }

            return [
                'id' => $item->id,
                'title' => $title,
                'type' => $item->type,
                'coins' => $item->coins,
                'amount_inr' => round($amountINR, 2),
                'store_id' => $storeId,
                'transaction_id' => $transactionId,
                'date' => $item->created_at->format('Y-m-d'),
                'created_at' => $item->created_at->toIso8601String()
            ];
        });

        return response()->json([
            'success' => true,
            'message' => "Transactions fetched",
            'data' => [
                'pagination' => [
                    'page' => $paginated->currentPage(),
                    'limit' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ],
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * My Campaigns - Get all purchased campaigns
     */
    public function myCampaigns(Request $request)
    {
        $user = Auth::guard('api')->user();

        $campaigns = PurchasedCoins::with(['basedetails', 'coupons', 'campaign'])
            ->where('user_id', $user->id);

        // Filter by campaign status if provided
        if ($request->has('status') && $request->status !== null) {
            $campaigns->whereHas('campaign', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $campaigns = $campaigns->orderBy('created_at', 'desc')
            ->paginate(15);

        // Append coin_expires_at from CoinLedger for each purchase
        $campaigns->getCollection()->transform(function ($purchase) {
            $coinLedgerEntry = CoinLedger::where('reference_id', 'OID' . $purchase->id)
                ->where('entry_type', CoinLedger::TYPE_PAID_CREDIT)
                ->first();
            $purchase->coin_expires_at = $coinLedgerEntry ? $coinLedgerEntry->expiry_date : null;
            return $purchase;
        });

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
