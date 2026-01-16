<?php

namespace Database\Seeders\Store;

use App\Models\Store\AdminShopCommissionDiscount;
use App\Models\Store\Seller;
use App\Models\Store\ShopVisitor;
use App\Models\Store\Transaction;
use App\Models\User;
use App\Models\UserCoins;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreDemoBulkTransactionsSeeder extends Seeder
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

    /**
     * Generate 1000 visitors + transactions across year 2026.
     * Rules applied:
     * - discount/coins only if redemption is eligible AND status=SUCCESS AND total_amount >= minimum_bill_amount
     * - shop_visitors.redeemed is derived from whether redeemed_coins > 0 (requested behavior)
     */
    public function run()
    {
        $seller = Seller::query()->where('seller_code', 'SELLER001')->first();
        if (!$seller || !$seller->shop) {
            return;
        }

        $shop = $seller->shop;

        $master = AdminShopCommissionDiscount::resolveForShop($shop->id);
        $discountPercent = (float) ($master?->discount_percent ?? 0);
        $minimumBillAmount = (float) ($master?->minimum_bill_amount ?? 0);

        $start = strtotime('2026-01-01');
        $end = strtotime('2026-12-31');

        // Requirement: each user should have 10-15 transactions, while keeping 1000 total transactions.
        // Use 80 users: min=800 txns, max=1200 txns => we can hit exactly 1000 with random distribution.
        $userCount = 80;
        $totalTargetTxns = 1000;
        $basePerUser = 10;
        $maxExtraPerUser = 5; // => 10..15
        $extraToDistribute = $totalTargetTxns - ($userCount * $basePerUser); // 200

        $extras = array_fill(1, $userCount, 0);
        while ($extraToDistribute > 0) {
            $u = mt_rand(1, $userCount);
            if ($extras[$u] < $maxExtraPerUser) {
                $extras[$u]++;
                $extraToDistribute--;
            }
        }

        $txnSeq = 1;
        for ($u = 1; $u <= $userCount; $u++) {
            $phone = '98' . str_pad((string) mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $user = User::query()->updateOrCreate(
                ['email' => "visitor{$u}@demo.com"],
                [
                    'name' => 'Visitor ' . $u,
                    'phone' => $phone,
                    'password' => Hash::make('123456'),
                ]
            );

            $txnCount = $basePerUser + ($extras[$u] ?? 0);

            // Pre-generate txns for this user so we can set ONE initial coin credit (no auto top-up)
            // and still strictly redeem for every above-minimum transaction.
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

                // Strict rule: if amount >= minimum bill amount, then redeem coins & apply discount
                // using user's coins, capped by store max discount percent.
                // For seed realism: allow random failed txns only when below minimum bill.
                $status = $aboveMin ? 'SUCCESS' : (mt_rand(1, 100) <= 92 ? 'SUCCESS' : 'FAILED');

                $attemptRedeem = $aboveMin && $status === 'SUCCESS' && $discountPercent > 0;
                 $coinValue = $this->coinValue();
                 $maxDiscountAmount = $attemptRedeem ? round($totalAmount * ($discountPercent / 100), 2) : 0.0;
                 $coinsNeeded = $attemptRedeem ? (int) floor($maxDiscountAmount / $coinValue) : 0;
                $totalCoinsNeededForUser += $coinsNeeded;

                $txns[] = compact('txnCode', 'visitedOn', 'visitedAt', 'totalAmount', 'aboveMin', 'status', 'coinsNeeded');
            }

            // Give each user a starting coin wallet credit that can cover all above-minimum redemptions
            // (no per-transaction top-ups; wallet must drive discount strictly).
            UserCoins::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'order_id' => null,
                    'purchased_camp_id' => 8000000 + $u, // stable per user so reseed updates amount
                ],
                [
                    'coins' => max(0, $totalCoinsNeededForUser) + mt_rand(500, 2500),
                    'coin_expires' => '2099-12-31 00:00:00',
                    'status' => 1,
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

                // Ensure stable reseeding: if txn exists, reuse its visitor row; else create a fresh visitor row.
                $existingTxn = Transaction::query()->where('txn_code', $txnCode)->first();
                $visitor = $existingTxn?->visitor_id ? ShopVisitor::query()->find($existingTxn->visitor_id) : null;
                if (!$visitor) {
                    $visitor = new ShopVisitor();
                }
                $visitor->shop_id = $shop->id;
                $visitor->user_id = $user->id;
                $visitor->visited_on = $visitedOn;
                $visitor->redeemed = $redeemed;
                $visitor->created_at = $visitedAt;
                $visitor->updated_at = $visitedAt;
                $visitor->save();

                $tx = Transaction::query()->updateOrCreate(
                    ['txn_code' => $txnCode],
                    [
                        'shop_id' => $shop->id,
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

                // Debit the user's coins for this transaction (idempotent via order_id = store transaction id).
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
}


