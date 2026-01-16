<?php

namespace Database\Seeders;

use App\Models\PurchasedCoins;
use App\Models\UserCoins;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserCoinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates credit entries for campaign coin purchases and debit entries for coins spent in store transactions.
     * All coins expire after 30 days from creation date (global 30-day expiry).
     */
    public function run(): void
    {
        // Get all purchased coins (campaign purchases)
        $purchasedCoins = PurchasedCoins::where('payment_status', 'completed')->get();

        if ($purchasedCoins->isEmpty()) {
            $this->command->warn('No completed coin purchases found. Please run PurchasedCoinsSeeder first.');
            return;
        }

        $creditCount = 0;
        $debitCount = 0;

        // For each campaign purchase, create credit entries and some random debits
        foreach ($purchasedCoins as $purchase) {
            // Calculate coins earned from this purchase
            $totalCoinsEarned = $purchase->camp_coins_per_campaign * $purchase->quantity;

            // Create CREDIT entry - coins earned from campaign purchase
            $creditCreatedAt = $purchase->created_at ?? Carbon::now()->subDays(rand(10, 60));
            $coinExpires = Carbon::parse($creditCreatedAt)->addDays(30);

            UserCoins::firstOrCreate(
                [
                    'purchased_camp_id' => $purchase->id,
                    'user_id' => $purchase->user_id,
                    'type' => 'credit',
                ],
                [
                    'coins' => $totalCoinsEarned,
                    'coin_expires' => $coinExpires->toDateTimeString(),
                    'status' => 1,
                    'created_at' => $creditCreatedAt,
                    'updated_at' => $creditCreatedAt,
                ]
            );
            $creditCount++;

            // Create random DEBIT entries - coins spent from this campaign purchase
            // 60% chance of having some debits
            if (rand(1, 100) <= 60) {
                $debitCount = rand(1, 4); // 1-4 debit transactions
                $coinsRemaining = $totalCoinsEarned;

                for ($i = 0; $i < $debitCount && $coinsRemaining > 0; $i++) {
                    // Spend random amount (20-80% of remaining coins)
                    $debitAmount = min(
                        rand((int)($coinsRemaining * 0.2), (int)($coinsRemaining * 0.8)),
                        $totalCoinsEarned
                    );

                    // Create debit at random date between purchase and expiry
                    $daysSincePurchase = rand(1, min(25, 30)); // Spend before expiry
                    $debitCreatedAt = Carbon::parse($creditCreatedAt)->addDays($daysSincePurchase);

                    UserCoins::create([
                        'purchased_camp_id' => $purchase->id,
                        'user_id' => $purchase->user_id,
                        'type' => 'debit',
                        'coins' => $debitAmount,
                        'coin_expires' => $coinExpires->toDateTimeString(),
                        'status' => 1,
                        'created_at' => $debitCreatedAt,
                        'updated_at' => $debitCreatedAt,
                    ]);

                    $coinsRemaining -= $debitAmount;
                    $debitCount++;
                }
            }
        }

        // Also add some legacy ORDER-based coins (coins from store transactions)
        // These don't have purchased_camp_id set
        $users = \App\Models\User::limit(5)->get();
        foreach ($users as $user) {
            // Create 2-4 order-based coin entries per user (from store transactions)
            $orderCoinEntries = rand(2, 4);

            for ($i = 0; $i < $orderCoinEntries; $i++) {
                $createdAt = Carbon::now()->subDays(rand(5, 40));
                $coinExpires = Carbon::parse($createdAt)->addDays(30);
                $coins = rand(50, 300);

                // Create credit entry
                UserCoins::create([
                    'user_id' => $user->id,
                    'purchased_camp_id' => null,
                    'order_id' => rand(1, 100), // Mock order ID
                    'type' => 'credit',
                    'coins' => $coins,
                    'coin_expires' => $coinExpires->toDateTimeString(),
                    'status' => 1,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // 40% chance of having a debit for these coins
                if (rand(1, 100) <= 40) {
                    $debitAmount = rand((int)($coins * 0.3), (int)($coins * 0.9));
                    $debitCreatedAt = Carbon::parse($createdAt)->addDays(rand(1, 20));

                    UserCoins::create([
                        'user_id' => $user->id,
                        'purchased_camp_id' => null,
                        'order_id' => rand(1, 100),
                        'type' => 'debit',
                        'coins' => $debitAmount,
                        'coin_expires' => $coinExpires->toDateTimeString(),
                        'status' => 1,
                        'created_at' => $debitCreatedAt,
                        'updated_at' => $debitCreatedAt,
                    ]);
                }
            }
        }

        $this->command->info('UserCoinsSeeder completed.');
        $this->command->info("  - Created {$creditCount} credit entries for campaign coin purchases");
        $this->command->info("  - Created additional debit entries for coin usage");
        $this->command->info("  - All coins set with 30-day global expiry");
    }
}
