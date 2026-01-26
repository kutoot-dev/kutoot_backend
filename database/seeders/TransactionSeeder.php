<?php

namespace Database\Seeders;

use App\Models\CoinLedger;
use App\Models\Store\Seller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * TransactionSeeder - Seeds bulk transactions for testing.
 *
 * Creates 1000 transactions across 80 users with realistic coin operations.
 * Uses the global Zoho-compatible CoinLedger system.
 * DEV ONLY - For populating test data.
 */
class TransactionSeeder extends Seeder
{
    private function coinValue(): float
    {
        return (float) config('kutoot.coin_value', 0.25);
    }

    private function getUserCoinBalance(int $userId): int
    {
        $ledger = CoinLedger::selectRaw("
                SUM(coins_in) as total_in,
                SUM(coins_out) as total_out
            ")
            ->where('user_id', $userId)
            ->available() // Only unexpired coins
            ->first();

        $totalIn = (int) ($ledger->total_in ?? 0);
        $totalOut = (int) ($ledger->total_out ?? 0);
        return max(0, $totalIn - $totalOut);
    }

    public function run(): void
    {
        $seller = Seller::query()->where('seller_code', 'SELLER001')->first();
        if (!$seller || !$seller->application || $seller->application->status !== 'APPROVED') {
            $this->command->warn('Seller SELLER001 or their approved application not found. Skipping TransactionSeeder.');
            return;
        }

        $application = $seller->application;

        $discountPercent = (float) ($application->discount_percent ?? 0);
        $minimumBillAmount = (float) ($application->min_bill_amount ?? 0);

        $start = strtotime('2026-01-01');
        $end = strtotime('2026-12-31');

        $userCount = 80;
        $totalTargetTxns = 1000;
        $basePerUser = 10;
        $maxExtraPerUser = 5;
        $extraToDistribute = $totalTargetTxns - ($userCount * $basePerUser);

        $extras = array_fill(1, $userCount, 0);
        while ($extraToDistribute > 0) {
            $u = mt_rand(1, $userCount);
            if ($extras[$u] < $maxExtraPerUser) {
                $extras[$u]++;
                $extraToDistribute--;
            }
        }

        $txnSeq = 1;
        $totalTransactions = 0;

        for ($u = 1; $u <= $userCount; $u++) {
            $user = User::query()->updateOrCreate(
                ['email' => "visitor{$u}@demo.com"],
                [
                    'name' => 'Visitor ' . $u,
                    'password' => Hash::make('123456'),
                ]
            );

            $txnCount = $basePerUser + ($extras[$u] ?? 0);

            $txns = [];
            $totalCoinsNeededForUser = 0;

            for ($k = 1; $k <= $txnCount; $k++) {
                $visitedOnTs = mt_rand($start, $end);
                $visitedOn = date('Y-m-d', $visitedOnTs);
                $visitedAt = date('Y-m-d H:i:s', $visitedOnTs + mt_rand(0, 86399));
                $totalAmount = mt_rand(200, 5000);

                $txnCode = 'TXN' . str_pad((string) (2000 + $txnSeq), 6, '0', STR_PAD_LEFT);
                $txnSeq++;

                $aboveMin = ($minimumBillAmount <= 0) || ($totalAmount >= $minimumBillAmount);
                $status = $aboveMin ? 'SUCCESS' : (mt_rand(1, 100) <= 92 ? 'SUCCESS' : 'FAILED');

                $attemptRedeem = $aboveMin && $status === 'SUCCESS' && $discountPercent > 0;
                $coinValue = $this->coinValue();
                $maxDiscountAmount = $attemptRedeem ? round($totalAmount * ($discountPercent / 100), 2) : 0.0;
                $coinsNeeded = $attemptRedeem ? (int) floor($maxDiscountAmount / $coinValue) : 0;
                $totalCoinsNeededForUser += $coinsNeeded;

                $txns[] = compact('txnCode', 'visitedOn', 'visitedAt', 'totalAmount', 'aboveMin', 'status', 'coinsNeeded');
            }

            // Credit coins to user via CoinLedger (Zoho-compatible)
            CoinLedger::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'entry_type' => CoinLedger::TYPE_PAID_CREDIT,
                    'reference_id' => 'SEED_BULK_' . $user->id,
                ],
                [
                    'coins_in' => max(0, $totalCoinsNeededForUser) + mt_rand(500, 2500),
                    'coins_out' => 0,
                    'coin_category' => CoinLedger::CAT_PAID,
                    'expiry_date' => '2099-12-31 00:00:00',
                    'metadata' => json_encode(['source' => 'transaction_seeder', 'user_id' => $user->id]),
                ]
            );

            foreach ($txns as $t) {
                $txnCode = $t['txnCode'];
                $visitedOn = $t['visitedOn'];
                $visitedAt = $t['visitedAt'];
                $totalAmount = (int) $t['totalAmount'];
                $aboveMin = (bool) $t['aboveMin'];
                $status = (string) $t['status'];
                $coinsNeeded = (int) $t['coinsNeeded'];

                $attemptRedeem = $aboveMin && $status === 'SUCCESS' && $discountPercent > 0;
                $availableCoins = $attemptRedeem ? $this->getUserCoinBalance($user->id) : 0;
                $redeemedCoins = ($attemptRedeem && $coinsNeeded > 0) ? min($availableCoins, $coinsNeeded) : 0;
                $coinValue = $this->coinValue();
                $discountAmount = $redeemedCoins > 0 ? round($redeemedCoins * $coinValue, 2) : 0.0;
                $redeemed = $redeemedCoins > 0;

                $existingTxn = Transaction::query()->where('txn_code', $txnCode)->first();
                $visitor = $existingTxn?->visitor_id ? ShopVisitor::query()->find($existingTxn->visitor_id) : null;
                if (!$visitor) {
                    $visitor = new ShopVisitor();
                }
                $visitor->seller_application_id = $application->id;
                $visitor->user_id = $user->id;
                $visitor->visited_on = $visitedOn;
                $visitor->redeemed = $redeemed;
                $visitor->created_at = $visitedAt;
                $visitor->updated_at = $visitedAt;
                $visitor->save();

                $tx = Transaction::query()->updateOrCreate(
                    ['txn_code' => $txnCode],
                    [
                        'seller_application_id' => $application->id,
                        'visitor_id' => $visitor->id,
                        'total_amount' => $totalAmount,
                        'discount_amount' => $discountAmount,
                        'redeemed_coins' => $redeemedCoins,
                        'status' => $status,
                        'settled_at' => $visitedOn,
                        'created_at' => $visitedAt,
                        'updated_at' => $visitedAt,
                    ]
                );

                // Record coin redemption in ledger
                if ($redeemedCoins > 0) {
                    CoinLedger::query()->updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'entry_type' => CoinLedger::TYPE_REDEEM,
                            'reference_id' => 'TXN_' . $tx->id,
                        ],
                        [
                            'coins_in' => 0,
                            'coins_out' => $redeemedCoins,
                            'coin_category' => CoinLedger::CAT_PAID,
                            'expiry_date' => null,
                            'metadata' => json_encode([
                                'source' => 'store_redemption',
                                'seller_application_id' => $application->id,
                                'transaction_id' => $tx->id,
                                'txn_code' => $txnCode,
                            ]),
                        ]
                    );
                }

                $totalTransactions++;
            }
        }

        $this->command->info("TransactionSeeder completed. {$totalTransactions} transactions seeded across {$userCount} users.");
    }
}
