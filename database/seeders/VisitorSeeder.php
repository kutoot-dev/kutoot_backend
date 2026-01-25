<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CoinLedger;
use App\Models\Store\Seller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use App\Models\Store\AdminShopCommissionDiscount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * VisitorSeeder - Seeds demo shop visitors and transactions.
 *
 * Creates test users with coin balances (via CoinLedger) and transaction records.
 * Uses the global Zoho-compatible coin ledger system.
 */
class VisitorSeeder extends Seeder
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
        if (!$seller || !$seller->shop) {
            $this->command->warn('Seller SELLER001 or their shop not found. Skipping VisitorSeeder.');
            return;
        }

        $shop = $seller->shop;

        $master = AdminShopCommissionDiscount::resolveForShop($shop->id);
        $minimumBillAmount = (float) ($master?->minimum_bill_amount ?? 0);
        $discountPercent = (float) ($master?->discount_percent ?? 0);

        $rows = [
            [
                'user_name' => 'Rahul Sharma',
                'user_email' => 'rahul@demo.com',
                'visited_on' => '2026-01-10',
                'txn_code' => 'TXN1001',
                'total_amount' => 1200,
                'status' => 'SUCCESS',
            ],
            [
                'user_name' => 'Anjali Singh',
                'user_email' => 'anjali@demo.com',
                'visited_on' => '2026-01-11',
                'txn_code' => 'TXN1002',
                'total_amount' => 1100,
                'status' => 'SUCCESS',
            ],
            [
                'user_name' => 'Vikram Patel',
                'user_email' => 'vikram@demo.com',
                'visited_on' => '2026-01-12',
                'txn_code' => 'TXN1003',
                'total_amount' => 1500,
                'status' => 'SUCCESS',
            ],
        ];

        $count = 0;

        foreach ($rows as $row) {
            $user = User::query()->updateOrCreate(
                ['email' => $row['user_email']],
                [
                    'name' => $row['user_name'],
                    'password' => Hash::make('123456'),
                ]
            );

            $aboveMin = ($minimumBillAmount <= 0) || ((float) $row['total_amount'] >= $minimumBillAmount);
            $status = $aboveMin ? 'SUCCESS' : (string) $row['status'];
            $attemptRedeem = $aboveMin && $status === 'SUCCESS' && $discountPercent > 0;

            $coinValue = $this->coinValue();
            $maxDiscountAmount = $attemptRedeem ? round(((float) $row['total_amount']) * ($discountPercent / 100), 2) : 0.0;
            $coinsNeeded = $attemptRedeem ? (int) floor($maxDiscountAmount / $coinValue) : 0;

            // Credit coins to user via CoinLedger (Zoho-compatible)
            CoinLedger::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'entry_type' => CoinLedger::TYPE_PAID_CREDIT,
                    'reference_id' => 'SEED_' . $user->id,
                ],
                [
                    'coins_in' => max(5000, $coinsNeeded + 1000),
                    'coins_out' => 0,
                    'coin_category' => CoinLedger::CAT_PAID,
                    'expiry_date' => '2099-12-31 00:00:00',
                    'metadata' => json_encode(['source' => 'seeder', 'user_email' => $row['user_email']]),
                ]
            );

            $availableCoins = $attemptRedeem ? $this->getUserCoinBalance($user->id) : 0;
            $redeemedCoins = ($attemptRedeem && $coinsNeeded > 0) ? min($availableCoins, $coinsNeeded) : 0;
            $discountAmount = $redeemedCoins > 0 ? round($redeemedCoins * $coinValue, 2) : 0.0;
            $redeemed = $redeemedCoins > 0;

            $visitor = ShopVisitor::query()->updateOrCreate(
                ['shop_id' => $shop->id, 'user_id' => $user->id, 'visited_on' => $row['visited_on']],
                [
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'visited_on' => $row['visited_on'],
                    'redeemed' => $redeemed,
                ]
            );

            $tx = Transaction::query()->updateOrCreate(
                ['txn_code' => $row['txn_code']],
                [
                    'shop_id' => $shop->id,
                    'visitor_id' => $visitor->id,
                    'total_amount' => $row['total_amount'],
                    'discount_amount' => $discountAmount,
                    'redeemed_coins' => $redeemedCoins,
                    'status' => $status,
                    'settled_at' => $row['visited_on'],
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
                            'shop_id' => $shop->id,
                            'transaction_id' => $tx->id,
                            'txn_code' => $row['txn_code'],
                        ]),
                    ]
                );
            }

            $count++;
        }

        $this->command->info("VisitorSeeder completed. {$count} visitors and transactions seeded.");
    }
}
