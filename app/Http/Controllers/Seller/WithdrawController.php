<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawMethod;
use App\Models\SellerWithdraw;
use App\Models\OrderProduct;
use App\Models\Setting;
use Auth;
class WithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(){
        $user = Auth::guard('api')->user();
        $seller = $user->seller;
        $withdraws = SellerWithdraw::where('seller_id',$seller->id)->get();

        return response()->json(['withdraws' => $withdraws], 200);

    }

    public function show($id){
        $withdraw = SellerWithdraw::find($id);


        return response()->json(['withdraw' => $withdraw], 200);
    }

    public function create(){
        $methods = WithdrawMethod::whereStatus('1')->get();

        return response()->json(['methods' => $methods], 200);
    }

    public function getWithDrawAccountInfo($id){
        $method = WithdrawMethod::whereId($id)->first();

        return response()->json(['method' => $method], 200);
    }

    public function store(Request $request){
        $rules = [
            'method_id' => 'required',
            'withdraw_amount' => 'required|numeric',
            'account_info' => 'required',
        ];

        $customMessages = [
            'method_id.required' => trans('Payment Method filed is required'),
            'withdraw_amount.required' => trans('Withdraw amount filed is required'),
            'withdraw_amount.numeric' => trans('Please provide valid numeric number'),
            'account_info.required' => trans('Account filed is required'),
        ];

        $this->validate($request, $rules, $customMessages);

        $user = Auth::guard('api')->user();
        $seller = $user->seller;
        $totalAmount = 0;
        $orderProducts = OrderProduct::with('order')->where('seller_id',$seller->id)->get();
        foreach($orderProducts as $orderProduct){
            if($orderProduct->order->payment_status == 1 && $orderProduct->order->order_status == 3){
                $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                $totalAmount = $totalAmount + $price;
            }
        }

        $totalWithdraw = SellerWithdraw::where('seller_id',$seller->id)->where('status',1)->sum('withdraw_amount');
        $currentAmount = $totalAmount - $totalWithdraw;
        if($request->withdraw_amount > $currentAmount){
            $notification = trans('Sorry! Your Payment request is more then your current balance');
            return response()->json(['notification' => $notification], 400);
        }

        $method = WithdrawMethod::whereId($request->method_id)->first();
        if($request->withdraw_amount >= $method->min_amount && $request->withdraw_amount <= $method->max_amount){
            $user = Auth::guard('api')->user();
            $seller = $user->seller;
            $widthdraw = new SellerWithdraw();
            $widthdraw->seller_id = $seller->id;
            $widthdraw->method = $method->name;
            $widthdraw->total_amount = $request->withdraw_amount;
            $withdraw_request = $request->withdraw_amount;
            $withdraw_charge = ($method->withdraw_charge / 100) * $withdraw_request;
            $widthdraw->withdraw_amount = $request->withdraw_amount - $withdraw_charge;
            $widthdraw->withdraw_charge = $withdraw_charge;
            $widthdraw->account_info = $request->account_info;
            $widthdraw->save();
            $notification = trans('Withdraw request send successfully, please wait for admin approval');
            return response()->json(['notification' => $notification], 200);

        }else{
            $notification = trans('Your amount range is not available');
            return response()->json(['notification' => $notification], 400);
        }

    }
}
