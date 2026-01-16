<?php

namespace Database\Seeders;

use App\Models\CoinCampaigns;
use App\Models\PurchasedCoins;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PurchasedCoinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing campaigns or use IDs 1-5
        $campaigns = CoinCampaigns::whereIn('id', [1, 2, 3, 4, 5])->get();
        if ($campaigns->isEmpty()) {
            $this->command->warn('No campaigns found. Please run CoinCampaignSeeder first.');
            return;
        }

        // Get existing users (or create some test users)
        $users = User::limit(5)->get();
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please create some users first.');
            return;
        }

        // Create 12-15 campaign purchases spread across users
        $purchaseRecords = [];
        $purchaseCount = 0;

        foreach ($campaigns as $campaign) {
            // Each campaign gets 2-3 purchases from different users
            $purchasesByThisCampaign = rand(2, 3);
            $selectedUsers = $users->random(min($purchasesByThisCampaign, $users->count()))->unique();

            foreach ($selectedUsers as $user) {
                // Create purchase record with timestamps spread over past 2 months
                $daysAgo = rand(10, 60);
                $createdAt = Carbon::now()->subDays($daysAgo);

                $purchaseRecords[] = [
                    'camp_id' => $campaign->id,
                    'user_id' => $user->id,
                    'camp_title' => $campaign->title,
                    'camp_description' => $campaign->description,
                    'camp_ticket_price' => $campaign->ticket_price,
                    'camp_coins_per_campaign' => $campaign->coins_per_campaign,
                    'camp_coupons_per_campaign' => $campaign->coupons_per_campaign,
                    'quantity' => rand(1, 5),
                    'payment_status' => 'completed',
                    'status' => 1,
                    'is_cart' => 0,
                    'razor_order_id' => 'order_' . uniqid(),
                    'payment_id' => 'pay_' . uniqid(),
                    'razorpay_signature' => hash('sha256', uniqid()),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                $purchaseCount++;
                if ($purchaseCount >= 15) {
                    break;
                }
            }

            if ($purchaseCount >= 15) {
                break;
            }
        }

        // Insert all purchase records
        foreach ($purchaseRecords as $record) {
            PurchasedCoins::firstOrCreate(
                [
                    'camp_id' => $record['camp_id'],
                    'user_id' => $record['user_id'],
                    'created_at' => $record['created_at'],
                ],
                $record
            );
        }

        $this->command->info('PurchasedCoinsSeeder completed. ' . count($purchaseRecords) . ' purchases seeded.');
    }
}
