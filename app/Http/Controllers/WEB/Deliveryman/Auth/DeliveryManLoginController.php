<?php

namespace App\Http\Controllers\WEB\Deliveryman\Auth;

use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Hash;

class DeliveryManLoginController extends Controller
{
   public function loginPage(){
    return view('deliveryman.login');
   }
   public function dashboardLogin(Request $request){
    $rules = [
        'email'=>'required|email',
        'password'=>'required',
    ];

    $customMessages = [
        'email.required' => trans('admin_validation.Email is required'),
        'password.required' => trans('admin_validation.Password is required'),
    ];
    $this->validate($request, $rules,$customMessages);

    $credential=[
        'email'=> $request->email,
        'password'=> $request->password
    ];

    $isAdmin=DeliveryMan::where('email',$request->email)->first();
        if($isAdmin){
            if($isAdmin->status==1){
                if(Hash::check($request->password,$isAdmin->password)){
                    if (Auth::guard('deliveryman')->attempt($credential,$request->remember)) {
                        $notification= trans('admin_validation.Login Successfully');
                        return response()->json(['success'=>$notification]);
                   }
                }else{
                    $notification= trans('Invalid Password');
                    return response()->json(['error'=>$notification]);
                }
            }else{
                $notification= trans('Inactive account');
                return response()->json(['error'=>$notification]);
            }
        }else{
            $notification= trans('Invalid Email');
            return response()->json(['error'=>$notification]);
        }
   }
   public function logout(){
    Auth::guard('deliveryman')->logout();
    $notification= trans('admin_validation.Logout Successfully');
    $notification=array('messege'=>$notification,'alert-type'=>'success');
    return redirect()->route('delivery.man.login')->with($notification);
}
}
