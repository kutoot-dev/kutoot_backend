<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use App\Models\OrderProduct;
use App\Models\OrderProductVariant;
use App\Models\OrderAddress;
use Auth;
/**
 * @group Seller Order
 */
class SellerOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(){
        $seller = Auth::guard('api')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->paginate(15);
        $title = trans('All Orders');

        return response()->json(['orders' => $orders, 'title' => $title], 200);
    }

    public function pendingOrder(){
        $seller = Auth::guard('api')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',0)->paginate(15);
        $title = trans('Pending Orders');

        return response()->json(['orders' => $orders, 'title' => $title], 200);
    }

    public function progressOrder(){
        $seller = Auth::guard('api')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',1)->paginate(15);
        $title = trans('Progress Orders');

        return response()->json(['orders' => $orders, 'title' => $title], 200);
    }

    public function deliveredOrder(){
        $seller = Auth::guard('api')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',2)->paginate(15);
        $title = trans('Delivered Orders');

        return response()->json(['orders' => $orders, 'title' => $title], 200);
    }

    public function completedOrder(){
        $seller = Auth::guard('api')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',3)->paginate(15);
        $title = trans('Completed Orders');

        return response()->json(['orders' => $orders, 'title' => $title], 200);
    }

    public function declinedOrder(){
        $seller = Auth::guard('api')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('order_status',4)->paginate(15);
        $title = trans('Declined Orders');

        return response()->json(['orders' => $orders, 'title' => $title], 200);
    }

    public function cashOnDelivery(){
        $seller = Auth::guard('api')->user()->seller;
        $orders = Order::with('user')->whereHas('orderProducts',function($query) use ($seller){
            $query->where(['seller_id' => $seller->id]);
        })->orderBy('id','desc')->where('cash_on_delivery',1)->paginate(15);

        $title = trans('Cash On Delivery');

        return response()->json(['orders' => $orders, 'title' => $title], 200);
    }

    public function show($id){
        $order = Order::with('user','orderProducts.orderProductVariants','orderAddress')->find($id);

        return response()->json(['order' => $order], 200);

    }
}
