<?php

namespace App\Http\Controllers\Deliveryman\Auth;

use Auth;
use Hash;
use Carbon\Carbon;
use App\Models\Vendor;
use App\Rules\Captcha;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryManLoginController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('guest:api')->except('userLogout');
    // }
   public function loginPage(){
    return view('deliveryman.login');
   }
   public function dashboardLogin(Request $request){
    $rules = [
        'email'=>'required',
        'password'=>'required',
        'g-recaptcha-response'=>new Captcha()
    ];
    $customMessages = [
        'email.required' => trans('user_validation.Email is required'),
        'password.required' => trans('user_validation.Password is required'),
    ];
    $this->validate($request, $rules,$customMessages);

    $login_by = 'email';
    if(filter_var($request->email, FILTER_VALIDATE_EMAIL)){
        $login_by = 'email';
        $deliveryMan = DeliveryMan::where('email',$request->email)->first();

    }else if(is_numeric($request->email)){
        $login_by = 'phone';
        $deliveryMan = DeliveryMan::where('phone',$request->email)->first();
    }else{
        return response()->json(['message' => trans('Please provide valid email')],422);
    }

    if($deliveryMan){
        // if($deliveryMan->email_verified == 0){
        //     $notification = trans('user_validation.Please verify your acount. If you didn\'t get OTP, please resend your OTP and verify');
        //     return response()->json(['notification' => $notification],402);
        // }
        if($deliveryMan->status==1){
            if(Hash::check($request->password,$deliveryMan->password)){

                if($login_by == 'email'){
                    $credential=[
                        'email'=> $request->email,
                        'password'=> $request->password
                    ];
                }else{
                    $credential=[
                        'phone'=> $request->email,
                        'password'=> $request->password
                    ];
                }
                if (! $token = Auth::guard('deliveryman-api')->attempt($credential, ['exp' => Carbon::now()->addDays(365)->timestamp])) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                if($login_by == 'email'){
                    $deliveryMan = DeliveryMan::where('email',$request->email)->select('id','fname','email','phone','man_image','status')->first();
                }else{
                    $deliveryMan = DeliveryMan::where('phone',$request->email)->select('id','fname','email','phone','man_image','status')->first();
                }


                $isVendor = Vendor::where('user_id',$deliveryMan->id)->first();
                if($isVendor) {
                    return $this->respondWithToken($token,1,$deliveryMan);
                }else {
                    return $this->respondWithToken($token,0,$deliveryMan);
                }


            }else{
                $notification = trans('user_validation.Credentials does not exist');
                return response()->json(['notification' => $notification],402);
            }

        }else{
            $notification = trans('user_validation.Disabled Account');
            return response()->json(['notification' => $notification],402);
        }
    }else{
        $notification = trans('user_validation.Email does not exist');
        return response()->json(['notification' => $notification],402);
    }
   }
   public function logout(){
        Auth::guard('deliveryman-api')->logout();
        $notification= trans('admin_validation.Logout Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return response()->json(['message' => $notification],200);
    }

protected function respondWithToken($token, $vendor,$deliveryMan)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'is_vendor' => $vendor,
            'user' => $deliveryMan
        ]);
    }
}
