<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BreadcrumbImage;
use Auth;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\City;
use App\Models\Address;
use App\Models\Vendor;
use App\Models\Setting;
use App\Models\Wishlist;
use App\Models\StripePayment;
use App\Models\RazorpayPayment;
use App\Models\Flutterwave;
use App\Models\PaystackAndMollie;
use App\Models\BankPayment;
use App\Models\InstamojoPayment;
use App\Models\PaypalPayment;
use App\Models\SslcommerzPayment;
use App\Models\ShoppingCart;
use App\Models\Coupon;
use App\Models\Shipping;
use App\Models\MyfatoorahPayment;
use Cart;
use Session;
use App\Models\PurchasedCoins;
use App\Models\UserCoupons;
use App\Models\UserCoins;
use Illuminate\Support\Str;
use App\Models\CoinCampaigns;
use App\Models\Baseplans;
use App\Models\Winners;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Razorpay\Api\Api;
use App\Models\BaseplanCampaignLinked;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function mycoupons(Request $request)
    {
        $query = UserCoupons::with('purchasedCampaign');

        $user =  Auth::guard('api')->user();

        if ($user) {
            $userId = $user->id;
            $query->whereHas('purchasedCampaign', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        $query = $query->where('status', 1);
        // Filter by purchased_camp_id if provided
        if ($request->has('purchased_camp_id') && $request->purchased_camp_id) {
            $query->where('purchased_camp_id', $request->purchased_camp_id);
        }

        $coupons = $query->orderBy('id', 'desc')->paginate(15);

        // Transform output
        $data = $coupons->getCollection()->map(function ($coupon) {
            return [
                'id' => $coupon->id,
                'coupon_code' => $coupon->coupon_code,
                'coupon_expires' => $coupon->coupon_expires,
                'coins' => $coupon->coins,
                'is_claimed' => $coupon->is_claimed,
                'status' => $coupon->status,
                'main_campaign_id' => $coupon->main_campaign_id,
                'series_label' => $coupon->series_label,
                'purchased_camp_id' => $coupon->purchased_camp_id,
                'purchased_camp_details' => $coupon->purchasedCampaign,
                'created_at' => $coupon->created_at,
                'camp_title' => $coupon->purchasedCampaign->camp_title ?? null,
                'user_id' => $coupon->purchasedCampaign->user_id ?? null,
            ];
        });

        $coupons->setCollection($data);

        return response()->json([
            'status' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $coupons->currentPage(),
                'last_page' => $coupons->lastPage(),
                'per_page' => $coupons->perPage(),
                'total' => $coupons->total(),
                'next_page_url' => $coupons->nextPageUrl(),
                'prev_page_url' => $coupons->previousPageUrl(),
            ]
        ]);
    }


    public function mystatements(Request $request)
    {
        $query = UserCoins::with('purchasedCampaign');

        $user =  Auth::guard('api')->user();

        if ($user) {
            $userId = $user->id;
            $query->where('user_id', $userId);
        }

        $coins = $query->orderBy('id', 'desc')->paginate(15);


        $data = $coins->map(function ($coin) {
            return [
                'id' => $coin->id,
                'coins' => $coin->coins,
                'coin_expires' => $coin->coin_expires,
                'type' => $coin->type,
                'is_claimed' => $coin->is_claimed,
                'status' => $coin->status,
                'created_at' => $coin->created_at,
                'updated_at' => $coin->updated_at,
                'camp_title' => $coin->purchasedCampaign->camp_title ?? null,
                'user_id' => $coin->purchasedCampaign->user_id ?? null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function purchasestore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'camp_id' => 'required',
                'base_plan_id' => 'required',
                'amount' => 'required|numeric',
                'quantity' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                throw new HttpResponseException(response()->json([
                    'errors' => $validator->errors()
                ], 422));
            }

            if($request->quantity > 5){
                return response()->json(['message' => 'Qty is more!'],400);
            }

            $baseplan = Baseplans::find($request->base_plan_id);
            if (!$baseplan) {
                return response()->json(['message' => 'Base plan not found!'], 404);
            }

            $user =  Auth::guard('api')->user();
            $campaign = CoinCampaigns::find($request->camp_id);
            if (!$campaign) {
                return response()->json(['message' => 'Campaign not found!'], 404);
            }
           
            $purchase = PurchasedCoins::create([
                'camp_id' => $request->camp_id,
                'user_id' => $user->id,
                'base_plan_id' => $baseplan->id,
                'quantity' => $request->quantity,
                'camp_title' => $campaign->title,
                'camp_description' => $campaign->description,
                'camp_ticket_price' => $baseplan->ticket_price,
                'camp_coins_per_campaign' => $baseplan->coins_per_campaign,
                'camp_coupons_per_campaign' => $baseplan->coupons_per_campaign,
                'status' => 1,
                'payment_status' => 'Pending'
            ]);

            //# total_tickets value should be divided by series prefix value  to get single series total tickets
            $totaltickets = $campaign->total_tickets;
            $single_length_tickets = $campaign->total_tickets;
            $prefix = null;
            $seriesLabels = []; // Array to hold series A, B, C...
            
            if($campaign->series_prefix){
                $prefix = strtoupper($campaign->series_prefix);
                $firstChar = $prefix[0];
                if (ctype_alpha($firstChar)) {
                    // Calculate number of series (e.g., 'D' = 4 series: A, B, C, D)
                    $numberOfSeries = ord($firstChar) - 64; // 'A'=1, 'B'=2, 'C'=3, 'D'=4, etc.
                    
                    // Calculate tickets per series
                    $single_length_tickets = round($campaign->total_tickets / $numberOfSeries);
                    
                    // Generate series labels from A to the prefix letter
                    for ($i = 0; $i < $numberOfSeries; $i++) {
                        $seriesLabels[] = chr(65 + $i); // 65 is ASCII for 'A'
                    }
                }
            }
            
            $alreadyPurchasedCouponsCount = PurchasedCoins::where('camp_id', $request->camp_id)
                ->where('payment_status', 'success')
                ->sum(\DB::raw('camp_coupons_per_campaign * quantity'));

            if (($alreadyPurchasedCouponsCount + ($baseplan->coupons_per_campaign * $request->quantity)) > $totaltickets) {
                return response()->json(['message' => 'Not enough tickets available!'], 400);
            }
            
            $couponslist = [];
            $generatedCodes = []; // Track codes generated in this session
            
            for ($j = 0; $j < $baseplan->coupons_per_campaign * $purchase->quantity; $j++) {

                // Determine current series label based on already purchased count
                $currentSeriesLabel = null;
                if (!empty($seriesLabels)) {
                    $totalExistingForCampaign = UserCoupons::where('main_campaign_id', $campaign->id)->count();
                    $seriesIndex = floor($totalExistingForCampaign / $single_length_tickets);
                    $currentSeriesLabel = $seriesLabels[$seriesIndex] ?? end($seriesLabels);
                } else {
                    $currentSeriesLabel = $prefix;
                }

                // Generate a code as a combination of N pairs of two-digit numbers (01-49)
                do {
                    $numbers = [];
                    // Generate unique numbers between number_min and number_max, formatted as two digits
                    while (count($numbers) < $campaign->numbers_per_ticket) {
                        $num = rand($campaign->number_min, $campaign->number_max);
                        $numStr = str_pad($num, 2, '0', STR_PAD_LEFT); // ensures '01', '02', ..., '49'
                        if (!in_array($numStr, $numbers)) {
                            $numbers[] = $numStr;
                        }
                    }
                    // Join the numbers to form the code
                    $code = implode('', $numbers);

                    // Check both database and current session for same campaign and series
                    $existsInDb = UserCoupons::where('coupon_code', $code)
                        ->where('main_campaign_id', $campaign->id)
                        ->where('series_label', $currentSeriesLabel)
                        ->exists();
                    $existsInSession = in_array($code . '_' . $currentSeriesLabel, $generatedCodes);
                    
                } while ($existsInDb || $existsInSession);

                // Add to session tracker with series label
                $generatedCodes[] = $code . '_' . $currentSeriesLabel;

                $newone = UserCoupons::create([
                    'purchased_camp_id' => $purchase->id,
                    'coupon_code' => $code,
                    'coupon_expires' => now()->addDays(30),
                    'is_claimed' => 0,
                    'status' => 0,
                    'main_campaign_id'=>$campaign->id,
                    'series_label'=>$currentSeriesLabel
                ]);

                array_push($couponslist, $newone);
            }

            $razorpay = RazorpayPayment::first();
            if (!$razorpay) {
                return response()->json(['message' => 'Razorpay configuration not found!'], 500);
            }

            $total_price = $baseplan->ticket_price;
            $payable_amount = $total_price * 1;
            $payable_amount = round($payable_amount, 2);
            
            $api = new Api($razorpay->key, $razorpay->secret_key);
            $order = $api->order->create(
                array('receipt' => "OID".$purchase->id, 'amount' => ($payable_amount * 100), 'currency' => 'INR')
            );

            $purchase->razor_order_id = $order->id;
            $purchase->razor_key = $razorpay->key;
            $purchase->camp_ticket_price = $payable_amount;
            $purchase->save();

            if($baseplan->coupons_per_campaign*$request->quantity > 5000){
                return response()->json(['message' => 'Coupons count is more!'],400);
            }

            $purchase = PurchasedCoins::with('coupons')->where('id',$purchase->id)->where('user_id' , $user->id)->first();
            $purchase['series-prefix'] = $campaign->series_prefix;
            $purchase['number_min'] = $campaign->number_min;
            $purchase['number_max'] = $campaign->number_max;
            $purchase['numbers_per_ticket'] = $campaign->numbers_per_ticket;
            $purchase['basedetails']=$purchase->basedetails;
            
            return response()->json(['message' => 'Campaign purchased successfully', 'data' => $purchase]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during purchase',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function singlecoderegenerate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'coupon_id' => 'required',
            'coupon_code' => 'required'
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'errors' => $validator->errors()
            ], 422));
        }
        
        $user =  Auth::guard('api')->user();
        $purchase = PurchasedCoins::find($request->order_id);
        
        $referenceexists = UserCoupons::where('id', $request->coupon_id)->first();

        // do {
        //     // Generate a 12-digit random numeric string
        //     $code = '';
        //     for ($i = 0; $i < 12; $i++) {
        //         $code .= rand(0, 9);
        //     }

        //     $exists = UserCoupons::where('coupon_code', $code)->exists();
        // } while ($exists);

        $exists = UserCoupons::where('coupon_code', $request->coupon_code)->exists();

        if($exists){
            return response()->json(['message' => 'Coupon code exists Already!'],400);
        }

        $referenceexists->coupon_code=$request->coupon_code;
        $referenceexists->save();

        return response()->json(['message' => 'Campaign purchased successfully', 'data' => $referenceexists]);
    }

    public function payment_status(Request $request)
{
    $validator = Validator::make($request->all(), [
        'razorpay_order_id' => 'required|string',
        'payment_id' => 'required|string',
        'payment_status' => 'required|string',
        'razorpay_signature' => 'required|string',
    ]);

    if ($validator->fails()) {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }

    $user = Auth::guard('api')->user();

    $purchase = PurchasedCoins::with('coupons')
        ->where('razor_order_id', $request->razorpay_order_id)
        ->where('user_id', $user->id)
        ->first();

    if (!$purchase) {
        throw new HttpResponseException(response()->json([
            'errors' => 'Invalid Order Id'
        ], 422));
    }

    if (in_array($purchase->payment_status, ['success', 'failed'])) {
        throw new HttpResponseException(response()->json([
            'errors' => 'Status Already Updated'
        ], 422));
    }

    // ✅ Update payment details
    $purchase->payment_id = $request->payment_id;
    $purchase->payment_status = $request->payment_status;
    $purchase->razorpay_signature = $request->razorpay_signature;
    $purchase->save();

    $baseplan = Baseplans::find($purchase->base_plan_id);

    if ($baseplan->coupons_per_campaign * $purchase->quantity > 50) {
        return response()->json(['message' => 'Coupons count is more!'], 400);
    }

    $couponslist = [];
    // for ($j = 0; $j < $baseplan->coupons_per_campaign * $purchase->quantity; $j++) {
    //     do {
    //         $code = '';
    //         for ($i = 0; $i < 12; $i++) {
    //             $code .= rand(0, 9);
    //         }
    //         $exists = UserCoupons::where('coupon_code', $code)->exists();
    //     } while ($exists);

    //     $newCoupon = UserCoupons::create([
    //         'purchased_camp_id' => $purchase->id,
    //         'coupon_code' => $code,
    //         'coupon_expires' => now()->addDays(30),
    //         'is_claimed' => 0,
    //         'status' => 1,
    //     ]);

    //     $couponslist[] = $newCoupon;
    // }

    // Update all coupons status to 1 for this purchase
    UserCoupons::where('purchased_camp_id', $purchase->id)->update(['status' => 1]);

    $coinsdata = UserCoins::create([
        'purchased_camp_id' => $purchase->id,
        'user_id' => $user->id,
        'coin_expires' => now()->addDays(30),
        'coins' => $baseplan->coins_per_campaign * $purchase->quantity,
        'type' => 'credit',
        'status' => 1,
    ]);

    $purchase = PurchasedCoins::with('coupons')
        ->where('razor_order_id', $request->razorpay_order_id)
        ->where('user_id', $user->id)
        ->first();

    $purchase['coinsdata'] = $coinsdata;

    return response()->json([
        'data' => $purchase,
        'message' => 'Payment Updated Successfully',
    ]);
}


    public function myPurchases()
    {
        $user =  Auth::guard('api')->user();
        $purchases = PurchasedCoins::where('user_id', $user->id)->get();

        return response()->json($purchases);
    }

    public function checkwinnerclaim(Request $request)
    {
    
        $data = Winners::with('campaign')->where('coupon_number', $request->coupon_code)->where('is_claimed',0)->first();
        if($data){
           return response()->json(['data'=>$data,'message'=>'Congratulations for winning prize!!']); 
        }

        return response()->json(['data'=>'','message'=>'No eligible']);
        
    }

    public function Purchasedetails($id)
    {
        $user =  Auth::guard('api')->user();
        $purchases = PurchasedCoins::with('coupons')->where('id',$id)->where('user_id',$user->id)->first();
        $campaign = CoinCampaigns::find($purchases->camp_id);
        $purchases['series-prefix'] = $campaign->series_prefix; 
        $purchases['number_min'] =$campaign->number_min;
        $purchases['number_max']=$campaign->number_max;
        $purchases['numbers_per_ticket']=$campaign->numbers_per_ticket;
        return response()->json(["data"=> $purchases]);
    }


    public function checkout(Request $request){
        $user = Auth::guard('api')->user();
        $cartProducts = ShoppingCart::with('product','variants.variantItem')->where('user_id', $user->id)->select('id','product_id','qty')->get();

        if($cartProducts->count() == 0){
            $notification = trans('user_validation.Your shopping cart is empty');
            return response()->json(['message' => $notification],403);
        }

        $addresses = Address::with('country','countryState','city')->where(['user_id' => $user->id])->get();
        $shippings = Shipping::all();

        $couponOffer = '';
        if($request->coupon){
            $coupon = Coupon::where(['code' => $request->coupon, 'status' => 1])->first();
            if($coupon){
                if($coupon->expired_date >= date('Y-m-d')){
                    if($coupon->apply_qty <  $coupon->max_quantity ){
                        $couponOffer = $coupon;
                    }
                }
            }
        }


        $coins = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $user->id)
            ->whereDate('coin_expires', '>=', now()->toDateString())
            ->first();

        $creditCoins = $coins->credit ?? 0;
        $debitCoins = $coins->debit ?? 0;
        $balanceCoins = $creditCoins - $debitCoins;


        $total_redeemable_coins = 0;

        foreach ($cartProducts as $cart) {
            $reedem_percentage = $cart->product->reedem_percentage ?? 0;
            $redeemable_for_product = ($reedem_percentage / 100) * $balanceCoins;
            
            // Optional: multiply by quantity if needed
            $redeemable_for_product *= $cart->qty;

            $total_redeemable_coins += $redeemable_for_product;
        }

        // $reedem_coins_allowed=100;

        $redeemcoupon = array("reedem_coins_allowed"=>$total_redeemable_coins,"balance_coins"=>$balanceCoins,"message"=>"Congratulations, You Can Redeem Coins on this purchase");



        $stripePaymentInfo = StripePayment::first();
        $razorpayPaymentInfo = RazorpayPayment::first();
        $flutterwavePaymentInfo = Flutterwave::first();
        $paypalPaymentInfo = PaypalPayment::first();
        $bankPaymentInfo = BankPayment::first();

        $paystackAndMollie = PaystackAndMollie::first();
        $instamojo = InstamojoPayment::first();
        $sslcommerz = SslcommerzPayment::first();
        $myfatoorah = MyfatoorahPayment::first();


        return response()->json([
            'cartProducts' => $cartProducts,
            "redeemcoin_coupon" => $redeemcoupon,
            'addresses' => $addresses,
            'shippings' => $shippings,
            'couponOffer' => $couponOffer,
            'stripePaymentInfo' => $stripePaymentInfo,
            'razorpayPaymentInfo' => $razorpayPaymentInfo,
            'flutterwavePaymentInfo' => $flutterwavePaymentInfo,
            'paypalPaymentInfo' => $paypalPaymentInfo,
            'bankPaymentInfo' => $bankPaymentInfo,
            'paystackAndMollie' => $paystackAndMollie,
            'instamojo' => $instamojo,
            'sslcommerz' => $sslcommerz,
            'myfatoorah' => $myfatoorah,
        ],200);

    }




    public function generateCoupons(Request $request)
{
    $validator = Validator::make($request->all(), [
        'camp_id' => 'required',
        'base_plan_id' => 'required',
        'quantity' => 'required|numeric'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    if ($request->quantity > 5) {
        return response()->json(['message' => 'Qty is more!'], 400);
    }

    $baseplan = Baseplans::findOrFail($request->base_plan_id);
    $campaign = CoinCampaigns::findOrFail($request->camp_id);
    $user = Auth::guard('api')->user();

    if ($baseplan->coupons_per_campaign * $request->quantity > 50) {
        return response()->json(['message' => 'Coupons count is more!'], 400);
    }

    // Generate coupons only (no saving purchase)
    $couponsList = [];

    for ($j = 0; $j < $baseplan->coupons_per_campaign * $request->quantity; $j++) {
        do {
            $code = '';
            for ($i = 0; $i < 12; $i++) {
                $code .= rand(0, 9);
            }
            $exists = UserCoupons::where('coupon_code', $code)->exists();
        } while ($exists);

        $newCoupon = [
            'coupon_code' => $code,
            'coupon_expires' => now()->addDays(30)->toDateTimeString(),
            'is_claimed' => 0,
            'status' => 0,
        ];

        $couponsList[] = $newCoupon;
    }

    return response()->json([
        'message' => 'Coupons generated successfully',
        'coupons' => $couponsList
    ]);
}

}
