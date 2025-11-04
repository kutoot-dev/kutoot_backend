<?php

namespace App\Http\Controllers\Deliveryman;

use Auth;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\DeliveryManWithdraw;
use App\Http\Controllers\Controller;

class DeliveryManDashboardController extends Controller
{
    public function index(){
        $deliveryman_id=Auth::guard('deliveryman-api')->user()->id;
        $totalOrderRequest=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 0)->whereDay('order_req_date', now()->day)->get();
        $todayAcceptOrders=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', '>=', 1)->whereDay('order_req_accept_date', now()->day)->get();
        $runnignOrder=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 1)->get();
        $totalCompletedOrder=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 3)->get();
        $totalDeclinedOrder=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 4)->get();
        $todayEarning=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', 3)->whereDay('order_completed_date', now()->day)->sum('shipping_cost');
        $thisMonthEarning=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', 3)->whereMonth('order_completed_date', now()->month)->sum('shipping_cost');
        $thisYearEarning=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', 3)->whereYear('order_completed_date', now()->year)->sum('shipping_cost');
        $totalEarning=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', 3)->sum('shipping_cost');
        $runningOrder=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 1)->get();
        $orders = Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 1)->get();
        
        $deliveryManWithdraw=DeliveryManWithdraw::where('delivery_man_id', $deliveryman_id)->where('status', 1)->sum('total_amount');
        $deliveryManPendingWithdraw=DeliveryManWithdraw::where('delivery_man_id', $deliveryman_id)->where('status', 0)->sum('total_amount');
        $setting = Setting::first();
        return response()->json([
            'totalOrderRequest' => $totalOrderRequest->count(),
            'todayAcceptOrders' => $todayAcceptOrders->count(),
            'runnignOrder' => $runnignOrder->count(),
            'totalCompletedOrder' => $totalCompletedOrder->count(),
            'totalDeclinedOrder' => $totalDeclinedOrder->count(),
            'todayEarning' => $todayEarning,
            'thisMonthEarning' => $thisMonthEarning,
            'thisYearEarning' => $thisYearEarning,
            'totalEarning' => $totalEarning,
            'runningOrder' => $runningOrder,
            'orders' => $orders,
            'deliveryManWithdraw' => $deliveryManWithdraw,
            'deliveryManPendingWithdraw' => $deliveryManPendingWithdraw,
            'setting' => $setting,
        ]);
    }
}
