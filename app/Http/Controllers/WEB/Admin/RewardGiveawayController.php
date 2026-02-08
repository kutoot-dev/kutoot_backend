<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CoinCampaigns;
use App\Models\Baseplans;
use App\Models\PurchasedCoins;
use App\Models\UserCoupons;
use App\Models\CoinLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RewardGiveawayController extends Controller
{
    /**
     * Show the giveaway form
     */
    public function index()
    {
        $users = User::where('status', 1)
            ->select('id', 'name', 'email', 'phone')
            ->orderBy('name')
            ->get();

        $campaigns = CoinCampaigns::where('status', 1)
            ->select('id', 'title', 'series_prefix', 'number_min', 'number_max', 'numbers_per_ticket', 'total_tickets', 'max_coins', 'max_coupons')
            ->orderBy('title')
            ->get();

        $baseplans = Baseplans::where('status', 1)
            ->select('id', 'title', 'coins_per_campaign', 'coupons_per_campaign')
            ->orderBy('title')
            ->get();

        return view('admin.rewards.giveaway', compact('users', 'campaigns', 'baseplans'));
    }

    /**
     * Preview coupons before distribution
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'campaign_id' => 'required|exists:coin_campaigns,id',
            'baseplan_id' => 'required|exists:coinbase_plans,id',
            'quantity' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $campaign = CoinCampaigns::findOrFail($request->campaign_id);
        $baseplan = Baseplans::findOrFail($request->baseplan_id);
        $users = User::whereIn('id', $request->user_ids)->get();

        // Calculate total items per user
        $coinsPerUser = $baseplan->coins_per_campaign * $request->quantity;
        $couponsPerUser = $baseplan->coupons_per_campaign * $request->quantity;

        // Calculate distributed coins and coupons for this campaign
        $distributedCoins = PurchasedCoins::where('camp_id', $request->campaign_id)
            ->where('payment_status', 'success')
            ->sum(DB::raw('camp_coins_per_campaign * quantity'));

        $distributedCoupons = PurchasedCoins::where('camp_id', $request->campaign_id)
            ->where('payment_status', 'success')
            ->sum(DB::raw('camp_coupons_per_campaign * quantity'));

        // Calculate what will be distributed with this giveaway
        $totalCoinsToDistribute = $coinsPerUser * count($users);
        $totalCouponsToDistribute = $couponsPerUser * count($users);

        // Check max limits if defined
        if ($campaign->max_coins && ($distributedCoins + $totalCoinsToDistribute) > $campaign->max_coins) {
            $available = $campaign->max_coins - $distributedCoins;
            return response()->json([
                'success' => false,
                'message' => "Not enough coins available! Need: {$totalCoinsToDistribute}, Available: {$available}, Max: {$campaign->max_coins}"
            ], 400);
        }

        if ($campaign->max_coupons && ($distributedCoupons + $totalCouponsToDistribute) > $campaign->max_coupons) {
            $available = $campaign->max_coupons - $distributedCoupons;
            return response()->json([
                'success' => false,
                'message' => "Not enough coupons available! Need: {$totalCouponsToDistribute}, Available: {$available}, Max: {$campaign->max_coupons}"
            ], 400);
        }

        // Generate preview coupons for each user
        $preview = [];
        $totaltickets = $campaign->total_tickets;
        $single_length_tickets = $campaign->total_tickets;
        $prefix = null;
        $seriesLabels = [];

        // Calculate series information
        if ($campaign->series_prefix) {
            $prefix = strtoupper($campaign->series_prefix);
            $firstChar = $prefix[0];
            if (ctype_alpha($firstChar)) {
                $numberOfSeries = ord($firstChar) - 64;
                $single_length_tickets = round($campaign->total_tickets / $numberOfSeries);
                for ($i = 0; $i < $numberOfSeries; $i++) {
                    $seriesLabels[] = chr(65 + $i);
                }
            }
        }

        // Check available tickets
        $alreadyPurchasedCouponsCount = PurchasedCoins::where('camp_id', $request->campaign_id)
            ->where('payment_status', 'success')
            ->sum(DB::raw('camp_coupons_per_campaign * quantity'));

        $totalCouponsNeeded = $couponsPerUser * count($users);
        $availableTickets = $totaltickets - $alreadyPurchasedCouponsCount;

        if ($totalCouponsNeeded > $availableTickets) {
            return response()->json([
                'success' => false,
                'message' => "Not enough tickets available! Need: {$totalCouponsNeeded}, Available: {$availableTickets}"
            ], 400);
        }

        foreach ($users as $user) {
            $coupons = [];

            // Generate sample coupon codes for preview
            for ($j = 0; $j < $couponsPerUser; $j++) {
                // Determine series label
                $currentSeriesLabel = null;
                if (!empty($seriesLabels)) {
                    $totalExistingForCampaign = UserCoupons::where('main_campaign_id', $campaign->id)->count();
                    $seriesIndex = floor(($totalExistingForCampaign + $j) / $single_length_tickets);
                    $currentSeriesLabel = $seriesLabels[$seriesIndex] ?? end($seriesLabels);
                } else {
                    $currentSeriesLabel = $prefix;
                }

                // Generate unique code
                $code = $this->generateUniqueCouponCode($campaign, $currentSeriesLabel);

                $coupons[] = [
                    'code' => $code,
                    'series_label' => $currentSeriesLabel,
                    'expires' => now()->addDays(30)->format('Y-m-d H:i:s')
                ];
            }

            $preview[] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone
                ],
                'coins' => $coinsPerUser,
                'coupons' => $coupons
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'campaign' => [
                    'id' => $campaign->id,
                    'title' => $campaign->title
                ],
                'baseplan' => [
                    'id' => $baseplan->id,
                    'title' => $baseplan->title,
                    'coins_per_campaign' => $baseplan->coins_per_campaign,
                    'coupons_per_campaign' => $baseplan->coupons_per_campaign
                ],
                'quantity' => $request->quantity,
                'preview' => $preview,
                'summary' => [
                    'total_users' => count($users),
                    'coins_per_user' => $coinsPerUser,
                    'coupons_per_user' => $couponsPerUser,
                    'total_coins' => $totalCoinsToDistribute,
                    'total_coupons' => $totalCouponsToDistribute
                ],
                'campaign_limits' => [
                    'max_coins' => $campaign->max_coins,
                    'max_coupons' => $campaign->max_coupons,
                    'distributed_coins' => $distributedCoins,
                    'distributed_coupons' => $distributedCoupons,
                    'remaining_coins' => $campaign->max_coins ? ($campaign->max_coins - $distributedCoins) : null,
                    'remaining_coupons' => $campaign->max_coupons ? ($campaign->max_coupons - $distributedCoupons) : null,
                    'after_distribution_coins' => $campaign->max_coins ? ($campaign->max_coins - $distributedCoins - $totalCoinsToDistribute) : null,
                    'after_distribution_coupons' => $campaign->max_coupons ? ($campaign->max_coupons - $distributedCoupons - $totalCouponsToDistribute) : null,
                ]
            ]
        ]);
    }

    /**
     * Distribute rewards to users
     */
    public function distribute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'campaign_id' => 'required|exists:coin_campaigns,id',
            'baseplan_id' => 'required|exists:coinbase_plans,id',
            'quantity' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $campaign = CoinCampaigns::findOrFail($request->campaign_id);
        $baseplan = Baseplans::findOrFail($request->baseplan_id);
        $users = User::whereIn('id', $request->user_ids)->get();

        DB::beginTransaction();
        try {
            $distributionResults = [];

            foreach ($users as $user) {
                // Create a purchase record for tracking
                $purchase = PurchasedCoins::create([
                    'camp_id' => $campaign->id,
                    'user_id' => $user->id,
                    'base_plan_id' => $baseplan->id,
                    'quantity' => $request->quantity,
                    'camp_title' => $campaign->title,
                    'camp_description' => $campaign->description,
                    'camp_ticket_price' => 0, // Free giveaway
                    'camp_coins_per_campaign' => $baseplan->coins_per_campaign,
                    'camp_coupons_per_campaign' => $baseplan->coupons_per_campaign,
                    'status' => 1,
                    'payment_status' => 'success',
                    'payment_id' => 'ADMIN_GIVEAWAY_' . time() . '_' . $user->id,
                    'razor_order_id' => 'GIVEAWAY_' . time() . '_' . $user->id
                ]);

                // Generate and save coupons
                $totaltickets = $campaign->total_tickets;
                $single_length_tickets = $campaign->total_tickets;
                $prefix = null;
                $seriesLabels = [];

                if ($campaign->series_prefix) {
                    $prefix = strtoupper($campaign->series_prefix);
                    $firstChar = $prefix[0];
                    if (ctype_alpha($firstChar)) {
                        $numberOfSeries = ord($firstChar) - 64;
                        $single_length_tickets = round($campaign->total_tickets / $numberOfSeries);
                        for ($i = 0; $i < $numberOfSeries; $i++) {
                            $seriesLabels[] = chr(65 + $i);
                        }
                    }
                }

                $generatedCoupons = [];
                for ($j = 0; $j < $baseplan->coupons_per_campaign * $request->quantity; $j++) {
                    $currentSeriesLabel = null;
                    if (!empty($seriesLabels)) {
                        $totalExistingForCampaign = UserCoupons::where('main_campaign_id', $campaign->id)->count();
                        $seriesIndex = floor($totalExistingForCampaign / $single_length_tickets);
                        $currentSeriesLabel = $seriesLabels[$seriesIndex] ?? end($seriesLabels);
                    } else {
                        $currentSeriesLabel = $prefix;
                    }

                    $code = $this->generateUniqueCouponCode($campaign, $currentSeriesLabel);

                    $coupon = UserCoupons::create([
                        'purchased_camp_id' => $purchase->id,
                        'coupon_code' => $code,
                        'coupon_expires' => now()->addDays(30),
                        'is_claimed' => 0,
                        'status' => 1, // Active immediately
                        'main_campaign_id' => $campaign->id,
                        'series_label' => $currentSeriesLabel
                    ]);

                    $generatedCoupons[] = $coupon->coupon_code;
                }

                // Credit coins using CoinLedgerService
                if ($baseplan->coins_per_campaign > 0) {
                    $coinService = app(\App\Services\CoinLedgerService::class);
                    $coinService->creditPaid(
                        $user->id,
                        $baseplan->coins_per_campaign * $request->quantity,
                        "OID" . $purchase->id
                    );
                }

                $distributionResults[] = [
                    'user' => $user->name . ' (' . $user->email . ')',
                    'coins' => $baseplan->coins_per_campaign * $request->quantity,
                    'coupons' => count($generatedCoupons)
                ];
            }

            DB::commit();

            $notification = 'Rewards distributed successfully to ' . count($users) . ' user(s)';
            $notification = ['messege' => $notification, 'alert-type' => 'success'];

            return redirect()->route('admin.rewards.giveaway')->with($notification);

        } catch (\Exception $e) {
            DB::rollBack();

            $notification = 'Failed to distribute rewards: ' . $e->getMessage();
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);
        }
    }

    /**
     * Generate unique coupon code for campaign
     */
    private function generateUniqueCouponCode($campaign, $seriesLabel)
    {
        $maxRetries = 50;
        $retryCount = 0;

        do {
            if ($retryCount >= $maxRetries) {
                throw new \Exception('Failed to generate unique coupon code');
            }

            $numbers = [];
            while (count($numbers) < $campaign->numbers_per_ticket) {
                $num = rand($campaign->number_min, $campaign->number_max);
                $numStr = str_pad($num, 2, '0', STR_PAD_LEFT);
                if (!in_array($numStr, $numbers)) {
                    $numbers[] = $numStr;
                }
            }
            sort($numbers, SORT_STRING);
            // Format: Series-NN-NN-NN-NN-NN
            $code = $seriesLabel . '-' . implode('-', $numbers);

            $exists = UserCoupons::where('coupon_code', $code)
                ->where('main_campaign_id', $campaign->id)
                ->where('series_label', $seriesLabel)
                ->exists();

            $retryCount++;
        } while ($exists);

        return $code;
    }
}
