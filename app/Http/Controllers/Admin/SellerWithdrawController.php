<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawMethod;
use App\Models\SellerWithdraw;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Helpers\MailHelper;
use App\Mail\SellerWithdrawApproval;
use Mail;
use Auth;

class SellerWithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $withdraws = SellerWithdraw::with('seller')->orderBy('id','desc')->get();
        $setting = Setting::first();

        return response()->json(['withdraws' => $withdraws, 'setting' => $setting], 200);
    }

    public function pendingSellerWithdraw(){
        $withdraws = SellerWithdraw::with('seller')->orderBy('id','desc')->where('status',0)->get();
        $setting = Setting::first();

        return response()->json(['withdraws' => $withdraws, 'setting' => $setting], 200);
    }

    public function show($id){
        $setting = Setting::first();
        $withdraw = SellerWithdraw::with('seller')->find($id);
        return response()->json(['withdraw' => $withdraw, 'setting' => $setting], 200);
    }

    public function destroy($id){
        $withdraw = SellerWithdraw::find($id);
        $withdraw->delete();
        $notification = trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function approvedWithdraw($id){
        $withdraw = SellerWithdraw::find($id);
        $withdraw->status = 1;
        $withdraw->approved_date = date('Y-m-d');
        $withdraw->save();

        $user = $withdraw->seller->user;
        $template=EmailTemplate::where('id',5)->first();
        $message=$template->description;
        $subject=$template->subject;
        $message=str_replace('{{seller_name}}',$user->name,$message);
        $message=str_replace('{{withdraw_method}}',$withdraw->method,$message);
        $message=str_replace('{{total_amount}}',$withdraw->total_amount,$message);
        $message=str_replace('{{withdraw_charge}}',$withdraw->withdraw_charge,$message);
        $message=str_replace('{{withdraw_amount}}',$withdraw->withdraw_amount,$message);
        $message=str_replace('{{approval_date}}',$withdraw->approved_date,$message);
        MailHelper::setMailConfig();
        Mail::to($user->email)->send(new SellerWithdrawApproval($subject,$message));

        $notification = trans('Withdraw request approval successfully');
        return response()->json(['notification' => $notification], 200);
    }
}
