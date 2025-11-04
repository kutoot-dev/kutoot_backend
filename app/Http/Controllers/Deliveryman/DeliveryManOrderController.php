<?php

namespace App\Http\Controllers\Deliveryman;

use Auth;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryManOrderController extends Controller
{
    public function index(){
        $deliveryman_id=Auth::guard('deliveryman-api')->user()->id;
        $orders = Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', 1)->get();
        $setting = Setting::first();
        return response()->json([
            'orders' => $orders,
            'setting' => $setting,
        ]);
    }
    public function orderRequest(){
        $deliveryman_id=Auth::guard('deliveryman-api')->user()->id;
        $orders = Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_request', '=', 0)->orderBy('id','desc')->get();
        $setting = Setting::first();
        return response()->json([
            'orders' => $orders,
            'setting' => $setting,
        ]);
    }
    
    public function completedOrder(){
        $deliveryman_id=Auth::guard('deliveryman-api')->user()->id;
        $orders = Order::with('user')->where('delivery_man_id', $deliveryman_id)->where('order_status', '=', 3)->where('order_request', '=', 3)->orderBy('id','desc')->get();
        $title = trans('Complete Order');
        $setting = Setting::first();
        return response()->json([
            'orders' => $orders,
            'title' => $title,
            'setting' => $setting,
        ]);
    }
    public function show($id){
        $order = Order::with('user','orderProducts.orderProductVariants','orderAddress')->find($id);
        $setting = Setting::first();
        return response()->json([
            'order' => $order,
            'setting' => $setting,
        ]);
    }
    public function updateOrderStatus(Request $request , $id){
        $rules = [
            'order_status' =>'required',
            'payment_status' => 'required',
        ];
        $this->validate($request, $rules);
        $order = Order::find($id);
         if($request->order_status == 3){
            $order->order_request=3;
            $order->order_status =3;
            $order->order_completed_date = date('Y-m-d');
            $order->save();
        }else if($request->order_status == 4){
            $order->order_request=4;
            $order->order_status = 4;
            $order->order_declined_date = date('Y-m-d');
            $order->save();
        }

        if($request->payment_status == 0){
            $order->payment_status = 0;
            $order->save();
        }elseif($request->payment_status == 1){
            $order->payment_status = 1;
            $order->payment_approval_date = date('Y-m-d');
            $order->save();
        }
        $notification = trans('admin_validation.Order Status Updated successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return response()->json(['message' => $notification],200);
    }

    public function orderRequestStatus(Request $request, $id){
        $rules = [
            'order_request_status' => 'required',
        ];
        $this->validate($request, $rules);
        $deliveryman_id=Auth::guard('deliveryman-api')->user()->id;
        $order = Order::where('id', $id)->where('delivery_man_id', $deliveryman_id)->first();
        if($request->order_request_status == 1){
            $order->order_request=1;
            $order->order_req_accept_date = date('Y-m-d');
            $order->save();
        }elseif($request->order_request_status == 2){
            $order->order_request=2;
            $order->save();
        }
        $notification = trans('Order Request Status Updated successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return response()->json(['message' => $notification],200);
    }
}
