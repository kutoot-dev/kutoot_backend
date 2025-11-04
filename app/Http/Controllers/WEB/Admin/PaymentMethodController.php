<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaypalPayment;
use App\Models\StripePayment;
use App\Models\RazorpayPayment;
use App\Models\Flutterwave;
use App\Models\BankPayment;
use App\Models\PaystackAndMollie;
use App\Models\InstamojoPayment;
use App\Models\CurrencyCountry;
use App\Models\Currency;
use App\Models\MultiCurrency;
use App\Models\Setting;
use App\Models\PaymongoPayment;
use App\Models\SslcommerzPayment;
use App\Models\MyfatoorahPayment;
use Image;
use File;
class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $paypal = PaypalPayment::first();
        $stripe = StripePayment::first();
        $razorpay = RazorpayPayment::first();
        $flutterwave = Flutterwave::first();
        $bank = BankPayment::first();
        $paystackAndMollie = PaystackAndMollie::first();
        $instamojo = InstamojoPayment::first();
        $paymongo = PaymongoPayment::first();
        $sslcommerz = SslcommerzPayment::first();
        $myfatoorah = MyfatoorahPayment::first();

        $countires = CurrencyCountry::orderBy('name','asc')->get();
        $setting = Setting::first();

        $currencies = MultiCurrency::where('status',1)->orderBy('currency_name','asc')->get();

        return view('admin.payment_method', compact('paypal','stripe','razorpay','bank','paystackAndMollie','flutterwave','instamojo','countires','currencies','setting','paymongo','sslcommerz','myfatoorah'));

    }

    public function updatePaypal(Request $request){

        $rules = [
            'paypal_client_id' => 'required',
            'paypal_secret_key' => 'required',
            'account_mode' => 'required',
            'currency_name' => 'required',
        ];
        $customMessages = [
            'paypal_client_id.required' => trans('admin_validation.Paypal client id is required'),
            'paypal_secret_key.required' => trans('admin_validation.Paypal secret key is required'),
            'account_mode.required' => trans('admin_validation.Account mode is required'),
            'currency_name.required' => trans('admin_validation.Currency name is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $paypal = PaypalPayment::first();
        $paypal->client_id = $request->paypal_client_id;
        $paypal->secret_id = $request->paypal_secret_key;
        $paypal->account_mode = $request->account_mode;
        $paypal->currency_id = $request->currency_name;
        $paypal->status = $request->status ? 1 : 0;
        $paypal->save();

        $notification=trans('admin_validation.Updated Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateStripe(Request $request){

        $rules = [
            'stripe_key' => 'required',
            'stripe_secret' => 'required',
            'currency_name' => 'required',
        ];
        $customMessages = [
            'stripe_key.required' => trans('admin_validation.Stripe key is required'),
            'stripe_secret.required' => trans('admin_validation.Stripe secret is required'),
            'currency_name.required' => trans('admin_validation.Currency name is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $stripe = StripePayment::first();
        $stripe->stripe_key = $request->stripe_key;
        $stripe->stripe_secret = $request->stripe_secret;
        $stripe->currency_id = $request->currency_name;
        $stripe->status = $request->status ? 1 : 0;
        $stripe->save();

        $notification=trans('admin_validation.Updated Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateRazorpay(Request $request){
        $rules = [
            'razorpay_key' => 'required',
            'razorpay_secret' => 'required',
            'name' => 'required',
            'description' => 'required',
            'theme_color' => 'required',
            'currency_name' => 'required',
        ];
        $customMessages = [
            'razorpay_key.required' => trans('admin_validation.Razorpay key is required'),
            'razorpay_secret.required' => trans('admin_validation.Razorpay secret is required'),
            'name.required' => trans('admin_validation.Name is required'),
            'description.required' => trans('admin_validation.Description is required'),
            'currency_name.required' => trans('admin_validation.Currency name is required'),
            'theme_color.required' => trans('admin_validation.Theme Color is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $razorpay = RazorpayPayment::first();
        $razorpay->key = $request->razorpay_key;
        $razorpay->secret_key = $request->razorpay_secret;
        $razorpay->name = $request->name;
        $razorpay->description = $request->description;
        $razorpay->color = $request->theme_color;
        $razorpay->currency_id = $request->currency_name;
        $razorpay->status = $request->status ? 1 : 0;
        $razorpay->save();

        if($request->image){
            $old_image=$razorpay->image;
            $image=$request->image;
            $extention=$image->getClientOriginalExtension();
            $image_name= 'razorpay-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name='uploads/website-images/'.$image_name;
            Image::make($image)
                ->save(public_path().'/'.$image_name);
            $razorpay->image=$image_name;
            $razorpay->save();
            if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
        }

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateBank(Request $request){
        $rules = [
            'account_info' => 'required'
        ];
        $customMessages = [
            'account_info.required' => trans('admin_validation.Account information is required'),
        ];
        $this->validate($request, $rules,$customMessages);
        $bank = BankPayment::first();
        $bank->account_info = $request->account_info;
        $bank->status = $request->status ? 1 : 0;
        $bank->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }

    public function updateMollie(Request $request){
        $rules = [
            'mollie_key' => 'required',
            'mollie_currency_name' => 'required'
        ];

        $customMessages = [
            'mollie_key.required' => trans('admin_validation.Mollie key is required'),
            'mollie_currency_name.required' => trans('admin_validation.Currency name is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $mollie = PaystackAndMollie::first();
        $mollie->mollie_key = $request->mollie_key;
        $mollie->currency_id = $request->mollie_currency_name;
        $mollie->mollie_status = $request->status ? 1 : 0;
        $mollie->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updatePayStack(Request $request){
        $rules = [
            'paystack_public_key' => 'required',
            'paystack_secret_key' => 'required',
            'paystack_currency_name' => 'required',
        ];

        $customMessages = [
            'paystack_public_key.required' => trans('admin_validation.Paystack public key is required'),
            'paystack_secret_key.required' => trans('admin_validation.Paystack secret key is required'),
            'paystack_currency_name.required' => trans('admin_validation.Currency name is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $paystact = PaystackAndMollie::first();
        $paystact->paystack_public_key = $request->paystack_public_key;
        $paystact->paystack_secret_key = $request->paystack_secret_key;
        $paystact->currency_id = $request->paystack_currency_name;
        $paystact->paystack_status = $request->status ? 1 : 0;
        $paystact->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateflutterwave(Request $request){
        $rules = [
            'public_key' => 'required',
            'secret_key' => 'required',
            'title' => 'required',
            'currency_name' => 'required',
        ];
        $customMessages = [
            'title.required' => trans('admin_validation.Title is required'),
            'public_key.required' => trans('admin_validation.Public key is required'),
            'secret_key.required' => trans('admin_validation.Secret key is required'),
            'currency_name.required' => trans('admin_validation.Currency name is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $flutterwave = Flutterwave::first();
        $flutterwave->public_key = $request->public_key;
        $flutterwave->secret_key = $request->secret_key;
        $flutterwave->title = $request->title;
        $flutterwave->currency_id = $request->currency_name;
        $flutterwave->status = $request->status ? 1 : 0;
        $flutterwave->save();

        if($request->image){
            $old_image=$flutterwave->logo;
            $image=$request->image;
            $extention=$image->getClientOriginalExtension();
            $image_name= 'flutterwave-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name='uploads/website-images/'.$image_name;
            Image::make($image)
                ->save(public_path().'/'.$image_name);
            $flutterwave->logo=$image_name;
            $flutterwave->save();
            if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
        }

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function updateInstamojo(Request $request){
        $rules = [
            'account_mode' => 'required',
            'api_key' => 'required',
            'auth_token' => 'required',
            'currency_name' => 'required',
        ];
        $customMessages = [
            'account_mode.required' => trans('admin_validation.Account mode is required'),
            'api_key.required' => trans('admin_validation.Api key is required'),
            'auth_token.required' => trans('admin_validation.Auth token is required'),
            'currency_name.required' => trans('admin_validation.Currency name is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $instamojo = InstamojoPayment::first();
        $instamojo->account_mode = $request->account_mode;
        $instamojo->api_key = $request->api_key;
        $instamojo->auth_token = $request->auth_token;
        $instamojo->currency_id = $request->currency_name;
        $instamojo->status = $request->status ? 1 : 0;
        $instamojo->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateCashOnDelivery(Request $request){
        $bank = BankPayment::first();
        if($bank->cash_on_delivery_status==1){
            $bank->cash_on_delivery_status=0;
            $bank->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $bank->cash_on_delivery_status=1;
            $bank->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function updateSslcommerz(Request $request){
        $rules = [
            'store_id' => $request->status ? 'required' : '',
            'store_password' => $request->status ? 'required' : '',
            'currency_name' => $request->status ? 'required' : '',
        ];
        $customMessages = [
            'store_id.required' => trans('admin_validation.Store id is required'),
            'store_password.required' => trans('admin_validation.Store password is required'),
            'currency_name.required' => trans('admin_validation.Currency name is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $sslcommerz = SslcommerzPayment::first();
        $sslcommerz->mode = $request->account_mode;
        $sslcommerz->store_id = $request->store_id;
        $sslcommerz->store_password = $request->store_password;
        $sslcommerz->currency_id = $request->currency_name;
        $sslcommerz->status = $request->status ? 1 : 0;
        $sslcommerz->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function update_myfatoorah(Request $request){
        $myfatoorah = MyfatoorahPayment::first();
        $myfatoorah->status = $request->status ? 1 : 0;
        $myfatoorah->account_mode = $request->account_mode;
        $myfatoorah->currency_id = $request->currency_name;
        $myfatoorah->api_key = $request->api_key;
        $myfatoorah->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


}
