<?php

namespace Database\Seeders;

use App\Models\CoinLedger;
use App\Models\CoinCampaigns;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * CoinLedgerSeeder - Seeds the global coin ledger with Zoho-compatible entries.
 *
 * DEV ONLY: Do not use in production. Creates demo coin transactions.
 *
 * Entry Types:
 * - PAID_COIN_CREDIT: Coins purchased by user
 * - REWARD_COIN_CREDIT: Coins earned as reward
 * - COIN_REDEEM: Coins spent/redeemed
 * - COIN_EXPIRE: Coins expired
 * - COIN_REVERSAL: Coin reversal/adjustment
 */
class CoinLedgerSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::limit(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserFactory first.');
            return;
        }

        $campaigns = CoinCampaigns::limit(5)->get();
        $entries = [];
        $now = Carbon::now();

        foreach ($users as $index => $user) {
            // 1. PAID_COIN_CREDIT - User purchased coins
            $paidCoins = rand(500, 2000);
            $paidDaysAgo = rand(30, 90);
            $paidExpiry = $now->copy()->addDays(365 - $paidDaysAgo);

            $entries[] = [
                'user_id' => $user->id,
                'entry_type' => CoinLedger::TYPE_PAID_CREDIT,
                'coins_in' => $paidCoins,
                'coins_out' => 0,
                'coin_category' => CoinLedger::CAT_PAID,
                'expiry_date' => $paidExpiry,
                'reference_id' => 'CAMP_' . ($campaigns->random()->id ?? rand(1, 5)),
                'metadata' => json_encode([
                    'source' => 'campaign_purchase',
                    'campaign_id' => $campaigns->random()->id ?? 1,
                    'payment_method' => 'razorpay',
                ]),
                'created_at' => $now->copy()->subDays($paidDaysAgo),
                'updated_at' => $now->copy()->subDays($paidDaysAgo),
            ];

            // 2. REWARD_COIN_CREDIT - User earned reward coins (50% of users)
            if ($index % 2 === 0) {
                $rewardCoins = rand(100, 500);
                $rewardDaysAgo = rand(15, 60);

                $entries[] = [
                    'user_id' => $user->id,
                    'entry_type' => CoinLedger::TYPE_REWARD_CREDIT,
                    'coins_in' => $rewardCoins,
                    'coins_out' => 0,
                    'coin_category' => CoinLedger::CAT_REWARD,
                    'expiry_date' => $now->copy()->addDays(90),
                    'reference_id' => 'REWARD_' . uniqid(),
                    'metadata' => json_encode([
                        'source' => 'referral_bonus',
                        'referred_user_id' => rand(100, 999),
                    ]),
                    'created_at' => $now->copy()->subDays($rewardDaysAgo),
                    'updated_at' => $now->copy()->subDays($rewardDaysAgo),
                ];
            }

            // 3. COIN_REDEEM - User spent coins (70% of users)
            if ($index % 3 !== 0) {
                $redeemCoins = rand(100, 800);
                $redeemDaysAgo = rand(5, 30);

                $entries[] = [
                    'user_id' => $user->id,
                    'entry_type' => CoinLedger::TYPE_REDEEM,
                    'coins_in' => 0,
                    'coins_out' => $redeemCoins,
                    'coin_category' => rand(0, 1) ? CoinLedger::CAT_PAID : CoinLedger::CAT_REWARD,
                    'expiry_date' => null,
                    'reference_id' => 'TXN_' . rand(10000, 99999),
                    'metadata' => json_encode([
                        'source' => 'store_redemption',
                        'shop_id' => rand(1, 3),
                        'transaction_id' => rand(1000, 9999),
                    ]),
                    'created_at' => $now->copy()->subDays($redeemDaysAgo),
                    'updated_at' => $now->copy()->subDays($redeemDaysAgo),
                ];
            }

            // 4. COIN_EXPIRE - Some coins expired (20% of users)
            if ($index % 5 === 0) {
                $expiredCoins = rand(50, 200);

                $entries[] = [
                    'user_id' => $user->id,
                    'entry_type' => CoinLedger::TYPE_EXPIRE,
                    'coins_in' => 0,
                    'coins_out' => $expiredCoins,
                    'coin_category' => CoinLedger::CAT_REWARD,
                    'expiry_date' => $now->copy()->subDays(1),
                    'reference_id' => 'EXP_' . uniqid(),
                    'metadata' => json_encode([
                        'reason' => 'expiry_date_passed',
                        'original_entry_id' => rand(1, 100),
                    ]),
                    'created_at' => $now->copy()->subDays(1),
                    'updated_at' => $now->copy()->subDays(1),
                ];
            }
        }

        // Insert all entries
        foreach ($entries as $entry) {
            CoinLedger::create($entry);
        }

        $this->command->info('CoinLedgerSeeder completed. ' . count($entries) . ' ledger entries created.');
        $this->command->info('Entry types: PAID_COIN_CREDIT, REWARD_COIN_CREDIT, COIN_REDEEM, COIN_EXPIRE');
    }
}
