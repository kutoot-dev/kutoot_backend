<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchasedCoins;
use App\Models\Store\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * @group User Order
 */
class UserOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();
        $type = $request->get('type'); // Campaign / Store
        $status = $request->get('status');
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');
        $limit = $request->get('limit', 10);

        $orders = collect();

        if (!$type || $type === 'Campaign') {
            $campaignQuery = PurchasedCoins::with('campaign')
                ->where('user_id', $user->id);

            if ($status) {
                // Map status if needed, but spec shows 'Success' / 'Failed' / 'Pending'
                $campaignQuery->where('payment_status', $status);
            }

            if ($fromDate) {
                $campaignQuery->whereDate('created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $campaignQuery->whereDate('created_at', '<=', $toDate);
            }

            $campaigns = $campaignQuery->get()->map(function ($purchased) {
                $campaign = $purchased->campaign;
                return [
                    'id' => 'ORD-CAM-' . $purchased->id,
                    'type' => 'Campaign',
                    'title' => $campaign->title ?? 'Campaign Order',
                    'description' => $campaign->description ?? '',
                    'image' => $campaign->img ?? asset('/images/placeholder-campaign.jpg'),
                    'coins' => $purchased->camp_coins_per_campaign ?? 0,
                    'progress' => $campaign ? $campaign->marketingManifest()['display_percentage'] : 0,
                    'bill' => [
                        'orderId' => 'ORD-CAM-' . $purchased->id,
                        'storeOrCampaign' => $campaign->title ?? 'Campaign Order',
                        'amount' => $purchased->camp_ticket_price * $purchased->quantity,
                        'discount' => 0, // Assuming 0 for now as per spec example
                        'finalPaid' => $purchased->camp_ticket_price * $purchased->quantity,
                        'paymentMode' => 'Razorpay', // Or map from actual payment data
                        'status' => ucfirst($purchased->payment_status),
                        'createdAt' => Carbon::parse($purchased->created_at)->format('Y-m-d h:i A')
                    ],
                    'created_at' => $purchased->created_at
                ];
            });
            $orders = $orders->concat($campaigns);
        }

        if (!$type || $type === 'Store') {
            $storeQuery = Transaction::with('sellerApplication')
                ->whereHas('visitor', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });

            if ($status) {
                $storeQuery->where('status', $status);
            }

            if ($fromDate) {
                $storeQuery->whereDate('created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $storeQuery->whereDate('created_at', '<=', $toDate);
            }

            $storeTransactions = $storeQuery->get()->map(function ($txn) {
                $application = $txn->sellerApplication;
                return [
                    'id' => 'ORD-STR-' . $txn->id,
                    'type' => 'Store',
                    'title' => $application->store_name ?? 'Store Order',
                    'description' => '<p>Instant coin redeem bill at partner store near you.</p>', // from spec
                    'image' => $application->store_image ?? asset('/images/placeholder-store.jpg'),
                    'coins' => $txn->redeemed_coins,
                    'progress' => 100, // from spec for store
                    'bill' => [
                        'orderId' => 'ORD-STR-' . $txn->id,
                        'storeOrCampaign' => $application->store_name ?? 'Store Order',
                        'amount' => $txn->total_amount,
                        'discount' => $txn->discount_amount,
                        'finalPaid' => $txn->total_amount - $txn->discount_amount,
                        'paymentMode' => 'UPI', // spec example
                        'status' => ucfirst($txn->status),
                        'createdAt' => Carbon::parse($txn->created_at)->format('Y-m-d h:i A')
                    ],
                    'created_at' => $txn->created_at
                ];
            });
            $orders = $orders->concat($storeTransactions);
        }

        $sortedOrders = $orders->sortByDesc('created_at');
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedOrders->forPage($request->get('page', 1), $limit)->values(),
            $sortedOrders->count(),
            $limit,
            $request->get('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Orders fetched successfully',
            'data' => $paginated->items(),
            'pagination' => [
                'page' => $paginated->currentPage(),
                'limit' => $paginated->perPage(),
                'total' => $paginated->total(),
                'totalPages' => $paginated->lastPage()
            ]
        ]);
    }

    public function show($orderId)
    {
        $user = Auth::guard('api')->user();

        if (str_starts_with($orderId, 'ORD-CAM-')) {
            $id = str_replace('ORD-CAM-', '', $orderId);
            $purchased = PurchasedCoins::with('campaign')
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$purchased) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            $campaign = $purchased->campaign;
            $data = [
                'id' => 'ORD-CAM-' . $purchased->id,
                'type' => 'Campaign',
                'title' => $campaign->title ?? 'Campaign Order',
                'description' => $campaign->description ?? '',
                'image' => $campaign->img ?? asset('/images/placeholder-campaign.jpg'),
                'coins' => $purchased->camp_coins_per_campaign ?? 0,
                'progress' => $campaign ? $campaign->marketingManifest()['display_percentage'] : 0,
                'bill' => [
                    'orderId' => 'ORD-CAM-' . $purchased->id,
                    'storeOrCampaign' => $campaign->title ?? 'Campaign Order',
                    'amount' => $purchased->camp_ticket_price * $purchased->quantity,
                    'discount' => 0,
                    'finalPaid' => $purchased->camp_ticket_price * $purchased->quantity,
                    'paymentMode' => 'Razorpay',
                    'status' => ucfirst($purchased->payment_status),
                    'createdAt' => Carbon::parse($purchased->created_at)->format('Y-m-d h:i A')
                ]
            ];
        } elseif (str_starts_with($orderId, 'ORD-STR-')) {
            $id = str_replace('ORD-STR-', '', $orderId);
            $txn = Transaction::with('sellerApplication')
                ->whereHas('visitor', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('id', $id)
                ->first();

            if (!$txn) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            $application = $txn->sellerApplication;
            $data = [
                'id' => 'ORD-STR-' . $txn->id,
                'type' => 'Store',
                'title' => $application->store_name ?? 'Store Order',
                'description' => '<p>Instant coin redeem bill at partner store near you.</p>',
                'image' => $application->store_image ?? asset('/images/placeholder-store.jpg'),
                'coins' => $txn->redeemed_coins,
                'progress' => 100,
                'bill' => [
                    'orderId' => 'ORD-STR-' . $txn->id,
                    'storeOrCampaign' => $application->store_name ?? 'Store Order',
                    'amount' => $txn->total_amount,
                    'discount' => $txn->discount_amount,
                    'finalPaid' => $txn->total_amount - $txn->discount_amount,
                    'paymentMode' => 'UPI',
                    'status' => ucfirst($txn->status),
                    'createdAt' => Carbon::parse($txn->created_at)->format('Y-m-d h:i A')
                ]
            ];
        } else {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order details fetched',
            'data' => $data
        ]);
    }

    public function count()
    {
        $user = Auth::guard('api')->user();

        $campaignCount = PurchasedCoins::where('user_id', $user->id)->count();
        $storeCount = Transaction::whereHas('visitor', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        return response()->json([
            'success' => true,
            'message' => 'Counts fetched',
            'data' => [
                'Campaign' => $campaignCount,
                'Store' => $storeCount
            ]
        ]);
    }

    public function campaignOrders(Request $request)
    {
        $request->merge(['type' => 'Campaign']);
        return $this->index($request);
    }

    public function storeOrders(Request $request)
    {
        $request->merge(['type' => 'Store']);
        return $this->index($request);
    }
}
