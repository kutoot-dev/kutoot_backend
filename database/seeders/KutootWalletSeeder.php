<?php

namespace Database\Seeders;

use App\Models\CoinLedger;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * KutootWalletSeeder - Creates the Kutoot system wallet with initial liability.
 *
 * This seeder creates a system user to represent Kutoot's coin liability pool.
 * The system wallet holds 5 crore (50,000,000) coins representing total liability.
 */
class KutootWalletSeeder extends Seeder
{
    /**
     * System user ID constant - used across the application.
     */
    public const SYSTEM_USER_ID = 0;
    public const SYSTEM_USER_EMAIL = 'system@kutoot.com';
    public const INITIAL_LIABILITY_COINS = 50000000; // 5 crore

    public function run(): void
    {
        // Create or update the system user
        $systemUser = User::query()->updateOrCreate(
            ['id' => self::SYSTEM_USER_ID],
            [
                'name' => 'Kutoot System Wallet',
                'email' => self::SYSTEM_USER_EMAIL,
                'password' => bcrypt('SYSTEM_NO_LOGIN_' . time()),
                'status' => 0, // Inactive - cannot login
                'email_verified' => 0,
                'is_completed' => 0,
            ]
        );

        // Check current system wallet balance
        $currentBalance = CoinLedger::where('user_id', self::SYSTEM_USER_ID)
            ->selectRaw('COALESCE(SUM(coins_in), 0) - COALESCE(SUM(coins_out), 0) as balance')
            ->value('balance') ?? 0;

        $targetBalance = self::INITIAL_LIABILITY_COINS;
        $difference = $targetBalance - $currentBalance;

        if ($difference == 0) {
            $this->command->info('Kutoot wallet already at target: ' . number_format($targetBalance) . ' coins.');
            return;
        }

        // Create adjustment entry
        if ($difference > 0) {
            // Need to add coins
            CoinLedger::create([
                'user_id' => self::SYSTEM_USER_ID,
                'entry_type' => CoinLedger::TYPE_PAID_CREDIT,
                'coins_in' => $difference,
                'coins_out' => 0,
                'coin_category' => CoinLedger::CAT_PAID,
                'expiry_date' => null, // System coins never expire
                'reference_id' => 'SYSTEM_INIT_' . now()->format('Ymd_His'),
                'metadata' => [
                    'reason' => 'Initial Kutoot wallet liability setup',
                    'target_balance' => $targetBalance,
                    'previous_balance' => $currentBalance,
                ],
            ]);
            $this->command->info('Added ' . number_format($difference) . ' coins to Kutoot wallet.');
        } else {
            // Need to reduce coins (unlikely for initial setup)
            CoinLedger::create([
                'user_id' => self::SYSTEM_USER_ID,
                'entry_type' => CoinLedger::TYPE_REVERSAL,
                'coins_in' => 0,
                'coins_out' => abs($difference),
                'coin_category' => CoinLedger::CAT_PAID,
                'expiry_date' => null,
                'reference_id' => 'SYSTEM_ADJUST_' . now()->format('Ymd_His'),
                'metadata' => [
                    'reason' => 'Kutoot wallet liability adjustment',
                    'target_balance' => $targetBalance,
                    'previous_balance' => $currentBalance,
                ],
            ]);
            $this->command->info('Reduced ' . number_format(abs($difference)) . ' coins from Kutoot wallet.');
        }

        $this->command->info('Kutoot wallet now at: ' . number_format($targetBalance) . ' coins (₹' . number_format($targetBalance * 0.25, 2) . ' liability).');
    }
}
