<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Rules\Captcha;
use Auth;
use App\Mail\UserRegistration;
use App\Helpers\MailHelper;
use App\Models\EmailTemplate;
use App\Models\SmsTemplate;
use App\Models\TwilioSms;
use App\Models\Setting;
use App\Models\BiztechSms;
use Mail;
use Str;
use Exception;

use Twilio\Rest\Client;

class RegisterController extends Controller
{

    use RegistersUsers;


    protected $redirectTo = RouteServiceProvider::HOME;


    public function __construct()
    {
        $this->middleware('guest:api');
    }

    public function storeRegister(Request $request){
        // return response()->json($request->email);
        $setting = Setting::first();
        $enable_phone_required = $setting->phone_number_required;

        $rules = [
            'name'=>'required',
            'agree'=>'required',
            'email'=>'required|unique:users',
            'phone'=> $enable_phone_required == 1 ? 'required|unique:users' : '',
            'password'=>'required|min:4|confirmed',
            'g-recaptcha-response'=>new Captcha()
        ];
        $customMessages = [
            'name.required' => trans('user_validation.Name is required'),
            'email.required' => trans('user_validation.Email is required'),
            'email.unique' => trans('user_validation.Email already exist'),
            'phone.required' => trans('user_validation.Phone number is required'),
            'phone.unique' => trans('user_validation.Phone number already exist'),
            'password.required' => trans('user_validation.Password is required'),
            'password.min' => trans('user_validation.Password must be 4 characters'),
            'password.confirmed' => trans('user_validation.Confirm password does not match'),
            'agree.required' => trans('user_validation.Consent filed is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone ? $request->phone : '';
        $user->agree_policy = $request->agree ? 1 : 0;
        $user->password = Hash::make($request->password);
        // $user->verify_token = random_int(100000, 999999);
        $user->verify_token=123456;
        $user->save();
        
        
       

        MailHelper::setMailConfig();

        $template=EmailTemplate::where('id',4)->first();
        $subject=$template->subject;
        $message=$template->description;
        $message = str_replace('{{user_name}}',$request->name,$message);
        // Mail::to($user->email)->send(new UserRegistration($message,$subject,$user));

        if($enable_phone_required == 1){
            $template=SmsTemplate::where('id',1)->first();
            $message=$template->description;
            $message = str_replace('{{user_name}}',$user->name,$message);
            $message = str_replace('{{otp_code}}',$user->verify_token,$message);

            $twilio = TwilioSms::first();
            if($twilio->enable_register_sms == 1){
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


            $biztech = BiztechSms::first();
            if($biztech->enable_register_sms == 1){
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
        }



        $notification = trans('user_validation.Register Successfully. Please Verify your email');
        return response()->json(['notification' => $notification,"user_id" => $user->id]);
    }

    public function resendRegisterCode(Request $request){
        $setting = Setting::first();
        $enable_phone_required = $setting->phone_number_required;

        $rules = [
            'email'=>'required',
            'phone'=> $enable_phone_required == 1 ? 'required' : '',
        ];
        $customMessages = [
            'email.required' => trans('user_validation.Email is required'),
            'phone.required' => trans('user_validation.Phone number is required'),
            'phone.unique' => trans('user_validation.Phone number already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = User::where('email', $request->email)->first();
        if($user){
            if($user->email_verified == 0){
                MailHelper::setMailConfig();

                $template=EmailTemplate::where('id',4)->first();
                $subject=$template->subject;
                $message=$template->description;
                $message = str_replace('{{user_name}}',$user->name,$message);
                Mail::to($user->email)->send(new UserRegistration($message,$subject,$user));

                if($enable_phone_required == 1){
                    $template=SmsTemplate::where('id',1)->first();
                    $message=$template->description;
                    $message = str_replace('{{user_name}}',$user->name,$message);
                    $message = str_replace('{{otp_code}}',$user->verify_token,$message);

                    $twilio = TwilioSms::first();
                    if($twilio->enable_register_sms == 1){
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

                    $biztech = BiztechSms::first();
                    if($biztech->enable_register_sms == 1){
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
                }

                $notification = trans('user_validation.Register Successfully. Please Verify your email');
                return response()->json(['notification' => $notification]);

            }else{
                $notification = trans('user_validation.Already verfied your account');
                return response()->json(['notification' => $notification],402);
            }
        }else{
            $notification = trans('user_validation.Email does not exist');
            return response()->json(['notification' => $notification],402);
        }

    }


    public function userVerification($token,$id){
        $user = User::where('verify_token',$token)->where('id',$id)->first();
        if($user){
            $user->verify_token = null;
            $user->status = 1;
            $user->email_verified = 1;
            $user->save();
            $notification = trans('user_validation.Verification Successfully');
            return response()->json(['notification' => $notification],200);
        }else{
            $notification = trans('user_validation.Invalid token');
            return response()->json(['notification' => $notification],400);
        }
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }


    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
