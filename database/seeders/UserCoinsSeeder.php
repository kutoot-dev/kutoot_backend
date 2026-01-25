<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\PurchasedCoins;
use App\Models\User;
use App\Models\UserCoins;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * UserCoinsSeeder - Seeds user coin entries for the user coins blade.
 *
 * DEV ONLY: Creates credit and debit entries for campaign coin purchases
 * and store transactions. All coins expire after 30 days (global expiry).
 */
class UserCoinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding user coins...');

        // Get all completed coin purchases
        $purchasedCoins = PurchasedCoins::where('payment_status', 'completed')->get();
        $orders = Order::where('payment_status', 'success')->limit(20)->get();
        $users = User::limit(15)->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run user seeder first.');
            return;
        }

        $creditCount = 0;
        $debitCount = 0;

        // PART 1: Create coin entries from campaign purchases
        if ($purchasedCoins->isNotEmpty()) {
            foreach ($purchasedCoins as $purchase) {
                $totalCoinsEarned = $purchase->camp_coins_per_campaign * max(1, $purchase->quantity);
                $creditCreatedAt = $purchase->created_at ?? Carbon::now()->subDays(rand(10, 60));
                $coinExpires = Carbon::parse($creditCreatedAt)->addDays(30);

                // Create CREDIT entry for this purchase
                UserCoins::updateOrCreate(
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

                // 65% chance of having some debit transactions
                if (rand(1, 100) <= 65) {
                    $numDebits = rand(1, 4);
                    $coinsRemaining = $totalCoinsEarned;

                    for ($i = 0; $i < $numDebits && $coinsRemaining > 50; $i++) {
                        $debitAmount = min(
                            rand((int)($coinsRemaining * 0.15), (int)($coinsRemaining * 0.6)),
                            $coinsRemaining - 10
                        );

                        if ($debitAmount <= 0) break;

                        $daysSincePurchase = rand(1, min(25, 28));
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
        }

        // PART 2: Create coin entries from store orders (order-based coins)
        if ($orders->isNotEmpty()) {
            foreach ($orders as $order) {
                // Calculate coins earned (e.g., 1 coin per $10 spent)
                $coinsEarned = max(10, (int)($order->total_amount / 10));
                $createdAt = $order->created_at ?? Carbon::now()->subDays(rand(5, 45));
                $coinExpires = Carbon::parse($createdAt)->addDays(30);

                UserCoins::firstOrCreate(
                    [
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'type' => 'credit',
                    ],
                    [
                        'purchased_camp_id' => null,
                        'order_id' => $order->id,
                        'coins' => $coinsEarned,
                        'coin_expires' => $coinExpires->toDateTimeString(),
                        'status' => 1,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
                $creditCount++;
            }
        }

        // PART 3: Create additional coin entries for users without purchases
        foreach ($users as $user) {
            // Skip users who already have coin entries
            if (UserCoins::where('user_id', $user->id)->exists()) {
                continue;
            }

            // Create 2-5 coin entries per user
            $entries = rand(2, 5);
            for ($i = 0; $i < $entries; $i++) {
                $createdAt = Carbon::now()->subDays(rand(5, 50));
                $coinExpires = Carbon::parse($createdAt)->addDays(30);
                $coins = rand(50, 500);

                // Credit entry
                UserCoins::create([
                    'user_id' => $user->id,
                    'purchased_camp_id' => null,
                    'order_id' => null,
                    'type' => 'credit',
                    'coins' => $coins,
                    'coin_expires' => $coinExpires->toDateTimeString(),
                    'status' => 1,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $creditCount++;

                // 45% chance of having a debit
                if (rand(1, 100) <= 45) {
                    $debitAmount = rand((int)($coins * 0.2), (int)($coins * 0.8));
                    $debitCreatedAt = Carbon::parse($createdAt)->addDays(rand(1, 20));

                    UserCoins::create([
                        'user_id' => $user->id,
                        'purchased_camp_id' => null,
                        'order_id' => null,
                        'type' => 'debit',
                        'coins' => $debitAmount,
                        'coin_expires' => $coinExpires->toDateTimeString(),
                        'status' => 1,
                        'created_at' => $debitCreatedAt,
                        'updated_at' => $debitCreatedAt,
                    ]);
                    $debitCount++;
                }
            }
        }

        $this->command->info('UserCoinsSeeder completed.');
        $this->command->info("  - {$creditCount} credit entries created");
        $this->command->info("  - {$debitCount} debit entries created");
        $this->command->info("  - All coins set with 30-day global expiry");
    }
}
