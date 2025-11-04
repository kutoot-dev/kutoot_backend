<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BannerImage;
use App\Models\Order;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\ShippingAddress;
use App\Models\BillingAddress;
use App\Models\Wishlist;
use App\Helpers\MailHelper;
use Mail;
use App\Mail\SendSingleSellerMail;
use Image;
use File;
class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $customers = User::with('city','seller','state', 'country')->orderBy('id','desc')->where('status',1)->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        $orders = Order::all();

        return response()->json(['customers' => $customers, 'defaultProfile' => $defaultProfile, 'orders' => $orders], 200);
    }

    public function pendingCustomerList(){
        $customers = User::with('city','seller','state', 'country')->orderBy('id','desc')->where('status',0)->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        $orders = Order::all();

        return response()->json(['customers' => $customers, 'defaultProfile' => $defaultProfile, 'orders' => $orders], 200);

    }

    public function show($id){
        $customer = User::with('city','seller','state', 'country')->find($id);
        if($customer){
            $defaultProfile = BannerImage::whereId('15')->first();

            return response()->json(['customer' => $customer, 'defaultProfile' => $defaultProfile], 200);

        }else{
            $notification= trans('Something Went Wrong');
            return response()->json(['notification' => $notification], 500);
        }

    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user_image = $user->image;
        $user->delete();
        if($user_image){
            if(File::exists(public_path().'/'.$user_image))unlink(public_path().'/'.$user_image);
        }
        ProductReport::where('user_id',$id)->delete();
        ProductReview::where('user_id',$id)->delete();
        ShippingAddress::where('user_id',$id)->delete();
        BillingAddress::where('user_id',$id)->delete();
        Wishlist::where('user_id',$id)->delete();

        $notification = trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);

    }

    public function changeStatus($id){
        $customer = User::find($id);
        if($customer->status == 1){
            $customer->status = 0;
            $customer->save();
            $message = trans('Inactive Successfully');
        }else{
            $customer->status = 1;
            $customer->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }

    public function sendEmailToAllUser(){
        return view('admin.send_email_to_all_customer');
    }

    public function sendMailToAllUser(Request $request){
        $rules = [
            'subject'=>'required',
            'message'=>'required'
        ];
        $customMessages = [
            'subject.required' => trans('Subject is required'),
            'message.required' => trans('Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $users = User::where('status',1)->get();
        MailHelper::setMailConfig();
        foreach($users as $user){
            Mail::to($user->email)->send(new SendSingleSellerMail($request->subject,$request->message));
        }

        $notification = trans('Email Send Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function sendMailToSingleUser(Request $request, $id){
        $rules = [
            'subject'=>'required',
            'message'=>'required'
        ];
        $customMessages = [
            'subject.required' => trans('Subject is required'),
            'message.required' => trans('Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = User::find($id);
        MailHelper::setMailConfig();
        Mail::to($user->email)->send(new SendSingleSellerMail($request->subject,$request->message));

        $notification = trans('Email Send Successfully');
        return response()->json(['notification' => $notification], 200);
    }

}
