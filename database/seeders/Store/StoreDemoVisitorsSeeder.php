<?php

namespace Database\Seeders\Store;

use App\Models\User;
use App\Models\UserCoins;
use App\Models\Store\Seller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use App\Models\Store\AdminShopCommissionDiscount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreDemoVisitorsSeeder extends Seeder
{
    private function coinValue(): float
    {
        return (float) config('kutoot.coin_value', 0.25);
    }

    private function getUserCoinBalance(int $userId): int
    {
        $coins = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $userId)
            ->whereDate('coin_expires', '>=', now()->toDateString())
            ->first();

        $credit = (int) ($coins->credit ?? 0);
        $debit = (int) ($coins->debit ?? 0);
        return max(0, $credit - $debit);
    }

    public function run()
    {
        $seller = Seller::query()->where('seller_code', 'SELLER001')->first();
        if (!$seller || !$seller->shop) {
            return;
        }

        $shop = $seller->shop;

        $master = AdminShopCommissionDiscount::resolveForShop($shop->id);
        $minimumBillAmount = (float) ($master?->minimum_bill_amount ?? 0);
        $discountPercent = (float) ($master?->discount_percent ?? 0);

        $rows = [
            [
                'user_name' => 'Rahul Sharma',
                'user_phone' => '9898989898',
                'user_email' => 'rahul@demo.com',
                'visited_on' => '2026-01-10',
                'txn_code' => 'TXN1001',
                // Keep this above the seeded minimum bill amount so discount/coins apply when redeemed=true
                'total_amount' => 1200,
                'status' => 'SUCCESS',
            ],
            [
                'user_name' => 'Anjali Singh',
                'user_phone' => '9797979797',
                'user_email' => 'anjali@demo.com',
                'visited_on' => '2026-01-11',
                'txn_code' => 'TXN1002',
                'total_amount' => 1100,
                'status' => 'SUCCESS',
            ],
        ];

        foreach ($rows as $row) {
            $user = User::query()->updateOrCreate(
                ['email' => $row['user_email']],
                [
                    'name' => $row['user_name'],
                    'phone' => $row['user_phone'],
                    'password' => Hash::make('123456'),
                ]
            );

            $aboveMin = ($minimumBillAmount <= 0) || ((float) $row['total_amount'] >= $minimumBillAmount);

            // Seeder rule: if transaction amount > minimum bill amount of that store then definitely redeem coins,
            // apply discount, and redeemed=true (force SUCCESS for above-min rows).
            // Strict: redemption amount comes strictly from user's wallet; max discount = store max discount percent.
            $status = $aboveMin ? 'SUCCESS' : (string) $row['status'];
            $attemptRedeem = $aboveMin && $status === 'SUCCESS' && $discountPercent > 0;

            // Max discount is store max discount percent; actual discount is limited by user's coin balance.
            $coinValue = $this->coinValue();
            $maxDiscountAmount = $attemptRedeem ? round(((float) $row['total_amount']) * ($discountPercent / 100), 2) : 0.0;
            $coinsNeeded = $attemptRedeem ? (int) floor($maxDiscountAmount / $coinValue) : 0;

            // Ensure wallet has enough coins upfront for these fixed demo rows (no per-transaction top-up).
            UserCoins::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'order_id' => null,
                    'purchased_camp_id' => 8001000 + abs((int) crc32((string) $row['user_email'])),
                ],
                [
                    'coins' => max(5000, $coinsNeeded + 1000),
                    'coin_expires' => '2099-12-31 00:00:00',
                    'status' => 1,
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

            UserCoins::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'order_id' => $tx->id,
                ],
                [
                    'purchased_camp_id' => null,
                    'coins' => $redeemedCoins,
                    'coin_expires' => '2099-12-31 00:00:00',
                    'status' => 1,
                ]
            );
        }
    }
}


