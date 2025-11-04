<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;

use App\Models\BreadcrumbImage;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\City;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\Vendor;
use App\Models\ShippingMethod;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductVariant;
use App\Models\OrderAddress;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StripePayment;
use App\Mail\OrderSuccessfully;
use App\Helpers\MailHelper;
use App\Models\EmailTemplate;
use App\Models\PaypalPayment;
use App\Models\Coupon;
use App\Models\ShoppingCart;

use App\Models\Shipping;
use App\Models\Address;
use App\Models\ShoppingCartVariant;


use App\Models\RazorpayPayment;
use App\Models\Flutterwave;
use App\Models\PaystackAndMollie;
use App\Models\InstamojoPayment;
use App\Models\ProductVariantItem;
use App\Models\FlashSaleProduct;
use App\Models\FlashSale;


use Str;
use Cart;
use Mail;
use Session;
use Auth;

use Twilio\Rest\Client;
use App\Models\SmsTemplate;
use App\Models\TwilioSms;
use App\Models\BiztechSms;
use Exception;

class PaypalController extends Controller
{
    private $apiContext;
    public function __construct()
    {
        $account = PaypalPayment::first();
        $paypal_conf = \Config::get('paypal');
        $this->apiContext = new ApiContext(new OAuthTokenCredential(
            $account->client_id,
            $account->secret_id,
            )
        );

        $setting=array(
            'mode' => $account->account_mode,
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => storage_path() . '/logs/paypal.log',
            'log.LogLevel' => 'ERROR'
        );
        $this->apiContext->setConfig($setting);
    }


    public function paypalWebView(Request $request){
        // return $request;
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

        $shipping_address_id = $request->shipping_address_id;
        $billing_address_id = $request->billing_address_id;
        $shipping_method_id = $request->shipping_method_id;
        $token = $request->token;
        $user = Auth::guard('api')->user();
        Session::put('shipping_address_id', $shipping_address_id);
        Session::put('billing_address_id', $billing_address_id);
        Session::put('shipping_method_id', $shipping_method_id);
        Session::put('user', $user);

        return view('paypal_btn', compact('shipping_method_id','shipping_address_id','token','billing_address_id'));
    }



    public function payWithPaypal(Request $request){
        // return $request;
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


        $user = Session::get('user');

        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

        $total_price = $total['total_price'];
        $coupon_price = $total['coupon_price'];
        $shipping_fee = $total['shipping_fee'];
        $productWeight = $total['productWeight'];
        $shipping = $total['shipping'];


        $amount_real_currency = $total_price;
        $paypalSetting = PaypalPayment::first();
        $payableAmount = round($total_price * $paypalSetting->currency_rate,2);

        $name=env('APP_NAME');

        // set payer
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // set amount total
        $amount = new Amount();
        $amount->setCurrency($paypalSetting->currency_code)
            ->setTotal($payableAmount);

        // transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription(env('APP_NAME'));

        // redirect url
        $redirectUrls = new RedirectUrls();

        $root_url=url('/');
        $redirectUrls->setReturnUrl($root_url."/user/checkout/paypal-payment-success")
            ->setCancelUrl($root_url."/user/checkout/paypal-payment-cancled");

        // payment
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        try {
            $payment->create($this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            $notification = trans('user_validation.Payment Faild');
            return response()->json(['Payment' => $Payment],403);
        }

        // get paymentlink
        $approvalUrl = $payment->getApprovalLink();

        return redirect($approvalUrl);
    }

    public function paypalPaymentSuccess(Request $request){

        if (empty($request->get('PayerID')) || empty($request->get('token'))) {
            $notification = trans('user_validation.Payment Faild');
            return response()->json(['notification' => $notification],403);
        }

        $payment_id=$request->get('paymentId');
        $payment = Payment::get($payment_id, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->apiContext);

        if ($result->getState() == 'approved') {

            $shipping_address_id = Session::get('shipping_address_id');
            $billing_address_id = Session::get('billing_address_id');
            $shipping_method_id = Session::get('shipping_method_id');

            $total_price = 0;
            $coupon_price = 0;
            $shipping_fee = 0;
            $productWeight = 0;

            // calculate total price
            $user = Session::get('user');
            $total = $this->calculateCartTotal($user, $request->coupon, $shipping_method_id);

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
            $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Paypal', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$billing_address_id, $shipping_address_id);

            $this->sendOrderSuccessMail($user, $total_price, 'Paypal', 1, $order_result['order'], $order_result['order_details']);

            $this->sendOrderSuccessSms($user, $order_result['order']);

            $notification = trans('user_validation.Payment Successfully');
            return response()->json(['message' => $notification],200);
        }
    }

    public function paypalPaymentCancled(){
        $notification = trans('user_validation.Payment Faild');
        return response()->json(['notification' => $notification],403);
    }

    public function paypalReactWebView(Request $request){
        $rules = [
            'shipping_address_id'=>'required',
            'billing_address_id'=>'required',
            'shipping_method_id'=>'required',
            'success_url'=>'required',
            'faild_url'=>'required',
        ];
        $customMessages = [
            'shipping_address_id.required' => trans('user_validation.Shipping address is required'),
            'billing_address_id.required' => trans('user_validation.Billing address is required'),
            'shipping_method_id.required' => trans('user_validation.Shipping method is required'),
            'success_url.required' => trans('user_validation.Success url is required'),
            'faild_url.required' => trans('user_validation.Faild url is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $shipping_address_id = $request->shipping_address_id;
        $billing_address_id = $request->billing_address_id;
        $shipping_method_id = $request->shipping_method_id;
        $token = $request->token;
        $user = Auth::guard('api')->user();
        Session::put('shipping_address_id', $shipping_address_id);
        Session::put('billing_address_id', $billing_address_id);
        Session::put('shipping_method_id', $shipping_method_id);
        Session::put('success_url', $request->success_url);
        Session::put('faild_url', $request->faild_url);
        Session::put('user', $user);

        return view('paypal_btn_for_react', compact('shipping_method_id','shipping_address_id','token','billing_address_id'));
    }


    public function payWithPaypalForReactJs(Request $request){
        // return $request;
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

        $user = Session::get('user');
        $total = $this->calculateCartTotal($user, $request->coupon, $request->shipping_method_id);

        $total_price = $total['total_price'];
        $coupon_price = $total['coupon_price'];
        $shipping_fee = $total['shipping_fee'];
        $productWeight = $total['productWeight'];
        $shipping = $total['shipping'];

        $amount_real_currency = $total_price;
        $paypalSetting = PaypalPayment::first();
        $payableAmount = round($total_price * $paypalSetting->currency->currency_rate,2);

        $name=env('APP_NAME');

        // set payer
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // set amount total
        $amount = new Amount();
        $amount->setCurrency($paypalSetting->currency->currency_code)
            ->setTotal($payableAmount);

        // transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription(env('APP_NAME'));

        // redirect url
        $redirectUrls = new RedirectUrls();

        $root_url=url('/');
        $redirectUrls->setReturnUrl($root_url."/user/checkout/paypal-payment-success-from-react")
            ->setCancelUrl($root_url."/user/checkout/paypal-payment-cancled-from-react");

        // payment
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        try {
            $payment->create($this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            $notification = trans('user_validation.Payment Faild');
            return response()->json(['Payment' => $Payment],403);
        }

        // get paymentlink
        $approvalUrl = $payment->getApprovalLink();

        return redirect($approvalUrl);
    }

    public function paypalPaymentSuccessFromReact(Request $request){

        if (empty($request->get('PayerID')) || empty($request->get('token'))) {
            $notification = trans('user_validation.Payment Faild');
            return response()->json(['notification' => $notification],403);
        }

        $payment_id=$request->get('paymentId');
        $payment = Payment::get($payment_id, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->apiContext);

        if ($result->getState() == 'approved') {

            $user = Session::get('user');

            $shipping_address_id = Session::get('shipping_address_id');
            $billing_address_id = Session::get('billing_address_id');
            $shipping_method_id = Session::get('shipping_method_id');

            $total = $this->calculateCartTotal($user, $request->coupon, $shipping_method_id);

            $total_price = $total['total_price'];
            $coupon_price = $total['coupon_price'];
            $shipping_fee = $total['shipping_fee'];
            $productWeight = $total['productWeight'];
            $shipping = $total['shipping'];

            // calculate total price

            $totalProduct = ShoppingCart::with('variants')->where('user_id', $user->id)->sum('qty');
            $setting = Setting::first();

            $amount_real_currency = $total_price;
            $amount_usd = round($total_price / $setting->currency->currency_rate,2);
            $currency_rate = $setting->currency->currency_rate;
            $currency_icon = $setting->currency->currency_icon;
            $currency_name = $setting->currency->currency_name;

            $transaction_id = $payment_id;
            $order_result = $this->orderStore($user, $total_price, $totalProduct, 'Paypal', $transaction_id, 1, $shipping, $shipping_fee, $coupon_price, 0,$billing_address_id, $shipping_address_id);

            $this->sendOrderSuccessMail($user, $total_price, 'Paypal', 1, $order_result['order'], $order_result['order_details']);

            $order = $order_result['order'];
            $success_url = Session::get('success_url');
            $success_url = $success_url. "/" . $order->order_id;
            return redirect($success_url);
        }
    }

    public function paypalPaymentCancledFromReact(){
        $faild_url = Session::get('faild_url');
        return redirect($faild_url);
    }

    public function calculateCartTotal($user, $request_coupon, $request_shipping_method_id){
        $total_price = 0;
        $coupon_price = 0;
        $shipping_fee = 0;
        $productWeight = 0;

        // calculate total price
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

    public function orderStore($user,$total_price, $totalProduct, $payment_method, $transaction_id, $paymetn_status, $shipping, $shipping_fee, $coupon_price, $cash_on_delivery,$billing_address_id,$shipping_address_id){
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
        $order->save();

        $order_details = '';
        $setting = Setting::first();
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
            $order_details.='Price: '.$setting->currency->currency_icon . $cartProduct->qty * $price .'<br>';

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
        $setting = Setting::first();

        MailHelper::setMailConfig();

        $template=EmailTemplate::where('id',6)->first();
        $subject=$template->subject;
        $message=$template->description;
        $message = str_replace('{{user_name}}',$user->name,$message);
        $message = str_replace('{{total_amount}}',$setting->currency_icon.$total_price,$message);
        $message = str_replace('{{payment_method}}',$payment_method,$message);
        $message = str_replace('{{payment_status}}',$payment_status,$message);
        $message = str_replace('{{order_status}}','Pending',$message);
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

}
