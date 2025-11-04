<?php

namespace App\Http\Controllers\Deliveryman;

use Auth;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\DeliveryManWithdraw;
use App\Http\Controllers\Controller;
use App\Models\DeliveryManWithdrawMethod;

class MyWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveryman_id = Auth::guard('deliveryman-api')->user()->id;
        $withdraws = DeliveryManWithdraw::where('delivery_man_id', $deliveryman_id)->get();
        $setting = Setting::first();

        return response()->json([
            'withdraws' => $withdraws,
            'setting' => $setting,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $methods = DeliveryManWithdrawMethod::whereStatus('1')->get();

        return response()->json([
            'methods' => $methods,
        ], 200);
    }

    public function getWithDrawAccountInfo($id){
        $method = DeliveryManWithdrawMethod::whereId($id)->first();

        $setting = Setting::first();
        return response()->json(['method' => $method, 'setting' => $setting], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'method_id' => 'required',
            'withdraw_amount' => 'required|numeric',
            'account_info' => 'required',
        ];

        $customMessages = [
            'method_id.required' => trans('admin_validation.Payment Method filed is required'),
            'withdraw_amount.required' => trans('admin_validation.Withdraw amount filed is required'),
            'withdraw_amount.numeric' => trans('admin_validation.Please provide valid numeric number'),
            'account_info.required' => trans('admin_validation.Account filed is required'),
        ];

        $this->validate($request, $rules, $customMessages);

        $deliveryman_id = Auth::guard('deliveryman-api')->user()->id;
        $totalAmount = 0;
        $orders = Order::where('delivery_man_id',$deliveryman_id)->get();
        foreach($orders as $order){
            if($order->payment_status == 1 && $order->order_status == 3){
                $totalAmount = $totalAmount + $order->shipping_cost;
            }
        }
        $totalWithdraw = DeliveryManWithdraw::where('delivery_man_id',$deliveryman_id)->where('status',1)->sum('withdraw_amount');
        $currentAmount = $totalAmount - $totalWithdraw;
        if($request->withdraw_amount > $currentAmount){
            $notification = trans('admin_validation.Sorry! Your Payment request is more then your current balance');
            return response()->json(['notification' => $notification], 400);
        }
        $method = DeliveryManWithdrawMethod::whereId($request->method_id)->first();
        if($request->withdraw_amount >= $method->min_amount && $request->withdraw_amount <= $method->max_amount){
            $widthdraw = new DeliveryManWithdraw();
            $widthdraw->delivery_man_id = $deliveryman_id;
            $widthdraw->method = $method->name;
            $widthdraw->total_amount = $request->withdraw_amount;
            $withdraw_request = $request->withdraw_amount;
            $withdraw_charge = ($method->withdraw_charge / 100) * $withdraw_request;
            $widthdraw->withdraw_amount = $request->withdraw_amount - $withdraw_charge;
            $widthdraw->withdraw_charge = $withdraw_charge;
            $widthdraw->account_info = $request->account_info;
            $widthdraw->save();
            $notification = trans('admin_validation.Withdraw request send successfully, please wait for admin approval');
            return response()->json(['notification' => $notification], 200);

        }else{
            $notification = trans('admin_validation.Your amount range is not available');
            return response()->json(['notification' => $notification], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $withdraw = DeliveryManWithdraw::find($id);


        $setting = Setting::first();
        return response()->json(['withdraw' => $withdraw, 'setting' => $setting], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
