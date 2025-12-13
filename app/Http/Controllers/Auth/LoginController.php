<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\BannerImage;
use App\Models\BreadcrumbImage;
use App\Models\GoogleRecaptcha;
use App\Models\User;
use App\Models\Vendor;
use App\Rules\Captcha;
use Auth;
use Hash;
use App\Mail\UserForgetPassword;
use App\Helpers\MailHelper;
use App\Helpers\SmsHelper;
use App\Models\EmailTemplate;
use App\Models\SocialLoginInformation;
use App\Models\TwilioSms;
use App\Models\SmsTemplate;
use App\Models\BiztechSms;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Str;
use Redirect,Response,File;
use Socialite;
use Carbon\Carbon;
use Twilio\Rest\Client;
use Exception;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    use AuthenticatesUsers;
    protected $redirectTo = '/user/dashboard';

    public function __construct()
    {
        //$this->middleware('guest:api')->except('userLogout');
         $this->middleware('guest:api')->except([
        'userLogout',
        'firebaseLogin'
    ]);
    }

    public function loginPage(){
        $banner = BreadcrumbImage::where(['id' => 5])->first();
        $background = BannerImage::whereId('13')->first();
        $recaptchaSetting = GoogleRecaptcha::first();
        $socialLogin = SocialLoginInformation::first();
        return view('login', compact('banner','background','recaptchaSetting','socialLogin'));
    }




    public function logintrigger(Request $request)
    {
        $rules = [
            'identifier' => 'required',
        ];

        $customMessages = [
            'identifier.required' => trans('user_validation.Email or phone is required'),
        ];

        $this->validate($request, $rules, $customMessages);

        $identifier = trim($request->input('identifier')); // safely access and trim
        $user = null;


        // Check if it's a valid email
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
            $login_by = 'email';

        // Check if it's a valid phone number (remove non-numeric first)
        } elseif (preg_match('/^\+?[0-9]{7,15}$/', $identifier)) {
            $cleanedPhone = preg_replace('/\D/', '', $identifier); // Remove non-numeric chars
            $user = User::where('phone', $cleanedPhone)->first();
            $login_by = 'phone';

        } else {
            return response()->json([
                'message' => trans('user_validation.Please provide valid email or phone')
            ], 422);
        }

        // 👇 If user does not exist, create new user
        if (!$user) {
            // Extra validation for uniqueness

            if ($login_by == 'email') {
                $user = User::where('email', $identifier)->exists();
                if (!$user) {
                    $user = User::create([
                        'email' => $identifier,
                        'email_verified' => 0,
                        'status' => 1,
                        'login_otp' => null,
                        'name' => 'User_' . rand(1000, 9999), // default name
                    ]);
                }


            } else {
                $user = User::where('phone', $identifier)->exists();
            
                if(!$user){

                    $user = User::create([
                        'phone' => $identifier,
                        'email_verified' => 0,
                        'status' => 1,
                        'login_otp' => null,
                        'name' => 'User_' . rand(1000, 9999), // default name
                    ]); 
                }
                
            }
        }

        // ✅ Proceed with verification

        if ($user->status == 1) {
            
            // Check OTP send count
            // $today = now()->toDateString();
            // $otpCount = OtpLog::where('user_id', $user->id)
            //     ->whereDate('created_at', $today)
            //     ->count();

            // if ($otpCount >= 4) {
            //     return response()->json(['message' => trans('user_validation.You have reached the maximum OTP request limit for today')], 429);
            // }

            // Generate and store OTP
            $otp = rand(1000, 9999);

            // $otp = 1234;
            $user->login_otp = $otp;
            $user->save();

if ($login_by == 'phone') {
    $smsResponse = SmsHelper::sendOtp($user->phone, $otp);

    if (!$smsResponse['success']) {
        return response()->json([
            'message' => 'Failed to send SMS OTP',
            'details' => $smsResponse['raw']
        ], 500);
    }
}else {
        // ✅ Call your email OTP sender
        $emailResponse = $this->sendOtpEmail($user->email, $otp);

        if (!$emailResponse['success']) {
            return response()->json([
                'message' => 'Failed to send Email OTP',
                'details' => $emailResponse['error']
            ], 500);
        }
    }

            // Log the OTP send
            // \App\Models\OtpLog::create([
            //     'user_id' => $user->id,
            //     'sent_at' => now()
            // ]);

            return response()->json([
                'message' => trans('OTP sent successfully'),
                // 'otp' => $otp // Remove this in production
            ], 200);

        }

        // If already verified, just say login allowed or proceed further
        return response()->json(['message' => trans('User account not verified. Please contact admin.')], 200);
    }


    public function verifyOtp(Request $request)
    {
        $identifier = trim($request->input('identifier'));

    if ($identifier === 'sociallogin') {
        // Forward request to social login
        return $this->sociallogin($request);
        exit;
    }
        // dd($request->all());
        // $rules = [
        //     'identifier' => 'required',
        //     'otp' => 'required|digits:4',
        // ];

        // $customMessages = [
        //     'identifier.required' => trans('user_validation.Email or phone is required'),
        //     'otp.required' => trans('user_validation.OTP is required'),
        //     'otp.digits' => trans('user_validation.OTP must be 4 digits'),
        // ];

        // $this->validate($request, $rules, $customMessages);
 $validator = Validator::make($request->all(), [
        'identifier' => 'required',
        'otp'        => 'required|digits:4',
    ], [
        'identifier.required' => 'Email or phone is required',
        'otp.required'        => 'OTP is required',
        'otp.digits'          => 'OTP must be exactly 4 digits',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }


        $identifier = trim($request->input('identifier'));
        $otp = $request->otp;

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
            $login_by = 'email';

        // Check if it's a valid phone number (remove non-numeric first)
        } elseif (preg_match('/^\+?[0-9]{7,15}$/', $identifier)) {
            $cleanedPhone = preg_replace('/\D/', '', $identifier); // Remove non-numeric chars
            $user = User::where('phone', $cleanedPhone)->first();
            $login_by = 'phone';

        } else {
            return response()->json([
                'message' => trans('user_validation.Please provide valid email or phone')
            ], 422);
        }

        // if (!$user) {
        //     return response()->json(['message' => trans('user_validation.User not found')], 404);
        // }

        // if ($user->login_otp != $otp) {
        //     return response()->json(['message' => trans('user_validation.Invalid OTP')], 401);
        // }
if (!$user) {
    return response()->json(['message' => trans('user_validation.User not found')], 404);
}

if ((string) trim($user->login_otp) !== (string) trim($otp)) {
    return response()->json(['message' => trans('user_validation.Invalid OTP')], 401);
}

        // OTP is correct – mark as verified
        $user->email_verified = 1;
        $user->login_otp = null;
        $user->save();

        $token = Auth::guard('api')->login($user);

        $isVendor = Vendor::where('user_id',$user->id)->first();
        if($isVendor) {
            return $this->respondWithToken($token,1,$user);
        }else {
            return $this->respondWithToken($token,0,$user);
        }

        // You could return a token here or success response
        return response()->json(['message' => trans('Failed to Login')], 400);
        
    }


public function sendOtpEmail($email, $otp)
{
    try {
        Mail::to($email)->send(new OtpMail($otp));

        if (count(Mail::failures()) > 0) {
            return ['success' => false, 'error' => 'Failed to send email'];
        }

        return ['success' => true];
    } catch (\Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}



    public function storeLogin(Request $request){
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
            $user = User::where('email',$request->email)->first();

        }else if(is_numeric($request->email)){
            $login_by = 'phone';
            $user = User::where('phone',$request->email)->first();
        }else{
            return response()->json(['message' => trans('user_validation.Please provide valid email or phone')],422);
        }

        if($user){
            if($user->email_verified == 0){
                $notification = trans('user_validation.Please verify your acount. If you didn\'t get OTP, please resend your OTP and verify');
                return response()->json(['notification' => $notification],402);
            }
            if($user->status==1){
                if(Hash::check($request->password,$user->password)){

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
                    if (! $token = Auth::guard('api')->attempt($credential, ['exp' => Carbon::now()->addDays(365)->timestamp])) {
                        return response()->json(['error' => 'Unauthorized'], 401);
                    }

                    if($login_by == 'email'){
                        $user = User::where('email',$request->email)->select('id','name','email','phone','image','status')->first();
                    }else{
                        $user = User::where('phone',$request->email)->select('id','name','email','phone','image','status')->first();
                    }


                    $isVendor = Vendor::where('user_id',$user->id)->first();
                    if($isVendor) {
                        return $this->respondWithToken($token,1,$user);
                    }else {
                        return $this->respondWithToken($token,0,$user);
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


    protected function respondWithToken($token, $vendor,$user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'is_vendor' => $vendor,
            'user' => $user
        ]);
    }


    public function forgetPage(){
        $banner = BreadcrumbImage::where(['id' => 5])->first();
        $recaptchaSetting = GoogleRecaptcha::first();
        return view('forget_password', compact('banner','recaptchaSetting'));
    }

    public function sendForgetPassword(Request $request){
        $rules = [
            'email'=>'required',
            'g-recaptcha-response'=>new Captcha()
        ];
        $customMessages = [
            'email.required' => trans('user_validation.Email is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = User::where('email', $request->email)->first();
        if($user){
            $user->forget_password_token = random_int(100000, 999999);
            $user->save();

            MailHelper::setMailConfig();
            $template = EmailTemplate::where('id',1)->first();
            $subject = $template->subject;
            $message = $template->description;
            $message = str_replace('{{name}}',$user->name,$message);
            Mail::to($user->email)->send(new UserForgetPassword($message,$subject,$user));

            $template=SmsTemplate::where('id',2)->first();
            $message=$template->description;
            $message = str_replace('{{name}}',$user->name,$message);
            $message = str_replace('{{otp_code}}', $user->forget_password_token ,$message);

            $twilio = TwilioSms::first();
            if($twilio->enable_reset_pass_sms == 1){
                if($user->phone){
                    try{
                        $account_sid = $twilio->account_sid;
                        $auth_token = $twilio->auth_token;
                        $twilio_number = $twilio->twilio_phone_number;
                        $recipients = $user->phone;
                        $client = new Client($account_sid, $auth_token);
                        $client->messages->create($recipients,
                                ['from' => $twilio_number, 'body' => $message] );
                    }catch(Exception $ex){

                    }
                }
            }

            $biztech = BiztechSms::first();
            if($biztech->enable_reset_pass_sms == 1){
                if($user->phone){
                    try{
                        $apikey = $biztech->api_key;
                        $clientid = $biztech->client_id;
                        $senderid = $biztech->sender_id;
                        $senderid = urlencode($senderid);
                        $message = $message;
                        $msg_type = true;  // true or false for unicode message
                        $message  = urlencode($message);
                        $mobilenumbers = $user->phone; //8801700000000 or 8801700000000,9100000000
                        $url = "https://api.smsq.global/api/v2/SendSMS?ApiKey=$apikey&ClientId=$clientid&SenderId=$senderid&Message=$message&MobileNumbers=$mobilenumbers&Is_Unicode=$msg_type";
                        $ch = curl_init();
                        curl_setopt ($ch, CURLOPT_URL, $url);
                        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
                        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_NOBODY, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $response = curl_exec($ch);
                        $response = json_decode($response);
                    }catch(Exception $ex){}
                }
            }

            $notification = trans('user_validation.Reset password link send to your email.');
            return response()->json(['notification' => $notification],200);

        }else{
            $notification = trans('user_validation.Email does not exist');
            return response()->json(['notification' => $notification],402);
        }
    }


    public function resetPasswordPage($token){
        $user = User::where('forget_password_token', $token)->first();
        $banner = BreadcrumbImage::where(['id' => 5])->first();
        $recaptchaSetting = GoogleRecaptcha::first();

        return response()->json(['user' => $user, 'banner' => $banner, 'recaptchaSetting' => $recaptchaSetting],200);

        return view('reset_password', compact('banner','recaptchaSetting','user','token'));
    }

    public function storeResetPasswordPage(Request $request, $token){
        $rules = [
            'email'=>'required',
            'password'=>'required|min:4|confirmed',
            'g-recaptcha-response'=>new Captcha()
        ];
        $customMessages = [
            'email.required' => trans('user_validation.Email is required'),
            'password.required' => trans('user_validation.Password is required'),
            'password.min' => trans('user_validation.Password must be 4 characters'),
            'password.confirmed' => trans('user_validation.Confirm password does not match'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = User::where(['email' => $request->email, 'forget_password_token' => $token])->first();
        if($user){
            $user->password=Hash::make($request->password);
            $user->forget_password_token=null;
            $user->save();

            $notification = trans('user_validation.Password Reset successfully');
            return response()->json(['notification' => $notification],200);
        }else{
            $notification = trans('user_validation.Email or token does not exist');
            return response()->json(['notification' => $notification],402);
        }
    }

    public function userLogout(){
        Auth::guard('api')->logout();
        $notification= trans('user_validation.Logout Successfully');
        return response()->json(['notification' => $notification],200);
    }

    // public function redirectToGoogle(){

    //     // SocialLoginInformation::setGoogleLoginInfo();

    //     $googleInfo = SocialLoginInformation::first();
    //    \Config::set('services.google.client_id', $googleInfo->gmail_client_id);
    //         \Config::set('services.google.client_secret', $googleInfo->gmail_secret_id);
    //         \Config::set('services.google.redirect', $googleInfo->gmail_redirect_url);

    //     return response()->json([
    //         'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
    //     ]);


    //     SocialLoginInformation::setGoogleLoginInfo();
    //     return Socialite::driver('google')->redirect();
    // }
public function redirectToGoogle()
{
    $googleInfo = SocialLoginInformation::first();

    \Config::set('services.google.client_id', $googleInfo->gmail_client_id);
    \Config::set('services.google.client_secret', $googleInfo->gmail_secret_id);
    \Config::set('services.google.redirect', $googleInfo->gmail_redirect_url);

    return Socialite::driver('google')->stateless()->redirect();
}
    public function googleCallBack(Request $request){

        $googleInfo = SocialLoginInformation::first();
       \Config::set('services.google.client_id', $googleInfo->gmail_client_id);
            \Config::set('services.google.client_secret', $googleInfo->gmail_secret_id);
            \Config::set('services.google.redirect', $googleInfo->gmail_redirect_url);




        try {
            /** @var SocialiteUser $socialiteUser */
            $socialiteUser = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }


        $user = User::where('email', $socialiteUser->getEmail())->first();
        if (!$user) {
            $user = User::create([
                'name'     => $socialiteUser->getName(),
                'email'    => $socialiteUser->getEmail(),
                'provider' => 'google',
                'provider_id' => $socialiteUser->getId(),
                'provider_avatar' => $socialiteUser->getAvatar(),
                'status' => 1,
                'email_verified' => 1,
            ]);
        }


        $token = Auth::guard('api')->login($user);


        $isVendor = Vendor::where('user_id',$user->id)->first();
        if($isVendor) {
            return $this->respondWithToken($token,1,$user);
        }else {
            return $this->respondWithToken($token,0,$user);
        }



    }
public function redirectToFacebook()
{
    $facebookInfo = SocialLoginInformation::first();

    \Config::set('services.facebook.client_id', $facebookInfo->facebook_client_id);
    \Config::set('services.facebook.client_secret', $facebookInfo->facebook_secret_id);
    \Config::set('services.facebook.redirect', $facebookInfo->facebook_redirect_url);

    return Socialite::driver('facebook')->stateless()->redirect();
}
    // public function redirectToFacebook(){

    //     $facebookInfo = SocialLoginInformation::first();
    //     if($facebookInfo){
    //         \Config::set('services.facebook.client_id', $facebookInfo->facebook_client_id);
    //         \Config::set('services.facebook.client_secret', $facebookInfo->facebook_secret_id);
    //         \Config::set('services.facebook.redirect', $facebookInfo->facebook_redirect_url);
    //     }

    //     return response()->json([
    //         'url' => Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl(),
    //     ]);

    //     SocialLoginInformation::setFacebookLoginInfo();
    //     return Socialite::driver('facebook')->redirect();
    // }

    public function facebookCallBack(){

        $facebookInfo = SocialLoginInformation::first();
        if($facebookInfo){
            \Config::set('services.facebook.client_id', $facebookInfo->facebook_client_id);
            \Config::set('services.facebook.client_secret', $facebookInfo->facebook_secret_id);
            \Config::set('services.facebook.redirect', $facebookInfo->facebook_redirect_url);
        }


         try{    /** @var SocialiteUser $socialiteUser */
            $socialiteUser = Socialite::driver('facebook')->stateless()->user();
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }


        $user = User::where('email', $socialiteUser->getEmail())->first();
        if (!$user) {
            $user = User::create([
                'name'     => $socialiteUser->getName(),
                'email'    => $socialiteUser->getEmail(),
                'provider' => 'facebook',
                'provider_id' => $socialiteUser->getId(),
                'provider_avatar' => $socialiteUser->getAvatar(),
                'status' => 1,
                'email_verified' => 1,
            ]);
        }
            Auth::login($user);
            return redirect('/user/dashboard');

        // $token = Auth::guard('api')->login($user);


        // $isVendor = Vendor::where('user_id',$user->id)->first();
        // if($isVendor) {
        //     return $this->respondWithToken($token,1,$user);
        // }else {
        //     return $this->respondWithToken($token,0,$user);
        // }


    }



    function createUser($getInfo,$provider){
        $user = User::where('provider_id', $getInfo->id)->first();
        if (!$user) {
            $user = User::create([
                'name'     => $getInfo->name,
                'email'    => $getInfo->email,
                'provider' => $provider,
                'provider_id' => $getInfo->id,
                'provider_avatar' => $getInfo->avatar,
                'status' => 1,
                'email_verified' => 1,
            ]);
        }
        return $user;
    }
    
    // for google login
    public function sociallogin(Request $request)
{
    $request->validate([
        'token' => 'required'
    ]);

    try {
        $client = new \Google_Client([
            'client_id' => '117229478868-sfsv85bpqb85f9ie2gg7tdtnrb58hkuk.apps.googleusercontent.com'
        ]);

        $payload = $client->verifyIdToken($request->token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $user = User::firstOrCreate(
            ['email' => $payload['email']],
            [
                'name'            => $payload['name'] ?? 'User',
                'email_verified'  => 1,
                'status'          => 1,
                'provider'        => 'google',
                'provider_id'     => $payload['sub'],
                'provider_avatar' => $payload['picture'] ?? null,
            ]
        );

        $jwt = Auth::guard('api')->login($user);

        return response()->json([
            'access_token' => $jwt,
            'token_type'   => 'bearer',
            'user'         => $user
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Token verification failed',
            'message' => $e->getMessage()
        ], 500);
    }
}

}
