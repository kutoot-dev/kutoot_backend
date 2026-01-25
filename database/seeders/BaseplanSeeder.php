<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\BaseplanCampaignLinked;
use App\Models\Baseplans;
use App\Models\CoinCampaigns;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * BaseplanSeeder - Seeds coin base plans with HD ecommerce images.
 *
 * DEV ONLY: Creates sample base plans with optimized WebP images.
 * Base plans are linked to campaigns and define pricing tiers.
 */
class BaseplanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Downloading and optimizing base plan images...');

        $baseplans = [
            [
                'title' => 'Starter Pack',
                'description' => 'Perfect for beginners! Get started with our basic coin package including essential rewards and entry-level benefits.',
                'ticket_price' => 25.00,
                'total_tickets' => 100,
                'coins_per_campaign' => 100,
                'coupons_per_campaign' => 2,
                'duration' => '7 days',
                'point1' => '100 coins included',
                'point2' => '2 reward coupons',
                'point3' => 'Valid for 7 days',
                'point4' => 'Entry to lucky draws',
                'point5' => 'Email support',
                'status' => 1,
                'picsum_id' => 180,
            ],
            [
                'title' => 'Bronze Plan',
                'description' => 'A great value plan for regular shoppers. Enjoy more coins and better discounts on your purchases.',
                'ticket_price' => 50.00,
                'total_tickets' => 200,
                'coins_per_campaign' => 250,
                'coupons_per_campaign' => 5,
                'duration' => '14 days',
                'point1' => '250 coins included',
                'point2' => '5 reward coupons',
                'point3' => '14 days validity',
                'point4' => 'Priority lucky draw entry',
                'point5' => '5% extra discount',
                'status' => 1,
                'picsum_id' => 225,
            ],
            [
                'title' => 'Silver Plan',
                'description' => 'For dedicated customers who want more value. Get extra coins and exclusive member benefits.',
                'ticket_price' => 100.00,
                'total_tickets' => 300,
                'coins_per_campaign' => 550,
                'coupons_per_campaign' => 8,
                'duration' => '30 days',
                'point1' => '550 coins included',
                'point2' => '8 reward coupons',
                'point3' => '30 days validity',
                'point4' => 'Premium lucky draw entry',
                'point5' => '10% extra discount',
                'status' => 1,
                'picsum_id' => 250,
            ],
            [
                'title' => 'Gold Plan',
                'description' => 'Premium plan for power shoppers. Maximum coins, maximum savings, and exclusive VIP perks.',
                'ticket_price' => 200.00,
                'total_tickets' => 400,
                'coins_per_campaign' => 1200,
                'coupons_per_campaign' => 15,
                'duration' => '45 days',
                'point1' => '1200 coins included',
                'point2' => '15 reward coupons',
                'point3' => '45 days validity',
                'point4' => 'VIP lucky draw access',
                'point5' => '15% extra discount',
                'status' => 1,
                'picsum_id' => 292,
            ],
            [
                'title' => 'Platinum Plan',
                'description' => 'Ultimate premium package for elite customers. Unlimited benefits, top rewards, and exclusive access to all features.',
                'ticket_price' => 500.00,
                'total_tickets' => 500,
                'coins_per_campaign' => 3000,
                'coupons_per_campaign' => 25,
                'duration' => '90 days',
                'point1' => '3000 coins included',
                'point2' => '25 reward coupons',
                'point3' => '90 days validity',
                'point4' => 'Exclusive platinum draws',
                'point5' => '25% extra discount',
                'status' => 1,
                'picsum_id' => 299,
            ],
            [
                'title' => 'Enterprise Pack',
                'description' => 'Designed for businesses and bulk buyers. Maximum coins with extended validity for corporate accounts.',
                'ticket_price' => 1000.00,
                'total_tickets' => 1000,
                'coins_per_campaign' => 7500,
                'coupons_per_campaign' => 50,
                'duration' => '180 days',
                'point1' => '7500 coins included',
                'point2' => '50 reward coupons',
                'point3' => '180 days validity',
                'point4' => 'Enterprise-only draws',
                'point5' => 'Dedicated account manager',
                'status' => 1,
                'picsum_id' => 319,
            ],
        ];

        $campaigns = CoinCampaigns::whereIn('id', [1, 2, 3, 4, 5, 6])->get();

        foreach ($baseplans as $index => $planData) {
            $slug = Str::slug($planData['title']);

            // Download base plan image
            $imgPath = ImageSeederHelper::ensureImage(
                'baseplans',
                'baseplan-' . $slug,
                'product',
                $planData['picsum_id']
            );

            // Remove picsum_id from plan data
            unset($planData['picsum_id']);

            // Add image path
            $planData['img'] = $imgPath;

            // Link to a campaign if exists
            if (isset($campaigns[$index])) {
                $planData['camp_id'] = $campaigns[$index]->id;
            }

            $baseplan = Baseplans::updateOrCreate(
                ['title' => $planData['title']],
                $planData
            );

            // Link baseplan to campaigns (if pivot table exists)
            if ($campaigns->isNotEmpty()) {
                // Link to 2-3 random campaigns
                $linkedCampaigns = $campaigns->random(min(3, $campaigns->count()));
                foreach ($linkedCampaigns as $campaign) {
                    BaseplanCampaignLinked::firstOrCreate([
                        'baseplan_id' => $baseplan->id,
                        'campaign_id' => $campaign->id,
                    ]);
                }
            }

            $this->command->line("  ✓ Base Plan: {$planData['title']}");
        }

        $this->command->info('BaseplanSeeder completed. ' . count($baseplans) . ' base plans seeded with HD images.');
    }
}
