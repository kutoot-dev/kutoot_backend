<?php

namespace App\Http\Controllers\WEB\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\Admin;
use App\Models\Setting;
class AdminLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::ADMIN;

    public function __construct()
    {
        $this->middleware('guest:admin-api')->except('adminLogout');
    }

    public function adminLoginPage(){

       
        $setting = Setting::first();
        return view('admin.auth.login',compact('setting'));
    }


    public function storeLogin(Request $request){

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

        $isAdmin=Admin::where('email',$request->email)->first();
        if($isAdmin){
            if($isAdmin->status==1){
                if(Hash::check($request->password,$isAdmin->password)){
                    if(Auth::guard('admin')->attempt($credential,$request->remember)){
                        $notification= trans('admin_validation.Login Successfully');
                        $notification=array('messege'=>$notification,'alert-type'=>'success');
                        return redirect()->route('admin.dashboard')->with($notification);
                    }
                }else{
                    $notification= trans('admin_validation.Invalid Password');

                    $notification=array('messege'=>$notification,'alert-type'=>'error');
                    return redirect()->route('admin.login')->with($notification);
                }
            }else{
                $notification= trans('admin_validation.Inactive account');
                $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('admin.login')->with($notification);
            }
        }else{
            $notification= trans('admin_validation.Invalid Email');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('admin.login')->with($notification);
        }
    }

    public function adminLogout(){
        Auth::guard('admin')->logout();
        $notification= trans('admin_validation.Logout Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.login')->with($notification);
    }
}
