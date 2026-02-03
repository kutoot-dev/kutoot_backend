<?php

namespace App\Http\Controllers\WEB\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\User;
use App\Models\Setting;
use App\Models\Vendor;
/**
 * @group Seller Login
 */
class SellerLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest:web')->except('adminLogout');
    }

    public function sellerLoginPage(){
        $setting = Setting::first();
        return view('seller.login',compact('setting'));
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

        $isAdmin = User::where('email',$request->email)->first();
        if($isAdmin){
            if($isAdmin->status==1){
                $vendor = Vendor::where('user_id', $isAdmin->id)->first();
                if(!$vendor){
                    $notification= trans('admin_validation.Invalid Email');
                    return response()->json(['error'=>$notification]);
                }
                if($vendor->status == 1){
                    if(Hash::check($request->password,$isAdmin->password)){
                        if(Auth::guard('web')->attempt($credential,$request->remember)){
                            $notification= trans('admin_validation.Login Successfully');
                            return response()->json(['success'=>$notification]);
                        }
                    }else{
                        $notification= trans('admin_validation.Invalid Password');
                        return response()->json(['error'=>$notification]);
                    }
                }else{
                    $notification= trans('admin_validation.Inactive account');
                    return response()->json(['error'=>$notification]);
                }

            }else{
                $notification= trans('admin_validation.Inactive account');
                return response()->json(['error'=>$notification]);
            }
        }else{
            $notification= trans('admin_validation.Invalid Email');
            return response()->json(['error'=>$notification]);
        }
    }


    public function adminLogout(){
        Auth::guard('web')->logout();
        $notification= trans('admin_validation.Logout Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('seller.login')->with($notification);
    }


    protected function respondWithToken($token, $admin)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'admin' => $admin
        ]);
    }
}
