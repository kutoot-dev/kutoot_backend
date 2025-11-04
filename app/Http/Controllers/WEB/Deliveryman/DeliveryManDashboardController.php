<?php

namespace App\Http\Controllers\WEB\Deliveryman;

use Auth;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\DeliveryManWithdraw;
use App\Http\Controllers\Controller;

class DeliveryManDashboardController extends Controller
{
    public function index(){
        $data=[];
        $deliveryman_id=Auth::guard('deliveryman')->user()->id;
        $data['totalOrderRequest']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 0)->whereDay('order_req_date', now()->day)->get();
        $data['todayAcceptOrders']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', '>=', 1)->whereDay('order_req_accept_date', now()->day)->get();
        $data['runnignOrder']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 1)->get();
        $data['totalCompletedOrder']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 3)->get();
        $data['totalDeclinedOrder']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 4)->get();
        $data['todayEarning']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', 3)->whereDay('order_completed_date', now()->day)->sum('shipping_cost');
        $data['thisMonthEarning']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', 3)->whereMonth('order_completed_date', now()->month)->sum('shipping_cost');
        $data['thisYearEarning']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', 3)->whereYear('order_completed_date', now()->year)->sum('shipping_cost');
        $data['totalEarning']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', 3)->sum('shipping_cost');
        $data['runningOrder']=Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 1)->get();
        $data['orders'] = Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 1)->get();
        $data['title'] = trans('admin_validation.All Orders');
        $data['deliveryManWithdraw']=DeliveryManWithdraw::where('delivery_man_id', $deliveryman_id)->where('status', 1)->sum('total_amount');
        $data['deliveryManPendingWithdraw']=DeliveryManWithdraw::where('delivery_man_id', $deliveryman_id)->where('status', 0)->sum('total_amount');
        $data['setting'] = Setting::first();
        return view('deliveryman.dashboard', $data);
    }
}
