<?php

namespace Database\Seeders\Store;

use App\Models\CoinCampaigns;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StoreCoinCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campaigns = [
            [
                'title' => 'Lucky Draw Campaign - January',
                'title1' => 'Lucky Draw',
                'title2' => 'January Special',
                'campaign_id' => 1,
                'description' => 'Get lucky and win amazing prizes. Participate in our January lucky draw campaign and earn up to 500 coins.',
                'short_description' => 'January Lucky Draw - Win coins and rewards',
                'ticket_price' => 50.00,
                'total_tickets' => 1000,
                'sold_tickets' => 450,
                'coins_per_campaign' => 500,
                'coupons_per_campaign' => 10,
                'max_coins_per_transaction' => 500,
                'start_date' => Carbon::now()->subDays(30)->toDateString(),
                'end_date' => Carbon::now()->addDays(10)->toDateString(),
                'status' => 1,
                'category' => 'Lucky Draw',
                'tags' => json_encode(['lucky', 'draw', 'coins', 'rewards']),
            ],
            [
                'title' => 'Mega Coins Bonanza',
                'title1' => 'Mega Bonanza',
                'title2' => 'Double Coins',
                'campaign_id' => 2,
                'description' => 'Double your coins! Special campaign with 2x coin multiplication. Limited time offer with exciting rewards.',
                'short_description' => 'Double coins on every purchase',
                'ticket_price' => 100.00,
                'total_tickets' => 500,
                'sold_tickets' => 280,
                'coins_per_campaign' => 1000,
                'coupons_per_campaign' => 15,
                'max_coins_per_transaction' => 1000,
                'start_date' => Carbon::now()->subDays(15)->toDateString(),
                'end_date' => Carbon::now()->addDays(20)->toDateString(),
                'status' => 1,
                'category' => 'Promotion',
                'tags' => json_encode(['double', 'coins', 'mega', 'promotion']),
            ],
            [
                'title' => 'Flash Sale Coins',
                'title1' => 'Flash Sale',
                'title2' => 'Quick Coins',
                'campaign_id' => 3,
                'description' => 'Lightning fast deals! Grab coins quickly before they run out. Flash sale with limited tickets available.',
                'short_description' => 'Flash sale for quick coin rewards',
                'ticket_price' => 75.00,
                'total_tickets' => 300,
                'sold_tickets' => 185,
                'coins_per_campaign' => 300,
                'coupons_per_campaign' => 8,
                'max_coins_per_transaction' => 300,
                'start_date' => Carbon::now()->subDays(7)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'status' => 1,
                'category' => 'Flash Sale',
                'tags' => json_encode(['flash', 'sale', 'coins', 'limited']),
            ],
            [
                'title' => 'Weekend Special Coins',
                'title1' => 'Weekend Special',
                'title2' => 'Fun Weekend',
                'campaign_id' => 4,
                'description' => 'Weekend fun with extra coins! Join our weekend special campaign and enjoy premium rewards with your purchases.',
                'short_description' => 'Weekend special coins offer',
                'ticket_price' => 125.00,
                'total_tickets' => 600,
                'sold_tickets' => 340,
                'coins_per_campaign' => 750,
                'coupons_per_campaign' => 12,
                'max_coins_per_transaction' => 750,
                'start_date' => Carbon::now()->subDays(20)->toDateString(),
                'end_date' => Carbon::now()->addDays(15)->toDateString(),
                'status' => 1,
                'category' => 'Special Offer',
                'tags' => json_encode(['weekend', 'special', 'coins', 'rewards']),
            ],
            [
                'title' => 'Premium Coin Package',
                'title1' => 'Premium Package',
                'title2' => 'Exclusive Coins',
                'campaign_id' => 5,
                'description' => 'Exclusive premium coin package for our valued customers. Unlock special privileges and earn maximum coins with premium membership.',
                'short_description' => 'Premium exclusive coin package',
                'ticket_price' => 200.00,
                'total_tickets' => 200,
                'sold_tickets' => 95,
                'coins_per_campaign' => 1500,
                'coupons_per_campaign' => 20,
                'max_coins_per_transaction' => 1500,
                'start_date' => Carbon::now()->subDays(45)->toDateString(),
                'end_date' => Carbon::now()->addDays(30)->toDateString(),
                'status' => 1,
                'category' => 'Premium',
                'tags' => json_encode(['premium', 'exclusive', 'coins', 'vip']),
            ],
        ];

        foreach ($campaigns as $campaign) {
            CoinCampaigns::firstOrCreate(
                ['title' => $campaign['title']],
                $campaign
            );
        }

        $this->command->info('StoreCoinCampaignSeeder completed. ' . count($campaigns) . ' campaigns seeded.');
    }
}
