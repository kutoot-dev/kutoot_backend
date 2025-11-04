<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BreadcrumbImage;
use Auth;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\City;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductVariant;
use App\Models\OrderAddress;
use App\Models\Product;
use App\Models\StripePayment;
use App\Mail\OrderSuccessfully;
use App\Helpers\MailHelper;
use App\Models\EmailTemplate;
use App\Models\RazorpayPayment;
use App\Models\Flutterwave;
use App\Models\PaystackAndMollie;
use App\Models\InstamojoPayment;
use App\Models\Coupon;
use App\Models\ShoppingCart;
use App\Models\ProductVariantItem;
use App\Models\FlashSaleProduct;
use App\Models\FlashSale;
use App\Models\Shipping;
use App\Models\Address;
use App\Models\SslcommerzPayment;
use App\Models\ShoppingCartVariant;
use App\Models\MultiCurrency;
use Mail;
Use Stripe;
use Cart;
use Session;
use Str;
use Razorpay\Api\Api;
use Exception;
use Redirect;

use App\Library\SslCommerz\SslCommerzNotification;
use Mollie\Laravel\Facades\Mollie;

use Twilio\Rest\Client;
use App\Models\SmsTemplate;
use App\Models\TwilioSms;
use App\Models\BiztechSms;
use App\Models\MyfatoorahPayment;

use MyFatoorah\Library\PaymentMyfatoorahApiV2;
use App\Models\Setting;
use App\Models\UserCoins;


class PaymentController extends Controller
{
    public $mfObj;

    public function __construct()
    {
        $this->middleware('auth:api')->except('molliePaymentSuccess','instamojoResponse','sslcommerz_success','sslcommerz_failed','myfatoorah_webview_callback');
    }


    public function cashOnDelivery(Request $request){
       $currency= MultiCurrency::where('is_default','Yes')->first();
        $rules = [
            'shipping_address_id'=>'required',
            'billing_address_id'=>'required',
            'shipping_method_id'=>'required',
        ];
        $customMessages = [
            'shipping_address_id.required' => trans('user_validation.Shipping address is required'),
            'billing_address_id.required' => trans('user_validation.Billing address is required'),
            'shipping_method_id.required' => trans('user_validation.Shipping method is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = Auth::guard('api')->user();

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

        if ($total instanceof \Illuminate\Http\JsonResponse) {
            return $total; // Return the JSON response directly
        }

        $total_price = $total['total_price'];
        $coupon_price = $total['coupon_price'];
        $shipping_fee = $total['shipping_fee'];
        $productWeight = $total['productWeight'];
        $shipping = $total['shipping'];

        $reedem_coins = $request->reedem_coins?? 0;
        $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

        $transaction_id = $request->razorpay_payment_id;
        $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Cash on Delivery', 'cash_on_delivery', 0, $shipping, $shipping_fee, $coupon_price, 1, $request->billing_address_id, $request->shipping_address_id,$reedem_coins);

        // $this->sendOrderSuccessMail($user, $total_price, 'Cash on Delivery', 0, $order_result['order'], $order_result['order_details']);

        // $this->sendOrderSuccessSms($user, $order_result['order']);

        $notification = trans('user_validation.Order submited successfully. please wait for admin approval');

        $order = $order_result['order'];
        $order_id = $order->order_id;

        return response()->json(['message' => $notification, 'order_id' => $order_id],200);

    }

    public function payWithStripe(Request $request){

        $rules = [
            'shipping_address_id'=>'required',
            'billing_address_id'=>'required',
            'shipping_method_id'=>'required',
            'card_number'=>'required',
            'year'=>'required',
            'month'=>'required',
            'cvv'=>'required',
        ];
        $customMessages = [
            'shipping_address_id.required' => trans('user_validation.Shipping address is required'),
            'billing_address_id.required' => trans('user_validation.Billing address is required'),
            'shipping_method_id.required' => trans('user_validation.Shipping method is required'),
            'card_number.required' => trans('user_validation.Card number is required'),
            'year.required' => trans('user_validation.Year is required'),
            'month.required' => trans('user_validation.Month is required'),
            'cvv.required' => trans('user_validation.Cvv is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = Auth::guard('api')->user();
        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

        $total_price = $total['total_price'];
        $coupon_price = $total['coupon_price'];
        $shipping_fee = $total['shipping_fee'];
        $productWeight = $total['productWeight'];
        $shipping = $total['shipping'];

        $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

        $setting = Setting::first();

        $amount_real_currency = $total_price;
        $amount_usd = round($total_price / $setting->currency->currency_rate,2);

        $currency_rate = $setting->currency->currency_rate;
        $currency_icon = $setting->currency->currency_icon;
        $currency_name = $setting->currency->currency_name;


        $stripe = StripePayment::first();
        $payableAmount = round($total_price * $stripe->currency->currency_rate,2);
        Stripe\Stripe::setApiKey($stripe->stripe_secret);

        try{
            $token = Stripe\Token::create([
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->month,
                    'exp_year' => $request->year,
                    'cvc' => $request->cvc,
                ],
            ]);
        }catch (Exception $e) {
            return response()->json(['error' => 'Please provide valid card information'],403);
        }

        if (!isset($token['id'])) {
            return response()->json(['error' => 'Payment faild'],403);
        }

        $result = Stripe\Charge::create([
            'card' => $token['id'],
            'currency' => $stripe->currency->currency_code,
            'amount' => $payableAmount * 100,
            'description' => env('APP_NAME'),
        ]);

        if($result['status'] != 'succeeded') {
            return response()->json(['error' => 'Payment faild'],403);
        }

        $transaction_id = $result['balance_transaction'];
        $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Stripe', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$request->billing_address_id, $request->shipping_address_id);

        $this->sendOrderSuccessMail($user, $total_price, 'Stripe', 1, $order_result['order'], $order_result['order_details']);

        $this->sendOrderSuccessSms($user, $order_result['order']);


        $notification = trans('user_validation.Payment Successfully');
        $order = $order_result['order'];
        $order_id = $order->order_id;

        return response()->json(['message' => $notification, 'order_id' => $order_id],200);

    }

    public function razorpayOrder(Request $request){
        $user = Auth::guard('api')->user();

        $reedem_coins = $request->reedem_coins?? 0;

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);
        $razorpay = RazorpayPayment::first();
        $total_price = $total['total_price'];
        $payable_amount = $total_price-($reedem_coins*0.25);
        $payable_amount = (int) round($payable_amount, 2);
        $api = new Api($razorpay->key,$razorpay->secret_key);
        $order = $api->order->create(
            array('receipt' => '123', 'amount' => ($payable_amount*100), 'currency' => $razorpay->currency->currency_code)
        );

        $data = [
            "key"               => $razorpay->key,
            "amount"            => $payable_amount,
            "order_id"          => $order->id,
          ];

        return response()->json($data, 200);
    }

    public function razorpayWebView(Request $request){

        $rules = [
            'order_id'=>'required',
            'request_from'=>'required',
            'shipping_address_id'=>'required',
            'billing_address_id'=>'required',
            'shipping_method_id'=>'required',
            'amount'=>'required',
        ];
        $this->validate($request, $rules);

        $user = Auth::guard('api')->user();
        $cartProducts = ShoppingCart::with('product','variants.variantItem')->where('user_id', $user->id)->select('id','product_id','qty')->get();
        
        if($cartProducts->count() == 0){
            $notification = trans('user_validation.Your shopping cart is empty');
            return response()->json(['message' => $notification],403);
        }


        $orderId = $request->order_id;
        $razorpay = RazorpayPayment::first();
        $razorpay_key = $razorpay->key;
        $payable_amount = $request->amount;

        // Session::put('razorpay_order_id', $orderId);
        // Session::put('frontend_success_url', $request->frontend_success_url);
        // Session::put('frontend_faild_url', $request->frontend_faild_url);
        // Session::put('request_from', $request->request_from);
        // Session::put('shipping_address_id', $request->shipping_address_id);
        // Session::put('billing_address_id', $request->billing_address_id);
        // Session::put('shipping_method_id', $request->shipping_method_id);
        // Session::put('coupon', $request->coupon);
        // Session::put('amount', $request->amount);


        $frontend_success_url = $request->frontend_success_url;
        $frontend_faild_url = $request->frontend_faild_url;
        $request_from = $request->request_from;
        $shipping_address_id = $request->shipping_address_id;
        $billing_address_id = $request->billing_address_id;
        $shipping_method_id = $request->shipping_method_id;
        $reedem_coins = $request->reedem_coins;
        $coupon = $request->coupon;
        $token = $request->token;

        return view('razorpay_webview', compact('orderId','razorpay','payable_amount','frontend_success_url','frontend_faild_url','request_from','shipping_address_id','billing_address_id','shipping_method_id','coupon','token','reedem_coins'));
    }


    public function razorpayVerify(Request $request){
        $success = true;
        $error = "Payment Failed!";

        if (empty($request->razorpay_payment_id) === false) {
            $razorpay = RazorpayPayment::first();
            $api = new Api($razorpay->key,$razorpay->secret_key);
            try {
                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                ];
                $api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }
        }

        if ($success === true) {

            $reedem_coins = $request->reedem_coins?? 0;

            $user = Auth::guard('api')->user();

            $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

            $total_price = $total['total_price'];
            $coupon_price = $total['coupon_price'];
            $shipping_fee = $total['shipping_fee'];
            $productWeight = $total['productWeight'];
            $shipping = $total['shipping'];

            $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

            $setting = Setting::first();

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency->currency_rate,2);
            $currency_rate = $setting->currency->currency_rate;
            $currency_icon = $setting->currency->currency_icon;
            $currency_name = $setting->currency->currency_name;


            $transaction_id = $request->razorpay_payment_id;
            $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Razorpay', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$request->billing_address_id, $request->shipping_address_id,$reedem_coins);

            // $this->sendOrderSuccessMail($user, $total_price, 'Razorpay', 1, $order_result['order'], $order_result['order_details']);

            // $this->sendOrderSuccessSms($user, $order_result['order']);

            if($request->request_from == 'react_web'){
                $order = $order_result['order'];
                $success_url = $request->frontend_success_url;
                $success_url = $success_url. "/" . $order->order_id;
                return redirect($success_url);
            }else{
                return redirect()->route('user.checkout.order-success-url-for-mobile-app');
            }
        } else {
            if($request->request_from == 'react_web'){
                $faild_url = $request->frontend_faild_url;
            }else{
                return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
            }
        }

    }


    public function flutterwaveWebView(Request $request){

        $flutterwave = Flutterwave::first();
        $user = Auth::guard('api')->user();

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);
        $total_price = $total['total_price'];

        $frontend_success_url = $request->frontend_success_url;
        $frontend_faild_url = $request->frontend_faild_url;
        $request_from = $request->request_from;
        $shipping_address_id = $request->shipping_address_id;
        $billing_address_id = $request->billing_address_id;
        $shipping_method_id = $request->shipping_method_id;
        $coupon = $request->coupon;
        $token = $request->token;

        return view('flutterwave_webview', compact('flutterwave','user','total_price','frontend_success_url','frontend_faild_url','request_from','shipping_address_id','billing_address_id','shipping_method_id','coupon','token'));
    }

    public function payWithFlutterwave(Request $request){
        $flutterwave = Flutterwave::first();
        $curl = curl_init();
        $tnx_id = $request->tnx_id;
        $url = "https://api.flutterwave.com/v3/transactions/$tnx_id/verify";
        $token = $flutterwave->secret_key;
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $token"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);


        if($response->status == 'success'){
            $user = Auth::guard('api')->user();

            $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

            $total_price = $total['total_price'];
            $coupon_price = $total['coupon_price'];
            $shipping_fee = $total['shipping_fee'];
            $productWeight = $total['productWeight'];
            $shipping = $total['shipping'];



            $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

            $setting = Setting::first();

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency->currency_rate,2);
            $currency_rate = $setting->currency->currency_rate;
            $currency_icon = $setting->currency->currency_icon;
            $currency_name = $setting->currency->currency_name;


            $transaction_id = $request->tnx_id;
            $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Flutterwave', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$request->billing_address_id, $request->shipping_address_id);

            $this->sendOrderSuccessMail($user, $total_price, 'Flutterwave', 1, $order_result['order'], $order_result['order_details']);

            $this->sendOrderSuccessSms($user, $order_result['order']);

            $order = $order_result['order'];
            $order_id = $order->order_id;
            $notification = trans('user_validation.Payment Successfully');
            return response()->json(['status' => 'success' , 'message' => $notification, 'order_id' => $order_id],200);
        }else{
            $notification = trans('user_validation.Payment Faild');
            return response()->json(['status' => 'faild' , 'message' => $notification],403);
        }
    }

    public function payWithMollie(Request $request){
        $rules = [
            'request_from'=>'required',
            'shipping_address_id'=>'required',
            'billing_address_id'=>'required',
            'shipping_method_id'=>'required',
        ];
        $this->validate($request, $rules);

        $user = Auth::guard('api')->user();

        Session::put('frontend_success_url', $request->frontend_success_url);
        Session::put('frontend_faild_url', $request->frontend_faild_url);
        Session::put('request_from', $request->request_from);
        Session::put('shipping_address_id', $request->shipping_address_id);
        Session::put('billing_address_id', $request->billing_address_id);
        Session::put('shipping_method_id', $request->shipping_method_id);
        Session::put('coupon', $request->coupon);
        Session::put('user', $user);

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

        $total_price = $total['total_price'];
        $coupon_price = $total['coupon_price'];
        $shipping_fee = $total['shipping_fee'];
        $productWeight = $total['productWeight'];
        $shipping = $total['shipping'];


        $amount_real_currency = $total_price;
        $mollie = PaystackAndMollie::first();
        $price = $amount_real_currency * $mollie->currency->currency_rate;
        $price = sprintf('%0.2f', $price);

        $mollie_api_key = $mollie->mollie_key;
        $currency = strtoupper($mollie->currency->currency_code);
        Mollie::api()->setApiKey($mollie_api_key);
        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => $currency,
                'value' => ''.$price.'',
            ],
            'description' => env('APP_NAME'),
            'redirectUrl' => route('user.checkout.mollie-payment-success'),
        ]);

        $payment = Mollie::api()->payments()->get($payment->id);
        session()->put('payment_id',$payment->id);
        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function molliePaymentSuccess(Request $request){
        $mollie = PaystackAndMollie::first();
        $mollie_api_key = $mollie->mollie_key;
        Mollie::api()->setApiKey($mollie_api_key);
        $payment = Mollie::api()->payments->get(session()->get('payment_id'));
        if ($payment->isPaid()){

            $user = Session::get('user');
            $coupon = Session::get('coupon');
            $shipping_address_id = Session::get('shipping_address_id');
            $billing_address_id = Session::get('billing_address_id');
            $shipping_method_id = Session::get('shipping_method_id');
            $payment_id = Session::get('payment_id');

            $total = $this->calculateCartTotal($user, $coupon, $shipping_method_id);

            $total_price = $total['total_price'];
            $coupon_price = $total['coupon_price'];
            $shipping_fee = $total['shipping_fee'];
            $productWeight = $total['productWeight'];
            $shipping = $total['shipping'];

            $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

            $setting = Setting::first();

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency->currency_rate,2);
            $currency_rate = $setting->currency->currency_rate;
            $currency_icon = $setting->currency->currency_icon;
            $currency_name = $setting->currency->currency_name;


            $transaction_id = $payment_id;
            $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Mollie', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$billing_address_id, $shipping_address_id);

            $this->sendOrderSuccessMail($user, $total_price, 'Mollie', 1, $order_result['order'], $order_result['order_details']);

            $this->sendOrderSuccessSms($user, $order_result['order']);

            $frontend_success_url = Session::get('frontend_success_url');
            $request_from = Session::get('request_from');

            if($request_from == 'react_web'){
                $order = $order_result['order'];
                $success_url = $frontend_success_url;
                $success_url = $success_url. "/" . $order->order_id;
                return redirect($success_url);
            }else{
                return redirect()->route('user.checkout.order-success-url-for-mobile-app');
            }
        }else{
            $frontend_faild_url = Session::get('frontend_faild_url');
            $request_from = Session::get('request_from');

            if($request_from == 'react_web'){
                return redirect($frontend_faild_url);
            }else{
                return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
            }
        }
    }


    public function paystackWebView(Request $request){
        $paystack = PaystackAndMollie::first();
        $user = Auth::guard('api')->user();

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);
        $total_price = $total['total_price'];

        $frontend_success_url = $request->frontend_success_url;
        $frontend_faild_url = $request->frontend_faild_url;
        $request_from = $request->request_from;
        $shipping_address_id = $request->shipping_address_id;
        $billing_address_id = $request->billing_address_id;
        $shipping_method_id = $request->shipping_method_id;
        $coupon = $request->coupon;
        $token = $request->token;

        return view('paystack_webview', compact('paystack','user','total_price','frontend_success_url','frontend_faild_url','request_from','shipping_address_id','billing_address_id','shipping_method_id','coupon','token'));
    }

    public function payWithPayStack(Request $request){
        $paystack = PaystackAndMollie::first();

        $reference = $request->reference;
        $transaction = $request->tnx_id;
        $secret_key = $paystack->paystack_secret_key;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST =>0,
            CURLOPT_SSL_VERIFYPEER =>0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $secret_key",
                "Cache-Control: no-cache",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $final_data = json_decode($response);
        if($final_data->status == true) {

            $user = Auth::guard('api')->user();

            $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

            $total_price = $total['total_price'];
            $coupon_price = $total['coupon_price'];
            $shipping_fee = $total['shipping_fee'];
            $productWeight = $total['productWeight'];
            $shipping = $total['shipping'];

            $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

            $setting = Setting::first();

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency->currency_rate,2);
            $currency_rate = $setting->currency->currency_rate;
            $currency_icon = $setting->currency->currency_icon;
            $currency_name = $setting->currency->currency_name;


            $transaction_id = $request->tnx_id;
            $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Paystack', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$request->billing_address_id, $request->shipping_address_id);

            $this->sendOrderSuccessMail($user, $total_price, 'Paystack', 1, $order_result['order'], $order_result['order_details']);

            $this->sendOrderSuccessSms($user, $order_result['order']);

            $order = $order_result['order'];
            $order_id = $order->order_id;
            $notification = trans('user_validation.Payment Successfully');
            return response()->json(['status' => 'success' , 'message' => $notification, 'order_id' => $order_id],200);
        }else{
            $notification = trans('user_validation.Payment Faild');
            return response()->json(['status' => 'faild' , 'message' => $notification],403);
        }
    }


    public function payWithInstamojo(Request $request){
        $rules = [
            'request_from'=>'required',
            'shipping_address_id'=>'required',
            'billing_address_id'=>'required',
            'shipping_method_id'=>'required',
        ];
        $this->validate($request, $rules);

        $user = Auth::guard('api')->user();

        Session::put('frontend_success_url', $request->frontend_success_url);
        Session::put('frontend_faild_url', $request->frontend_faild_url);
        Session::put('request_from', $request->request_from);
        Session::put('shipping_address_id', $request->shipping_address_id);
        Session::put('billing_address_id', $request->billing_address_id);
        Session::put('shipping_method_id', $request->shipping_method_id);
        Session::put('coupon', $request->coupon);
        Session::put('user', $user);

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

        $total_price = $total['total_price'];
        $coupon_price = $total['coupon_price'];
        $shipping_fee = $total['shipping_fee'];
        $productWeight = $total['productWeight'];
        $shipping = $total['shipping'];


        $amount_real_currency = $total_price;
        $instamojoPayment = InstamojoPayment::first();
        $price = $amount_real_currency * $instamojoPayment->currency->currency_rate;
        $price = round($price,2);

        $environment = $instamojoPayment->account_mode;
        $api_key = $instamojoPayment->api_key;
        $auth_token = $instamojoPayment->auth_token;

        if($environment == 'Sandbox') {
            $url = 'https://test.instamojo.com/api/1.1/';
        } else {
            $url = 'https://www.instamojo.com/api/1.1/';
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url.'payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:$api_key",
                "X-Auth-Token:$auth_token"));
        $payload = Array(
            'purpose' => env("APP_NAME"),
            'amount' => $price,
            'phone' => '918160651749',
            'buyer_name' => Auth::user()->name,
            'redirect_url' => route('user.checkout.instamojo-response'),
            'send_email' => true,
            'webhook' => 'http://www.example.com/webhook/',
            'send_sms' => true,
            'email' => Auth::user()->email,
            'allow_repeated_payments' => false
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);
        return redirect($response->payment_request->longurl);
    }

    public function instamojoResponse(Request $request){
        $input = $request->all();

        $instamojoPayment = InstamojoPayment::first();
        $environment = $instamojoPayment->account_mode;
        $api_key = $instamojoPayment->api_key;
        $auth_token = $instamojoPayment->auth_token;

        if($environment == 'Sandbox') {
            $url = 'https://test.instamojo.com/api/1.1/';
        } else {
            $url = 'https://www.instamojo.com/api/1.1/';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'payments/'.$request->get('payment_id'));
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:$api_key",
                "X-Auth-Token:$auth_token"));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            $frontend_faild_url = Session::get('frontend_faild_url');
            $request_from = Session::get('request_from');

            if($request_from == 'react_web'){
                return redirect($frontend_faild_url);
            }else{
                return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
            }
        } else {
            $data = json_decode($response);
        }

        if($data->success == true) {
            if($data->payment->status == 'Credit') {
                $user = Session::get('user');
                $coupon = Session::get('coupon');
                $shipping_address_id = Session::get('shipping_address_id');
                $billing_address_id = Session::get('billing_address_id');
                $shipping_method_id = Session::get('shipping_method_id');
                $payment_id = $request->get('payment_id');

                $total = $this->calculateCartTotal($user, $coupon, $shipping_method_id);

                $total_price = $total['total_price'];
                $coupon_price = $total['coupon_price'];
                $shipping_fee = $total['shipping_fee'];
                $productWeight = $total['productWeight'];
                $shipping = $total['shipping'];

                $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

                $setting = Setting::first();

                $amount_real_currency = $total_price;
                $amount_usd = round($total_price / $setting->currency->currency_rate,2);
                $currency_rate = $setting->currency->currency_rate;
                $currency_icon = $setting->currency->currency_icon;
                $currency_name = $setting->currency->currency_name;


                $transaction_id = $payment_id;
                $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Instamojo', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$billing_address_id, $shipping_address_id);

                $this->sendOrderSuccessMail($user, $total_price, 'Instamojo', 1, $order_result['order'], $order_result['order_details']);

                $this->sendOrderSuccessSms($user, $order_result['order']);

                $frontend_success_url = Session::get('frontend_success_url');
                $request_from = Session::get('request_from');

                if($request_from == 'react_web'){
                    $order = $order_result['order'];
                    $success_url = $frontend_success_url;
                    $success_url = $success_url. "/" . $order->order_id;
                    return redirect($success_url);
                }else{
                    return redirect()->route('user.checkout.order-success-url-for-mobile-app');
                }

            }
        }

    }

    public function payWithBank(Request $request){
        $rules = [
            'shipping_address_id'=>'required',
            'billing_address_id'=>'required',
            'shipping_method_id'=>'required',
            'tnx_info'=>'required',
        ];
        $customMessages = [
            'shipping_address_id.required' => trans('user_validation.Shipping address is required'),
            'billing_address_id.required' => trans('user_validation.Billing address is required'),
            'shipping_method_id.required' => trans('user_validation.Shipping method is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = Auth::guard('api')->user();

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

        $total_price = $total['total_price'];
        $coupon_price = $total['coupon_price'];
        $shipping_fee = $total['shipping_fee'];
        $productWeight = $total['productWeight'];
        $shipping = $total['shipping'];

        $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

        $setting = Setting::first();

        $amount_real_currency = $total_price;
        $amount_usd = round($total_price / $setting->currency->currency_rate,2);
        $currency_rate = $setting->currency->currency_rate;
        $currency_icon = $setting->currency->currency_icon;
        $currency_name = $setting->currency->currency_name;

        $transaction_id = $request->tnx_info;
        $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Bank Payment', $transaction_id , 0, $shipping, $shipping_fee, $coupon_price, 1, $request->billing_address_id, $request->shipping_address_id);

        $this->sendOrderSuccessMail($user, $total_price, 'Bank Payment', 0, $order_result['order'], $order_result['order_details']);

        $this->sendOrderSuccessSms($user, $order_result['order']);

        $notification = trans('user_validation.Order submited successfully. please wait for admin approval');

        $order = $order_result['order'];
        $order_id = $order->order_id;

        return response()->json(['message' => $notification, 'order_id' => $order_id],200);
    }

    public function sslcommerzWebView(Request $request){

        $sslcommerzPaymentInfo = SslcommerzPayment::first();
        $user = Auth::guard('api')->user();

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);
        $total_price = $total['total_price'];
        $total_price = round($total_price * $sslcommerzPaymentInfo->currency->currency_rate,2);

        $frontend_success_url = $request->frontend_success_url;
        $frontend_faild_url = $request->frontend_faild_url;
        $request_from = $request->request_from;
        $shipping_address_id = $request->shipping_address_id;
        $billing_address_id = $request->billing_address_id;
        $shipping_method_id = $request->shipping_method_id;
        $coupon = $request->coupon;
        $token = $request->token;

        Session::put('frontend_success_url', $request->frontend_success_url);
        Session::put('frontend_faild_url', $request->frontend_faild_url);
        Session::put('request_from', $request->request_from);
        Session::put('shipping_address_id', $request->shipping_address_id);
        Session::put('billing_address_id', $request->billing_address_id);
        Session::put('shipping_method_id', $request->shipping_method_id);
        Session::put('coupon', $request->coupon);
        Session::put('user', $user);


        return view('sslcommerz_webview', compact('total_price','sslcommerzPaymentInfo','token'));
    }

    public function sslcommerz(Request $request)
    {

        $user = Auth::guard('api')->user();
        $coupon = Session::get('coupon');
        $shipping_method_id = Session::get('shipping_method_id');
        $total = $this->calculateCartTotal($user, $coupon, $shipping_method_id);
        $total_price = $total['total_price'];

        $sslcommerzPaymentInfo = SslcommerzPayment::first();
        $payableAmount = round($total_price * $sslcommerzPaymentInfo->currency->currency_rate,2);

        $post_data = array();
        $post_data['total_amount'] = $payableAmount; # You cant not pay less than 10
        $post_data['currency'] = $sslcommerzPaymentInfo->currency->currency_code;
        $post_data['tran_id'] = uniqid();

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $user->name;
        $post_data['cus_email'] = $user->email ? $user->email : 'johndoe@gmail.com';
        $post_data['cus_add1'] = '';
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Country";
        $post_data['cus_phone'] =  $user->phone ? $user->phone : '123456789';
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "";
        $post_data['ship_add1'] = "";
        $post_data['ship_add2'] = "";
        $post_data['ship_city'] = "";
        $post_data['ship_state'] = "";
        $post_data['ship_postcode'] = "";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = 'Test Product';
        $post_data['product_category'] = "Package";
        $post_data['product_profile'] = "Package";

        config(['sslcommerz.apiCredentials.store_id' => $sslcommerzPaymentInfo->store_id]);
        config(['sslcommerz.apiCredentials.store_password' => $sslcommerzPaymentInfo->store_password]);
        config(['sslcommerz.success_url' => '/user/checkout/sslcommerz-success']);
        config(['sslcommerz.failed_url' => '/user/checkout/sslcommerz-failed']);

        $sslc = new SslCommerzNotification(config('sslcommerz'));

        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }

    }


    public function sslcommerz_success(Request $request)
    {

        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslcommerzPaymentInfo = SslcommerzPayment::first();

        config(['sslcommerz.apiCredentials.store_id' => $sslcommerzPaymentInfo->store_id]);
        config(['sslcommerz.apiCredentials.store_password' => $sslcommerzPaymentInfo->store_password]);
        config(['sslcommerz.success_url' => '/user/checkout/sslcommerz-success']);
        config(['sslcommerz.failed_url' => '/user/checkout/sslcommerz-failed']);

        $sslc = new SslCommerzNotification(config('sslcommerz'));

        $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

        if ($validation == TRUE) {
            $user = Session::get('user');
            $coupon = Session::get('coupon');
            $shipping_address_id = Session::get('shipping_address_id');
            $billing_address_id = Session::get('billing_address_id');
            $shipping_method_id = Session::get('shipping_method_id');
            $payment_id = $request->get('payment_id');

            $total = $this->calculateCartTotal($user, $coupon, $shipping_method_id);

            $total_price = $total['total_price'];
            $coupon_price = $total['coupon_price'];
            $shipping_fee = $total['shipping_fee'];
            $productWeight = $total['productWeight'];
            $shipping = $total['shipping'];

            $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

            $setting = Setting::first();

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency->currency_rate,2);
            $currency_rate = $setting->currency->currency_rate;
            $currency_icon = $setting->currency->currency_icon;
            $currency_name = $setting->currency->currency_name;

            $transaction_id = $payment_id;
            $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Instamojo', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$billing_address_id, $shipping_address_id);

            $this->sendOrderSuccessMail($user, $total_price, 'Instamojo', 1, $order_result['order'], $order_result['order_details']);

            $this->sendOrderSuccessSms($user, $order_result['order']);

            $frontend_success_url = Session::get('frontend_success_url');
            $request_from = Session::get('request_from');

            if($request_from == 'react_web'){
                $order = $order_result['order'];
                $success_url = $frontend_success_url;
                $success_url = $success_url. "/" . $order->order_id;
                return redirect($success_url);
            }else{
                return response()->json(['message' => trans('user_validation.Order Successfully')],200);
            }
        } else {
            $frontend_faild_url = Session::get('frontend_faild_url');
            $request_from = Session::get('request_from');

            if($request_from == 'react_web'){
                return redirect($frontend_faild_url);
            }else{
                return response()->json(['message' => trans('user_validation.Payment Faild')],403);
            }
        }

    }


    public function sslcommerz_failed(Request $request)
    {
        $frontend_faild_url = Session::get('frontend_faild_url');
        $request_from = Session::get('request_from');

        if($request_from == 'react_web'){
            return redirect($frontend_faild_url);
        }else{
            return response()->json(['message' => trans('user_validation.Payment Faild')],403);
        }
    }



    public function calculateCartTotal($user, $request_coupon, $request_shipping_method_id){
        $total_price = 0;
        $coupon_price = 0;
        $shipping_fee = 0;
        $productWeight = 0;

        $cartProducts = ShoppingCart::with('product','variants.variantItem')->where('user_id', $user->id)->select('id','product_id','qty')->get();
        if($cartProducts->count() == 0){
            $notification = trans('user_validation.Your shopping cart is empty');
            return response()->json(['message' => $notification],403);
        }
        foreach($cartProducts as $index => $cartProduct){
            $variantPrice = 0;
            if($cartProduct->variants){
                foreach($cartProduct->variants as $item_index => $var_item){
                  $item = ProductVariantItem::find($var_item->variant_item_id);
                  if($item){
                    $variantPrice += $item->price;
                  }
                }
            }

            $product = Product::select('id','price','offer_price','weight')->find($cartProduct->product_id);
            $price = $product->offer_price ? $product->offer_price : $product->price;
            $price = $price + $variantPrice;
            $weight = $product->weight;
            $weight = $weight * $cartProduct->qty;
            $productWeight += $weight;
            $isFlashSale = FlashSaleProduct::where(['product_id' => $product->id,'status' => 1])->first();
            $today = date('Y-m-d H:i:s');
            if($isFlashSale){
                $flashSale = FlashSale::first();
                if($flashSale->status == 1){
                    if($today <= $flashSale->end_time){
                        $offerPrice = ($flashSale->offer / 100) * $price;
                        $price = $price - $offerPrice;
                    }
                }
            }

            $price = $price * $cartProduct->qty;
            $total_price += $price;
        }

        // calculate coupon coast
        if($request_coupon){
            $coupon = Coupon::where(['code' => $request_coupon, 'status' => 1])->first();
            if($coupon){
                if($coupon->expired_date >= date('Y-m-d')){
                    if($coupon->apply_qty <  $coupon->max_quantity ){
                        if($coupon->offer_type == 1){
                            $couponAmount = $coupon->discount;
                            $couponAmount = ($couponAmount / 100) * $total_price;
                        }elseif($coupon->offer_type == 2){
                            $couponAmount = $coupon->discount;
                        }
                        $coupon_price = $couponAmount;

                        $qty = $coupon->apply_qty;
                        $qty = $qty +1;
                        $coupon->apply_qty = $qty;
                        $coupon->save();

                    }
                }
            }
        }

        $shipping = Shipping::find($request_shipping_method_id);
        if(!$shipping){
            return response()->json(['message' => trans('user_validation.Shipping method not found')],403);
        }

        if($shipping->shipping_fee == 0){
            $shipping_fee = 0;
        }else{
            $shipping_fee = $shipping->shipping_fee;
        }

        $total_price = ($total_price - $coupon_price) + $shipping_fee;
        $total_price = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), '', $total_price);
        $total_price = number_format($total_price, 2, '.', '');

        $arr = [];
        $arr['total_price'] = $total_price;
        $arr['coupon_price'] = $coupon_price;
        $arr['shipping_fee'] = $shipping_fee;
        $arr['productWeight'] = $productWeight;
        $arr['shipping'] = $shipping;

        return $arr;
    }

    public function orderStore($user, $total_price, $totalProduct, $payment_method, $transaction_id, $paymetn_status, $shipping, $shipping_fee, $coupon_price, $cash_on_delivery,$billing_address_id,$shipping_address_id,$reedem_coins=0){
        $cartProducts = ShoppingCart::with('product','variants.variantItem')->where('user_id', $user->id)->select('id','product_id','qty')->get();
        if($cartProducts->count() == 0){
            $notification = trans('user_validation.Your shopping cart is empty');
            return response()->json(['message' => $notification],403);
        }

        $order = new Order();
        $orderId = substr(rand(0,time()),0,10);
        $order->order_id = $orderId;
        $order->user_id = $user->id;
        $order->total_amount = $total_price;
        $order->product_qty = $totalProduct;
        $order->payment_method = $payment_method;
        $order->transection_id = $transaction_id;
        $order->payment_status = $paymetn_status;
        $order->shipping_method = $shipping->shipping_rule;
        $order->shipping_cost = $shipping_fee;
        $order->coupon_coast = $coupon_price;
        $order->order_status = 0;
        $order->cash_on_delivery = $cash_on_delivery;
        $order->reedem_coins = $reedem_coins;
        $order->save();

        $order_details = '';
       $currency= MultiCurrency::where('is_default','Yes')->first();
        foreach($cartProducts as $key => $cartProduct){

            $variantPrice = 0;
            if($cartProduct->variants){
                foreach($cartProduct->variants as $item_index => $var_item){
                  $item = ProductVariantItem::find($var_item->variant_item_id);
                  if($item){
                    $variantPrice += $item->price;
                  }
                }
            }

            // calculate product price
            $product = Product::select('id','price','offer_price','weight','vendor_id','qty','name')->find($cartProduct->product_id);
            $price = $product->offer_price ? $product->offer_price : $product->price;
            $price = $price + $variantPrice;
            $isFlashSale = FlashSaleProduct::where(['product_id' => $product->id,'status' => 1])->first();
            $today = date('Y-m-d H:i:s');
            if($isFlashSale){
                $flashSale = FlashSale::first();
                if($flashSale->status == 1){
                    if($today <= $flashSale->end_time){
                        $offerPrice = ($flashSale->offer / 100) * $price;
                        $price = $price - $offerPrice;
                    }
                }
            }

            // store ordre product
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $cartProduct->product_id;
            $orderProduct->seller_id = $product->vendor_id;
            $orderProduct->product_name = $product->name;
            $orderProduct->unit_price = $price;
            $orderProduct->qty = $cartProduct->qty;
            $orderProduct->save();

            // update product stock
            $qty = $product->qty - $cartProduct->qty;
            $product->qty = $qty;
            $product->save();

            // store prouct variant

            // return $cartProduct->variants;
            foreach($cartProduct->variants as $index => $variant){
                $item = ProductVariantItem::find($variant->variant_item_id);
                $productVariant = new OrderProductVariant();
                $productVariant->order_product_id = $orderProduct->id;
                $productVariant->product_id = $cartProduct->product_id;
                $productVariant->variant_name = $item->product_variant_name;
                $productVariant->variant_value = $item->name;
                $productVariant->save();
            }

            $order_details.='Product: '.$product->name. '<br>';
            $order_details.='Quantity: '. $cartProduct->qty .'<br>';

            $order_details.='Price: '.$currency->currency_icon . $cartProduct->qty * $price .'<br>';


        }

        if($reedem_coins > 0){

            $coinsdata = UserCoins::create([
                            'order_id' => $order->id,
                            'user_id' => $user->id,
                            'coin_expires' => now()->addDays(30),
                            'coins' => $reedem_coins,
                            'type' => 'debit',
                            'status' => 1,
                        ]);
        }

        // store shipping and billing address
        $billing = Address::find($billing_address_id);
        $shipping = Address::find($shipping_address_id);
        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $order->id;
        $orderAddress->billing_name = $billing->name;
        $orderAddress->billing_email = $billing->email;
        $orderAddress->billing_phone = $billing->phone;
        $orderAddress->billing_address = $billing->address;
        $orderAddress->billing_country = $billing->country->name;
        $orderAddress->billing_state = $billing->countryState->name;
        $orderAddress->billing_city = $billing->city->name;
        $orderAddress->billing_address_type = $billing->type;
        $orderAddress->shipping_name = $shipping->name;
        $orderAddress->shipping_email = $shipping->email;
        $orderAddress->shipping_phone = $shipping->phone;
        $orderAddress->shipping_address = $shipping->address;
        $orderAddress->shipping_country = $shipping->country->name ;
        $orderAddress->shipping_state = $shipping->countryState->name;
        $orderAddress->shipping_city = $shipping->city->name;
        $orderAddress->shipping_address_type = $shipping->type;
        $orderAddress->save();

        foreach($cartProducts as $cartProduct){
            ShoppingCartVariant::where('shopping_cart_id', $cartProduct->id)->delete();
            $cartProduct->delete();
        }

        $arr = [];
        $arr['order'] = $order;
        $arr['order_details'] = $order_details;

        return $arr;
    }


    public function sendOrderSuccessMail($user, $total_price, $payment_method, $payment_status, $order, $order_details){
        $currency= MultiCurrency::where('is_default','Yes')->first();
        MailHelper::setMailConfig();

        $template=EmailTemplate::where('id',6)->first();
        $subject=$template->subject;
        $message=$template->description;
        $message = str_replace('{{user_name}}',$user->name,$message);

        $message = str_replace('{{total_amount}}',$currency->currency_icon.$total_price,$message);

        $message = str_replace('{{payment_method}}',$payment_method,$message);
        $message = str_replace('{{payment_status}}',$payment_status,$message);
        $message = str_replace('{{orde``r_status}}','Pending',$message);
        $message = str_replace('{{order_date}}',$order->created_at->format('d F, Y'),$message);
        $message = str_replace('{{order_detail}}',$order_details,$message);
        Mail::to($user->email)->send(new OrderSuccessfully($message,$subject));
    }

    public function sendOrderSuccessSms($user, $order){
        $template=SmsTemplate::where('id',3)->first();
        $message=$template->description;
        $message = str_replace('{{user_name}}',$user->name,$message);
        $message = str_replace('{{order_id}}',$order->order_id,$message);

        $twilio = TwilioSms::first();
        if($twilio->enable_order_confirmation_sms == 1){
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
        if($biztech->enable_order_confirmation_sms == 1){
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


    public function myfatoorah_webview(Request $request) {
        // try {
            $rules = [
                'request_from'=>'required',
                'shipping_address_id'=>'required',
                'billing_address_id'=>'required',
                'shipping_method_id'=>'required',
            ];
            $this->validate($request, $rules);

            $user = Auth::guard('api')->user();

            Session::put('frontend_success_url', $request->frontend_success_url);
            Session::put('frontend_faild_url', $request->frontend_faild_url);
            Session::put('request_from', $request->request_from);
            Session::put('shipping_address_id', $request->shipping_address_id);
            Session::put('billing_address_id', $request->billing_address_id);
            Session::put('shipping_method_id', $request->shipping_method_id);
            Session::put('coupon', $request->coupon);
            Session::put('user', $user);

            $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

            $total_price = $total['total_price'];
            $coupon_price = $total['coupon_price'];
            $shipping_fee = $total['shipping_fee'];
            $productWeight = $total['productWeight'];
            $shipping = $total['shipping'];


            $amount_real_currency = $total_price;
            $myfatoorah = MyfatoorahPayment::first();
            $price = $amount_real_currency * $myfatoorah->currency->currency_rate;
            $price = sprintf('%0.2f', $price);


            $paymentMethodId = 0;

            $curlData = $this->getPayLoadData();
            $data     = $this->mfObj->getInvoiceURL($curlData, $paymentMethodId);

            return redirect()->to($data['invoiceURL']);
        // } catch (\Exception $e) {
        //    if($request->request_from == 'react_web'){
        //         return redirect()->to($request->frontend_faild_url);
        //     }else{
        //         return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
        //     }
        // }
    }


    private function getPayLoadData($orderId = null) {
        $callbackURL = route('user.checkout.myfatoorah-webview-callback');

        return [
            'CustomerName'       => 'FName LName',
            'InvoiceValue'       => '10',
            'DisplayCurrencyIso' => 'KWD',
            'CustomerEmail'      => 'test@test.com',
            'CallBackUrl'        => $callbackURL,
            'ErrorUrl'           => $callbackURL,
            'MobileCountryCode'  => '+965',
            'CustomerMobile'     => '12345678',
            'Language'           => 'en',
            'CustomerReference'  => $orderId,
            'SourceInfo'         => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package ' . MYFATOORAH_LARAVEL_PACKAGE_VERSION
        ];
    }


    public function myfatoorah_webview_callback() {
        try {
            $paymentId = request('paymentId');
            $data      = $this->mfObj->getPaymentStatus($paymentId, 'PaymentId');

            if ($data->InvoiceStatus == 'Paid') {
                $user = Session::get('user');
                $coupon = Session::get('coupon');
                $shipping_address_id = Session::get('shipping_address_id');
                $billing_address_id = Session::get('billing_address_id');
                $shipping_method_id = Session::get('shipping_method_id');
                $payment_id = Session::get('payment_id');

                $total = $this->calculateCartTotal($user, $coupon, $shipping_method_id);

                $total_price = $total['total_price'];
                $coupon_price = $total['coupon_price'];
                $shipping_fee = $total['shipping_fee'];
                $productWeight = $total['productWeight'];
                $shipping = $total['shipping'];

                $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');

                $setting = Setting::first();

                $amount_real_currency = $total_price;
                $amount_usd = round($total_price / $setting->currency->currency_rate,2);
                $currency_rate = $setting->currency->currency_rate;
                $currency_icon = $setting->currency->currency_icon;
                $currency_name = $setting->currency->currency_name;

                $transaction_id = $paymentId;
                $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Myfatoorah', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$billing_address_id, $shipping_address_id);

                $this->sendOrderSuccessMail($user, $total_price, 'Myfatoorah', 1, $order_result['order'], $order_result['order_details']);

                $this->sendOrderSuccessSms($user, $order_result['order']);

                $frontend_success_url = Session::get('frontend_success_url');
                $request_from = Session::get('request_from');

                if($request_from == 'react_web'){
                    $order = $order_result['order'];
                    $success_url = $frontend_success_url;
                    $success_url = $success_url. "/" . $order->order_id;
                    return redirect($success_url);
                }else{
                    return redirect()->route('user.checkout.order-success-url-for-mobile-app');
                }

            } else if ($data->InvoiceStatus == 'Failed') {

                $frontend_faild_url = Session::get('frontend_faild_url');
                $request_from = Session::get('request_from');
                if($request_from == 'react_web'){
                    return redirect($frontend_faild_url);
                }else{
                    return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
                }

            } else if ($data->InvoiceStatus == 'Expired') {
                $frontend_faild_url = Session::get('frontend_faild_url');
                $request_from = Session::get('request_from');
                if($request_from == 'react_web'){
                    return redirect($frontend_faild_url);
                }else{
                    return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
                }
            }

            $frontend_faild_url = Session::get('frontend_faild_url');
            $request_from = Session::get('request_from');
            if($request_from == 'react_web'){
                return redirect($frontend_faild_url);
            }else{
                return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
            }
        } catch (\Exception $e) {

            $frontend_faild_url = Session::get('frontend_faild_url');
            $request_from = Session::get('request_from');
            if($request_from == 'react_web'){
                return redirect($frontend_faild_url);
            }else{
                return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
            }

        }

        $frontend_faild_url = Session::get('frontend_faild_url');
        $request_from = Session::get('request_from');
        if($request_from == 'react_web'){
            return redirect($frontend_faild_url);
        }else{
            return redirect()->route('user.checkout.order-fail-url-for-mobile-app');
        }
    }
}


