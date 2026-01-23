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

    // =========================================================================
    // ADMIN DASHBOARD METHODS
    // =========================================================================

    /**
     * Get comprehensive admin summary for coin ledger dashboard.
     */
    public function getAdminSummary(): array
    {
        // Outstanding PAID coins (liability) - unexpired only
        $paidLiability = CoinLedger::where('coin_category', CoinLedger::CAT_PAID)
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->selectRaw('COALESCE(SUM(coins_in), 0) - COALESCE(SUM(coins_out), 0) as balance')
            ->value('balance') ?? 0;

        // Outstanding REWARD coins (marketing liability) - unexpired only
        $rewardLiability = CoinLedger::where('coin_category', CoinLedger::CAT_REWARD)
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->selectRaw('COALESCE(SUM(coins_in), 0) - COALESCE(SUM(coins_out), 0) as balance')
            ->value('balance') ?? 0;

        // Total credits all time
        $totalPaidCredits = CoinLedger::where('coin_category', CoinLedger::CAT_PAID)
            ->where('coins_in', '>', 0)
            ->sum('coins_in');

        $totalRewardCredits = CoinLedger::where('coin_category', CoinLedger::CAT_REWARD)
            ->where('coins_in', '>', 0)
            ->sum('coins_in');

        // Total redemptions all time
        $totalPaidRedeemed = CoinLedger::where('coin_category', CoinLedger::CAT_PAID)
            ->where('entry_type', CoinLedger::TYPE_REDEEM)
            ->sum('coins_out');

        $totalRewardRedeemed = CoinLedger::where('coin_category', CoinLedger::CAT_REWARD)
            ->where('entry_type', CoinLedger::TYPE_REDEEM)
            ->sum('coins_out');

        // Total expired all time
        $totalPaidExpired = CoinLedger::where('coin_category', CoinLedger::CAT_PAID)
            ->where('entry_type', CoinLedger::TYPE_EXPIRE)
            ->sum('coins_out');

        $totalRewardExpired = CoinLedger::where('coin_category', CoinLedger::CAT_REWARD)
            ->where('entry_type', CoinLedger::TYPE_EXPIRE)
            ->sum('coins_out');

        // Today's activity
        $todayCredits = CoinLedger::whereDate('created_at', today())
            ->where('coins_in', '>', 0)
            ->sum('coins_in');

        $todayRedemptions = CoinLedger::whereDate('created_at', today())
            ->where('entry_type', CoinLedger::TYPE_REDEEM)
            ->sum('coins_out');

        // This month's activity
        $monthCredits = CoinLedger::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('coins_in', '>', 0)
            ->sum('coins_in');

        $monthRedemptions = CoinLedger::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('entry_type', CoinLedger::TYPE_REDEEM)
            ->sum('coins_out');

        $monthExpired = CoinLedger::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('entry_type', CoinLedger::TYPE_EXPIRE)
            ->sum('coins_out');

        // Active users with coins
        $activeUsers = CoinLedger::distinct('user_id')->count('user_id');

        // Users with positive balance
        $usersWithBalance = DB::table('coin_ledger')
            ->select('user_id')
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->groupBy('user_id')
            ->havingRaw('SUM(coins_in) - SUM(coins_out) > 0')
            ->count();

        return [
            'outstanding' => [
                'paid_liability' => $paidLiability,
                'reward_liability' => $rewardLiability,
                'total_liability' => $paidLiability + $rewardLiability,
            ],
            'all_time' => [
                'paid_credits' => $totalPaidCredits,
                'reward_credits' => $totalRewardCredits,
                'paid_redeemed' => $totalPaidRedeemed,
                'reward_redeemed' => $totalRewardRedeemed,
                'paid_expired' => $totalPaidExpired,
                'reward_expired' => $totalRewardExpired,
            ],
            'today' => [
                'credits' => $todayCredits,
                'redemptions' => $todayRedemptions,
            ],
            'this_month' => [
                'credits' => $monthCredits,
                'redemptions' => $monthRedemptions,
                'expired' => $monthExpired,
            ],
            'users' => [
                'total_active' => $activeUsers,
                'with_balance' => $usersWithBalance,
            ],
        ];
    }

    /**
     * Get ledger entries query with optional filters for admin listing.
     */
    public function getLedgerQuery(array $filters = [])
    {
        $query = CoinLedger::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by entry type
        if (!empty($filters['entry_type'])) {
            $query->where('entry_type', $filters['entry_type']);
        }

        // Filter by category
        if (!empty($filters['coin_category'])) {
            $query->where('coin_category', $filters['coin_category']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter by reference ID
        if (!empty($filters['reference_id'])) {
            $query->where('reference_id', 'like', '%' . $filters['reference_id'] . '%');
        }

        // Search by user name/email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Get daily coin flow for the last N days (for charts).
     */
    public function getDailyFlow(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();

        $credits = CoinLedger::where('created_at', '>=', $startDate)
            ->where('coins_in', '>', 0)
            ->selectRaw('DATE(created_at) as date, coin_category, SUM(coins_in) as total')
            ->groupBy('date', 'coin_category')
            ->get()
            ->groupBy('date');

        $redemptions = CoinLedger::where('created_at', '>=', $startDate)
            ->where('entry_type', CoinLedger::TYPE_REDEEM)
            ->selectRaw('DATE(created_at) as date, coin_category, SUM(coins_out) as total')
            ->groupBy('date', 'coin_category')
            ->get()
            ->groupBy('date');

        $flow = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayCredits = $credits->get($date, collect());
            $dayRedemptions = $redemptions->get($date, collect());

            $flow[] = [
                'date' => $date,
                'paid_credits' => $dayCredits->where('coin_category', CoinLedger::CAT_PAID)->sum('total'),
                'reward_credits' => $dayCredits->where('coin_category', CoinLedger::CAT_REWARD)->sum('total'),
                'paid_redeemed' => $dayRedemptions->where('coin_category', CoinLedger::CAT_PAID)->sum('total'),
                'reward_redeemed' => $dayRedemptions->where('coin_category', CoinLedger::CAT_REWARD)->sum('total'),
            ];
        }

        return $flow;
    }

    /**
     * Get Zoho account type mapping for a ledger entry.
     */
    public static function getZohoAccountType(CoinLedger $entry): string
    {
        $category = $entry->coin_category;
        $type = $entry->entry_type;

        // Credit entries (liability creation)
        if ($entry->coins_in > 0) {
            return $category === CoinLedger::CAT_PAID ? 'Coin Liability' : 'Marketing Liability';
        }

        // Redemption entries (liability squaring + expense recognition)
        if ($type === CoinLedger::TYPE_REDEEM) {
            return $category === CoinLedger::CAT_PAID ? 'Discount Expense' : 'Marketing Expense';
        }

        // Expiry entries (liability write-off)
        if ($type === CoinLedger::TYPE_EXPIRE) {
            return 'Liability Write-off';
        }

        // Reversal entries
        if ($type === CoinLedger::TYPE_REVERSAL) {
            return 'Reversal Adjustment';
        }

        return 'Unknown';
    }
}
