<?php

namespace Database\Seeders;

use App\Models\CoinCampaigns;
use App\Models\MasterPrize;
use App\Models\PurchasedCoins;
use App\Models\User;
use App\Models\UserCoupons;
use App\Models\Winners;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * WinnersSeeder - Seeds sample winners data for the winners blade.
 *
 * DEV ONLY: Creates sample winners linked to campaigns, users, and coupons.
 */
class WinnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding winners data...');

        // Get campaigns
        $campaigns = CoinCampaigns::all();
        if ($campaigns->isEmpty()) {
            $this->command->warn('No campaigns found. Please run CoinCampaignSeeder first.');
            return;
        }

        // Get completed purchases
        $purchases = PurchasedCoins::where('payment_status', 'completed')->get();

        // Get users with profile images
        $users = User::whereNotNull('image')->get();
        if ($users->isEmpty()) {
            $users = User::limit(10)->get();
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please create some users first.');
            return;
        }

        // Get or create master prizes
        $prizeDetails = [
            [
                'name' => 'iPhone 15 Pro Max',
                'value' => 1599.00,
                'description' => 'Latest Apple iPhone 15 Pro Max 256GB - Titanium Blue'
            ],
            [
                'name' => 'MacBook Air M3',
                'value' => 1299.00,
                'description' => 'Apple MacBook Air 15" M3 chip - 8GB RAM, 256GB SSD'
            ],
            [
                'name' => 'Samsung 65" OLED TV',
                'value' => 2499.00,
                'description' => 'Samsung 65" Class OLED S95C Series 4K Smart TV'
            ],
            [
                'name' => 'PlayStation 5 Bundle',
                'value' => 599.00,
                'description' => 'PS5 Console with extra controller and 3 games'
            ],
            [
                'name' => 'Apple Watch Ultra 2',
                'value' => 799.00,
                'description' => 'Apple Watch Ultra 2 with Ocean Band'
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'value' => 399.00,
                'description' => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones'
            ],
            [
                'name' => '$500 Amazon Gift Card',
                'value' => 500.00,
                'description' => 'Amazon Gift Card worth $500'
            ],
            [
                'name' => 'iPad Pro 12.9"',
                'value' => 1099.00,
                'description' => 'iPad Pro 12.9" M2 chip - 128GB WiFi'
            ],
            [
                'name' => 'DJI Mini 4 Pro Drone',
                'value' => 759.00,
                'description' => 'DJI Mini 4 Pro Fly More Combo'
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'value' => 349.00,
                'description' => 'Nintendo Switch OLED Model with 5 games bundle'
            ],
        ];

        // Get user coupons if available
        $coupons = UserCoupons::limit(50)->get();

        $winnersCount = 0;
        $claimedStatuses = [0, 0, 1, 1, 1]; // 60% claimed

        foreach ($campaigns as $index => $campaign) {
            // Each campaign has 2-4 winners
            $numWinners = rand(2, 4);
            $shuffledUsers = $users->shuffle();

            for ($w = 0; $w < min($numWinners, $shuffledUsers->count()); $w++) {
                $user = $shuffledUsers[$w];
                $prize = $prizeDetails[($index + $w) % count($prizeDetails)];

                // Get a purchase for this user and campaign if exists
                $purchase = $purchases->where('user_id', $user->id)
                    ->where('camp_id', $campaign->id)
                    ->first();

                if (!$purchase) {
                    $purchase = $purchases->where('user_id', $user->id)->first();
                }

                // Get or generate coupon
                $coupon = null;
                $couponNumber = null;

                if ($purchase && $coupons->isNotEmpty()) {
                    $coupon = $coupons->where('purchased_camp_id', $purchase->id)->first();
                }

                if ($coupon) {
                    $couponNumber = $coupon->coupon_code;
                } else {
                    $seriesLabel = $campaign->series_prefix ?? chr(65 + ($campaign->id % 26));
                    $couponNumber = $seriesLabel . '-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                }

                // Create announcement date (past dates for claimed, future for unclaimed)
                $isClaimed = $claimedStatuses[array_rand($claimedStatuses)];
                $announcingDate = $isClaimed
                    ? Carbon::now()->subDays(rand(5, 60))
                    : Carbon::now()->addDays(rand(1, 30));

                Winners::updateOrCreate(
                    [
                        'camp_id' => $campaign->id,
                        'user_id' => $user->id,
                        'coupon_number' => $couponNumber,
                    ],
                    [
                        'camp_id' => $campaign->id,
                        'purchased_camp_id' => $purchase?->id,
                        'coupon_id' => $coupon?->id,
                        'coupon_number' => $couponNumber,
                        'user_id' => $user->id,
                        'is_claimed' => $isClaimed,
                        'prize_details' => json_encode($prize),
                        'prize_id' => ($index + $w) % count($prizeDetails) + 1,
                        'announcing_date' => $announcingDate->toDateTimeString(),
                        'status' => 1,
                    ]
                );

                $winnersCount++;
                $status = $isClaimed ? 'claimed' : 'pending';
                $this->command->line("  ✓ Winner: {$user->name} - {$prize['name']} ({$status})");
            }
        }

        $this->command->info("WinnersSeeder completed. {$winnersCount} winners seeded.");
    }
}
