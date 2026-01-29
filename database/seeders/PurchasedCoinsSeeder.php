<?php

namespace Database\Seeders;

use App\Models\Baseplans;
use App\Models\CoinCampaigns;
use App\Models\PurchasedCoins;
use App\Models\User;
use App\Models\UserCoupons;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * PurchasedCoinsSeeder - Seeds sample campaign purchases for purchase orders blade.
 *
 * DEV ONLY: Creates completed purchases with payment details and linked coupons.
 */
class PurchasedCoinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding purchase orders...');

        // Get existing campaigns
        $campaigns = CoinCampaigns::all();
        if ($campaigns->isEmpty()) {
            $this->command->warn('No campaigns found. Please run CoinCampaignSeeder first.');
            return;
        }

        // Get existing base plans
        $baseplans = Baseplans::all();

        // Get existing users (or create some test users)
        $users = User::limit(10)->get();
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please create some users first.');
            return;
        }

        $paymentStatuses = ['completed', 'completed', 'completed', 'pending', 'failed'];
        $purchaseCount = 0;

        // Create 25-30 campaign purchases spread across users
        foreach ($campaigns as $campaign) {
            // Each campaign gets 3-5 purchases from different users
            $purchasesByThisCampaign = rand(3, 5);
            $shuffledUsers = $users->shuffle();

            for ($i = 0; $i < min($purchasesByThisCampaign, $shuffledUsers->count()); $i++) {
                $user = $shuffledUsers[$i];

                // Create purchase record with timestamps spread over past 3 months
                $daysAgo = rand(5, 90);
                $createdAt = Carbon::now()->subDays($daysAgo);

                $quantity = rand(1, 5);
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                $baseplan = $baseplans->isNotEmpty() ? $baseplans->random() : null;

                // Generate payment IDs for completed payments
                $razorOrderId = null;
                $paymentId = null;
                $razorpaySignature = null;

                if ($paymentStatus === 'completed') {
                    $razorOrderId = 'order_' . Str::upper(Str::random(14));
                    $paymentId = 'pay_' . Str::upper(Str::random(14));
                    $razorpaySignature = hash('sha256', $razorOrderId . '|' . $paymentId . '|secret');
                }

                $purchase = PurchasedCoins::updateOrCreate(
                    [
                        'camp_id' => $campaign->id,
                        'user_id' => $user->id,
                        'created_at' => $createdAt,
                    ],
                    [
                        'camp_id' => $campaign->id,
                        'user_id' => $user->id,
                        'camp_title' => $campaign->title,
                        'camp_description' => $campaign->description ?? '',
                        'camp_ticket_price' => $campaign->ticket_price,
                        'camp_coins_per_campaign' => $campaign->coins_per_campaign,
                        'camp_coupons_per_campaign' => $campaign->coupons_per_campaign,
                        'quantity' => $quantity,
                        'status' => 1,
                        'is_cart' => 0,
                        'payment_status' => $paymentStatus,
                        'razor_order_id' => $razorOrderId,
                        'payment_id' => $paymentId,
                        'razorpay_signature' => $razorpaySignature,
                        'razor_key' => $paymentStatus === 'completed' ? 'rzp_test_' . Str::random(14) : null,
                        'base_plan_id' => $baseplan?->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );

                // Create linked coupons for completed purchases
                if ($paymentStatus === 'completed') {
                    $couponsToCreate = $campaign->coupons_per_campaign * $quantity;
                    $couponsToCreate = min($couponsToCreate, 10); // Limit coupons per purchase

                    for ($c = 0; $c < $couponsToCreate; $c++) {
                        $seriesLabel = $campaign->series_prefix ?? chr(65 + ($campaign->id % 26));
                        $couponNumber = $seriesLabel . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

                        UserCoupons::firstOrCreate(
                            [
                                'purchased_camp_id' => $purchase->id,
                                'coupon_code' => $couponNumber,
                            ],
                            [
                                'purchased_camp_id' => $purchase->id,
                                'coupon_code' => $couponNumber,
                                'coupon_expires' => Carbon::parse($createdAt)->addDays(30)->toDateTimeString(),
                                'coins' => intval($campaign->coins_per_campaign / max(1, $couponsToCreate)),
                                'is_claimed' => rand(0, 1),
                                'main_campaign_id' => $campaign->id,
                                'series_label' => $seriesLabel,
                                'status' => 1,
                                'created_at' => $createdAt,
                                'updated_at' => $createdAt,
                            ]
                        );
                    }
                }

                $purchaseCount++;

                if ($purchaseCount >= 30) {
                    break 2;
                }
            }
        }

        $this->command->info("PurchasedCoinsSeeder completed. {$purchaseCount} purchase orders seeded.");
        $this->command->info("  - Completed payments include Razorpay payment IDs");
        $this->command->info("  - Linked coupons created for completed purchases");
    }
}
