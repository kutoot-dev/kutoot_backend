<?php

namespace App\Http\Controllers\Admin\Auth;

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
            'email.required' => trans('Email is required'),
            'password.required' => trans('Password is required'),
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
                    if (! $token = Auth::guard('admin-api')->attempt($credential)) {
                        return response()->json(['error' => 'Unauthorized'], 401);
                    }
                    return $this->respondWithToken($token, $isAdmin);
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

    public function adminLogout(){
        Auth::guard('admin-api')->logout();
        $notification= trans('Logout Successfully');
        return response()->json(['notification' => $notification],200);
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
