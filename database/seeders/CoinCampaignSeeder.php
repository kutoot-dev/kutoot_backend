<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\CoinCampaigns;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * CoinCampaignSeeder - Seeds sample coin campaigns with HD ecommerce images.
 *
 * DEV ONLY: Creates sample campaigns with optimized WebP images.
 */
class CoinCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Downloading and optimizing campaign images...');

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
                'series_prefix' => 'A',
                'number_min' => 1,
                'number_max' => 99,
                'numbers_per_ticket' => 6,
                'picsum_id' => 1011,  // Ecommerce/shopping themed
                'picsum_id_1' => 1012,
                'picsum_id_2' => 1013,
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
                'series_prefix' => 'B',
                'number_min' => 1,
                'number_max' => 99,
                'numbers_per_ticket' => 5,
                'picsum_id' => 1015,  // Gift/reward themed
                'picsum_id_1' => 1016,
                'picsum_id_2' => 1018,
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
                'series_prefix' => 'C',
                'number_min' => 1,
                'number_max' => 50,
                'numbers_per_ticket' => 4,
                'picsum_id' => 1019,  // Shopping cart themed
                'picsum_id_1' => 1021,
                'picsum_id_2' => 1022,
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
                'series_prefix' => 'D',
                'number_min' => 1,
                'number_max' => 75,
                'numbers_per_ticket' => 5,
                'picsum_id' => 1024,  // Discount/sale themed
                'picsum_id_1' => 1025,
                'picsum_id_2' => 1029,
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
                'series_prefix' => 'E',
                'number_min' => 1,
                'number_max' => 99,
                'numbers_per_ticket' => 6,
                'picsum_id' => 1031,  // Premium/luxury themed
                'picsum_id_1' => 1033,
                'picsum_id_2' => 1035,
            ],
            [
                'title' => 'Festive Season Bonanza',
                'title1' => 'Festive Sale',
                'title2' => 'Celebrate & Win',
                'campaign_id' => 6,
                'description' => 'Celebrate the festive season with mega coin rewards! Earn extra coins on every purchase and win exclusive festival prizes.',
                'short_description' => 'Festive season mega coin rewards',
                'ticket_price' => 150.00,
                'total_tickets' => 800,
                'sold_tickets' => 520,
                'coins_per_campaign' => 1200,
                'coupons_per_campaign' => 18,
                'max_coins_per_transaction' => 1200,
                'start_date' => Carbon::now()->subDays(10)->toDateString(),
                'end_date' => Carbon::now()->addDays(25)->toDateString(),
                'status' => 1,
                'category' => 'Festival',
                'tags' => json_encode(['festive', 'celebration', 'coins', 'prizes']),
                'series_prefix' => 'F',
                'number_min' => 1,
                'number_max' => 99,
                'numbers_per_ticket' => 5,
                'picsum_id' => 1037,
                'picsum_id_1' => 1038,
                'picsum_id_2' => 1040,
            ],
        ];

        foreach ($campaigns as $campaign) {
            $slug = Str::slug($campaign['title']);

            // Download main campaign image
            $imgPath = ImageSeederHelper::ensureImage(
                'campaigns',
                'campaign-' . $slug,
                'banner',
                $campaign['picsum_id']
            );

            // Download additional campaign images
            $image1Path = ImageSeederHelper::ensureImage(
                'campaigns',
                'campaign-' . $slug . '-1',
                'product',
                $campaign['picsum_id_1']
            );

            $image2Path = ImageSeederHelper::ensureImage(
                'campaigns',
                'campaign-' . $slug . '-2',
                'product',
                $campaign['picsum_id_2']
            );

            // Remove picsum_ids from campaign data
            unset($campaign['picsum_id'], $campaign['picsum_id_1'], $campaign['picsum_id_2']);

            // Add image paths to campaign data
            $campaign['img'] = $imgPath;
            $campaign['image1'] = $image1Path;
            $campaign['image2'] = $image2Path;

            CoinCampaigns::updateOrCreate(
                ['title' => $campaign['title']],
                $campaign
            );

            $this->command->line("  ✓ Campaign: {$campaign['title']}");
        }

        $this->command->info('CoinCampaignSeeder completed. ' . count($campaigns) . ' campaigns seeded with HD images.');
    }
}
