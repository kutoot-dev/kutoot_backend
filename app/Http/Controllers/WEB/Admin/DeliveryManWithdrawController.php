<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Models\Setting;
use App\Helpers\MailHelper;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\DeliveryManWithdraw;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeliveryManWithdrawApproval;

class DeliveryManWithdrawController extends Controller
{
    public function index(){
        $withdraws = DeliveryManWithdraw::with('deliveryman')->orderBy('id','desc')->get();
        $setting = Setting::first();
        
        return view('admin.delivery_man_withdraw', compact('withdraws','setting'));
    }
    
    public function pendingDeliveryManWithdraw(){
        $withdraws = DeliveryManWithdraw::with('deliveryman')->orderBy('id','desc')->where('status',0)->get();
        $setting = Setting::first();

        return view('admin.delivery_man_withdraw', compact('withdraws','setting'));
    }

    public function show($id){
        $setting = Setting::first();
        $withdraw = DeliveryManWithdraw::with('deliveryman')->find($id);
        return view('admin.show_delivery_man_withdraw', compact('withdraw','setting'));
    }

    public function destroy($id){
        $withdraw = DeliveryManWithdraw::find($id);
        $withdraw->delete();
        $notification = trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.delivery-man-withdraw')->with($notification);
    }

    public function approvedWithdraw($id){
        $withdraw = DeliveryManWithdraw::find($id);
        $withdraw->status = 1;
        $withdraw->approved_date = date('Y-m-d');
        $withdraw->save();

        $deliveryman_name = $withdraw->deliveryman->fname.' '.$withdraw->deliveryman->lname;
        $deliveryman_email = $withdraw->deliveryman->email;
        $template=EmailTemplate::where('id',8)->first();
        $message=$template->description;
        $subject=$template->subject;
        $message=str_replace('{{delivery_man_name}}',$deliveryman_name,$message);
        $message=str_replace('{{withdraw_method}}',$withdraw->method,$message);
        $message=str_replace('{{total_amount}}',$withdraw->total_amount,$message);
        $message=str_replace('{{withdraw_charge}}',$withdraw->withdraw_charge,$message);
        $message=str_replace('{{withdraw_amount}}',$withdraw->withdraw_amount,$message);
        $message=str_replace('{{approval_date}}',$withdraw->approved_date,$message);
        MailHelper::setMailConfig();
        Mail::to($deliveryman_email)->send(new DeliveryManWithdrawApproval($subject,$message));

        $notification = trans('admin_validation.Withdraw request approval successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.delivery-man-withdraw')->with($notification);
    }

    public function withdrawList($id){
        $withdraws = DeliveryManWithdraw::with('deliveryman')->where('delivery_man_id', $id)->orderBy('id','desc')->get();
        $deliveryman = DeliveryMan::whereId($id)->first();
        $setting = Setting::first();

        return view('admin.delivery_man_withdraw_list', compact('withdraws', 'deliveryman', 'setting'));
    }
}
