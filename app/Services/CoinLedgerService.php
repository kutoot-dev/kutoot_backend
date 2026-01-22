<?php

namespace App\Services;

use App\Models\CoinLedger;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CoinLedgerService
{
    /**
     * Credit coins to a user.
     */
    public function credit(int $userId, int $amount, string $category, string $type, ?string $refId = null, ?int $expiryDays = 100)
    {
        return CoinLedger::create([
            'user_id' => $userId,
            'entry_type' => $type,
            'coins_in' => $amount,
            'coins_out' => 0,
            'coin_category' => $category,
            'expiry_date' => $expiryDays ? now()->addDays($expiryDays) : null,
            'reference_id' => $refId,
        ]);
    }

    /**
     * Helper for Paid Coins Credit
     */
    public function creditPaid(int $userId, int $amount, ?string $refId = null)
    {
        return $this->credit($userId, $amount, CoinLedger::CAT_PAID, CoinLedger::TYPE_PAID_CREDIT, $refId);
    }

    /**
     * Helper for Reward Coins Credit
     */
    public function creditReward(int $userId, int $amount, ?string $refId = null)
    {
        return $this->credit($userId, $amount, CoinLedger::CAT_REWARD, CoinLedger::TYPE_REWARD_CREDIT, $refId);
    }

    /**
     * Get unexpired, unconsumed credit entries for a user, ordered by expiry (soonest first).
     */
    public function getAvailableCreditEntries(int $userId)
    {
        // To accurately calculate "unconsumed", we need to look at redeems/expires linked to these entries.
        // We'll use the 'source_id' in metadata to track consumption.

        $credits = CoinLedger::where('user_id', $userId)
            ->where('coins_in', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('expiry_date', 'asc')
            ->get();

        $availableEntries = [];

        foreach ($credits as $credit) {
            $consumed = CoinLedger::where('user_id', $userId)
                ->where('coins_out', '>', 0)
                ->where('metadata->source_id', $credit->id)
                ->sum('coins_out');

            $remaining = $credit->coins_in - $consumed;

            if ($remaining > 0) {
                $credit->remaining = $remaining;
                $availableEntries[] = $credit;
            }
        }

        return $availableEntries;
    }

    /**
     * Redeem coins using FIFO logic.
     */
    public function redeem(int $userId, int $amount, string $refId)
    {
        return DB::transaction(function () use ($userId, $amount, $refId) {
            $totalAvailable = User::find($userId)->wallet_balance;

            if ($totalAvailable < $amount) {
                throw new \Exception("Insufficient coins. Available: {$totalAvailable}, Required: {$amount}");
            }

            $availableEntries = $this->getAvailableCreditEntries($userId);
            $remainingToRedeem = $amount;

            foreach ($availableEntries as $entry) {
                if ($remainingToRedeem <= 0)
                    break;

                $take = min($remainingToRedeem, $entry->remaining);

                CoinLedger::create([
                    'user_id' => $userId,
                    'entry_type' => CoinLedger::TYPE_REDEEM,
                    'coins_in' => 0,
                    'coins_out' => $take,
                    'coin_category' => $entry->coin_category,
                    'expiry_date' => $entry->expiry_date,
                    'reference_id' => $refId,
                    'metadata' => ['source_id' => $entry->id]
                ]);

                $remainingToRedeem -= $take;
            }

            return true;
        });
    }

    /**
     * Get wallet balance breakdown.
     */
    public function getBalanceBreakdown(int $userId)
    {
        $paidIn = CoinLedger::where('user_id', $userId)->where('coin_category', CoinLedger::CAT_PAID)->where('coins_in', '>', 0)->sum('coins_in');
        $paidOut = CoinLedger::where('user_id', $userId)->where('coin_category', CoinLedger::CAT_PAID)->where('coins_out', '>', 0)->sum('coins_out');

        $rewardIn = CoinLedger::where('user_id', $userId)->where('coin_category', CoinLedger::CAT_REWARD)->where('coins_in', '>', 0)->sum('coins_in');
        $rewardOut = CoinLedger::where('user_id', $userId)->where('coin_category', CoinLedger::CAT_REWARD)->where('coins_out', '>', 0)->sum('coins_out');

        return [
            'total' => ($paidIn + $rewardIn) - ($paidOut + $rewardOut),
            'paid' => $paidIn - $paidOut,
            'reward' => $rewardIn - $rewardOut,
        ];
    }

    /**
     * Handle expiration of specific entries.
     */
    public function expireEntry(CoinLedger $entry)
    {
        $consumed = CoinLedger::where('user_id', $entry->user_id)
            ->where('coins_out', '>', 0)
            ->where('metadata->source_id', $entry->id)
            ->sum('coins_out');

        $remaining = $entry->coins_in - $consumed;

        if ($remaining > 0) {
            return CoinLedger::create([
                'user_id' => $entry->user_id,
                'entry_type' => CoinLedger::TYPE_EXPIRE,
                'coins_in' => 0,
                'coins_out' => $remaining,
                'coin_category' => $entry->coin_category,
                'expiry_date' => $entry->expiry_date,
                'metadata' => ['source_id' => $entry->id]
            ]);
        }

        return null;
    }
    /**
     * Reverse a previous coin operation.
     * If reversing a redeem, coins_in will be positive.
     * If reversing a credit, coins_out will be positive.
     */
    public function reverse(int $userId, int $amount, string $direction, string $category, string $refId, array $metadata = [])
    {
        return CoinLedger::create([
            'user_id' => $userId,
            'entry_type' => CoinLedger::TYPE_REVERSAL,
            'coins_in' => $direction === 'in' ? $amount : 0,
            'coins_out' => $direction === 'out' ? $amount : 0,
            'coin_category' => $category,
            'reference_id' => $refId,
            'metadata' => $metadata,
        ]);
    }
}
