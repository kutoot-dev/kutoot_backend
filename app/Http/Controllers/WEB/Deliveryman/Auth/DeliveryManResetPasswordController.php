<?php

namespace App\Http\Controllers\WEB\Deliveryman\Auth;

use App\Models\Admin;
use App\Helpers\MailHelper;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Mail\AdminForgetPassword;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeliveryManForgetPassword;
use Illuminate\Support\Facades\Password;
use Str;

class DeliveryManResetPasswordController extends Controller
{
    public function passwordReset(){
        return view('deliveryman.email');
    }
    public function passwrodResetEmail(Request $request){
        $rules = [
            'email'=>'required'
        ];

        $customMessages = [
            'email.required' => trans('admin_validation.Email is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        MailHelper::setMailConfig();
        $deliveryMan=DeliveryMan::where('email',$request->email)->first();
        if($deliveryMan){
            $deliveryMan->forget_password_token = random_int(100000, 999999).Str::random(120);
            $deliveryMan->save();
            // $template=EmailTemplate::where('id',1)->first();
            // $message=$template->description;
            // $subject=$template->subject;
            // $message=str_replace('{{name}}',$deliveryMan->name,$message);
            $url=URL::to('/deliveryman/password/reset',$deliveryMan->forget_password_token);

            Mail::to($deliveryMan->email)->send(new DeliveryManForgetPassword($deliveryMan,$url));

            // $notification= trans('admin_validation.Forget password link send your email');
            // return response()->json(['notification' => $notification],200);
            $notification = trans('Forget password link send your email');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);

        }else{
            // $notification= trans('admin_validation.email does not exist');
            // return response()->json(['notification' => $notification],400);
            $notification = trans('Email does not exist');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }
    }

    public function passwordResetPage($token){
        $deliveryMan=DeliveryMan::where('forget_password_token', $token)->first();
        if($deliveryMan){
            $token=$token;
            return view('deliveryman.reset', compact('token'));
        }else{
            $notification = trans('Password reset link is invalid');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('delivery.man.login')->with($notification);
        }
    }

    public function passwrodUpdate(Request $request){
        $rules = [
            'password'=>'required|min:4',
            'c_password' =>'required|same:password',
        ];

        $customMessages = [
            'password.required' => trans('Password is required'),
            'password.min' => trans('Password must 4 characters.'),
            'c_password.required' => trans('Confirm password is required'),
            'c_password.same' => trans('Confirm password do not match'),
            
        ];
        $this->validate($request, $rules,$customMessages);

        $deliveryMan=DeliveryMan::where('forget_password_token', $request->token)->first();
        $deliveryMan->password=Hash::make($request->password);
        $deliveryMan->forget_password_token=null;
        $deliveryMan->save();
        $notification = trans('Password Updated successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('delivery.man.login')->with($notification);
        
    }

}
