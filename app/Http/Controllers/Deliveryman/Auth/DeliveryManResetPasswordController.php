<?php

namespace App\Http\Controllers\Deliveryman\Auth;

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
use App\Mail\DeliveryManForgetPasswordApi;
use Illuminate\Support\Facades\Password;
use Str;

class DeliveryManResetPasswordController extends Controller
{
    
    public function passwrodResetEmail(Request $request){
        $rules = [
            'email'=>'required|email'
        ];

        $customMessages = [
            'email.required' => trans('admin_validation.Email is required'),
            'email.email' => trans('Email must valid email address'),
        ];
        $this->validate($request, $rules,$customMessages);

        MailHelper::setMailConfig();
        $deliveryMan=DeliveryMan::where('email',$request->email)->first();
        if($deliveryMan){
            $deliveryMan->forget_password_token = random_int(100000, 999999);
            $deliveryMan->save();

            $template=EmailTemplate::where('id',1)->first();
            $message=$template->description;
            $subject=$template->subject;
            $message=str_replace('{{name}}',$deliveryMan->name,$message);

            Mail::to($deliveryMan->email)->send(new DeliveryManForgetPasswordApi($deliveryMan,$message,$subject));
            
            $notification= trans('Forget password link send your email');
            return response()->json(['notification' => $notification],200);

        }else{
            $notification= trans('admin_validation.email does not exist');
            return response()->json(['notification' => $notification],400);
        }
    }

    public function passwrodUpdate(Request $request){
        $rules = [
            'token'=>'required',
            'password'=>'required|min:4',
            'c_password' =>'required|same:password',
        ];

        $customMessages = [
            'token.required' => trans('Authenticate token is required'),
            'password.required' => trans('Password is required'),
            'password.min' => trans('Password must 4 characters.'),
            'c_password.required' => trans('Confirm password is required'),
            'c_password.same' => trans('Confirm password do not match'),
            
        ];
        $this->validate($request, $rules,$customMessages);

        $deliveryMan=DeliveryMan::where('forget_password_token', $request->token)->first();
        if($deliveryMan){
            $deliveryMan->password=Hash::make($request->password);
            $deliveryMan->forget_password_token=null;
            $deliveryMan->save();
            $notification= trans('Password update successfully');
            return response()->json(['notification' => $notification],200);
        }else{
            $notification= trans('Authenticate token is invalid');
            return response()->json(['notification' => $notification],400);
        }
        
    }

}
