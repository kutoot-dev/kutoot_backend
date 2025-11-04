<?php

namespace App\Http\Controllers\WEB\Seller;

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
        $this->middleware('auth:web');
    }

    public function index(){
        $user = Auth::guard('web')->user();
        $seller = $user->seller;
        $withdraws = SellerWithdraw::where('seller_id',$seller->id)->get();
        $setting = Setting::first();
        return view('seller.withdraw', compact('withdraws','setting'));

    }

    public function show($id){
        $withdraw = SellerWithdraw::find($id);


        $setting = Setting::first();
        return view('seller.show_withdraw', compact('withdraw','setting'));
    }

    public function create(){
        $methods = WithdrawMethod::whereStatus('1')->get();

        $setting = Setting::first();
        return view('seller.create_withdraw', compact('methods','setting'));
    }

    public function getWithDrawAccountInfo($id){
        $method = WithdrawMethod::whereId($id)->first();

        $setting = Setting::first();
        return view('seller.withdraw_account_info', compact('method','setting'));
    }

    public function store(Request $request){
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

        $user = Auth::guard('web')->user();
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
            $notification = trans('admin_validation.Sorry! Your Payment request is more then your current balance');
            return response()->json(['notification' => $notification], 400);
        }

        $method = WithdrawMethod::whereId($request->method_id)->first();
        if($request->withdraw_amount >= $method->min_amount && $request->withdraw_amount <= $method->max_amount){
            $user = Auth::guard('web')->user();
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
            $notification = trans('admin_validation.Withdraw request send successfully, please wait for admin approval');
            $notification=array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->route('seller.my-withdraw.index')->with($notification);

        }else{
            $notification = trans('admin_validation.Your amount range is not available');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

    }
}
