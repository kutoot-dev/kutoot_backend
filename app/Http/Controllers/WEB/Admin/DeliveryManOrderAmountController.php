<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Models\Order;
use App\Models\Setting;
use App\Models\DeliveryMan;
use App\Models\OrderAmount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryManOrderAmountController extends Controller
{
    public function index(){
        $orderAmounts=OrderAmount::with('deliveryman')->latest()->get();
        $setting = Setting::first();
        return view('admin.order_amount', compact('orderAmounts', 'setting'));
    }

    public function create(){
        $deliveryMans=DeliveryMan::where('status', 1)->get();
        return view('admin.create_order_amount', compact('deliveryMans'));
    }

    public function store(Request $request){
        $rules = [
            'delivery_man_id' => 'required',
            'total_amount' => 'required',
        ];
        $customMessages = [
            'delivery_man_id.required' => trans('Delivery man name is required'),
            'total_amount.required' => trans('Total amount is required'),
        ];
        $this->validate($request, $rules,$customMessages);
        $totalAmount = 0;
        $orders = Order::where('delivery_man_id',$request->delivery_man_id)->get();
        foreach($orders as $order){
            if($order->payment_status == 1 && $order->order_status == 3 && $order->cash_on_delivery==1){
                $totalAmount = $totalAmount + $order->total_amount;
            }
        }

        $totalOrderAmount = OrderAmount::where('delivery_man_id', $request->delivery_man_id)->sum('total_amount');

        $currentAmount = $totalAmount - $totalOrderAmount;
        
        if($request->total_amount > $currentAmount){
            $notification = trans('Sorry! Your Payment request is more then delivery man current acount balance');
            //return response()->json(['notification' => $notification], 400);
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        
        $orderAmount = new OrderAmount();
        $orderAmount->delivery_man_id = $request->delivery_man_id;
        $orderAmount->total_amount = $request->total_amount;
        $orderAmount->save();
        $notification = trans('Amount created successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return back()->with($notification);
    }

    public function destroy($id)
    {
        $orderAmount = OrderAmount::find($id);
        $orderAmount->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return back()->with($notification);
    }

    public function getWithDeliveryManAccountInfo($id){
        $total_order_amount = Order::where('delivery_man_id', $id)->where('cash_on_delivery', 1)->where('order_status', 3)->sum('total_amount');

        $total_receive_amount=OrderAmount::where('delivery_man_id', $id)->sum('total_amount');

        $currentBalance= $total_order_amount-$total_receive_amount;

        $setting = Setting::first();

        

        return view('admin.delivery_man_account_info', compact('currentBalance','setting'));
    }
}
