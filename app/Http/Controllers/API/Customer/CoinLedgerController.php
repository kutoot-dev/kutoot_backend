<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Models\CoinLedger;
use App\Services\CoinLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoinLedgerController extends Controller
{
    protected $service;

    public function __construct(CoinLedgerService $service)
    {
        $this->service = $service;
    }

    /**
     * Get wallet balance and breakdown.
     */
    public function getWallet(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $breakdown = $this->service->getBalanceBreakdown($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $breakdown['total'],
                'paid_coins' => $breakdown['paid'],
                'reward_coins' => $breakdown['reward'],
            ]
        ]);
    }

    /**
     * Get coin transaction history.
     */
    public function getHistory(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $limit = $request->get('limit', 15);
        $paginated = CoinLedger::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        $transactions = collect($paginated->items())->map(function ($item) {
            $title = "Coin Transaction";
            $amountINR = 0;
            $type = 'unknown';

            switch ($item->entry_type) {
                case CoinLedger::TYPE_PAID_CREDIT:
                    $title = "Coin Pack Purchased";
                    $type = "credit";
                    // metadata might have price?
                    $amountINR = $item->metadata['price'] ?? 0;
                    break;
                case CoinLedger::TYPE_REWARD_CREDIT:
                    $title = "Reward Coins Added";
                    $type = "credit";
                    break;
                case CoinLedger::TYPE_REDEEM:
                    $title = "Redeemed at Store";
                    $type = "debit";
                    // If we have store info?
                    break;
                case CoinLedger::TYPE_EXPIRE:
                    $title = "Coins Expired";
                    $type = "debit";
                    break;
                case CoinLedger::TYPE_REVERSAL:
                    $title = "Coins Reversed";
                    $type = $item->coins_in > 0 ? "credit" : "debit";
                    break;
            }

            return [
                'id' => $item->id,
                'title' => $title,
                'type' => $type,
                'coins_in' => $item->coins_in,
                'coins_out' => $item->coins_out,
                'coins' => $item->coins_in > 0 ? $item->coins_in : $item->coins_out,
                'category' => $item->coin_category,
                'date' => $item->created_at->format('Y-m-d'),
                'created_at' => $item->created_at->toIso8601String(),
                'metadata' => $item->metadata,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ],
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * Admin: Manual credit coins to user.
     * This would typically be in an Admin-scoped controller.
     */
    public function adminCredit(Request $request)
    {
        // Require admin auth - typically handled by middleware 'auth:admin-api'
        // For now, simple check or assume middleware

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'category' => 'required|in:PAID,REWARD',
            'type' => 'required|string',
            'expiry_days' => 'nullable|integer',
            'reference_id' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $entry = $this->service->credit(
            $request->user_id,
            $request->amount,
            $request->category,
            $request->type,
            $request->reference_id,
            $request->expiry_days ?? 100
        );

        if ($request->has('metadata')) {
            $entry->metadata = array_merge($entry->metadata ?? [], $request->metadata);
            $entry->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Coins credited successfully',
            'data' => $entry
        ]);
    }
}
