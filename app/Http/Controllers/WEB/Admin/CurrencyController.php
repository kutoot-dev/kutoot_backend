<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flutterwave;
use App\Models\MultiCurrency;
use App\Models\PaypalPayment;
use App\Models\StripePayment;
use App\Models\RazorpayPayment;
use App\Models\InstamojoPayment;
use App\Models\PaystackAndMollie;
use App\Models\SslcommerzPayment;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = MultiCurrency::get();
        return view('admin.currency', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create_currency');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'currency_name'=>'required|unique:multi_currencies',
            'country_code'=>'required|unique:multi_currencies',
            'currency_code'=>'required|unique:multi_currencies',
            'currency_icon'=>'required',
            'currency_rate'=>'required|numeric',
        ];
        $customMessages = [
            'currency_name.required' => trans('admin_validation.Currency name is required'),
            'currency_name.unique' => trans('admin_validation.Currency name already exist'),
            'country_code.required' => trans('admin_validation.Country code is required'),
            'country_code.unique' => trans('admin_validation.Country code already exist'),
            'currency_code.required' => trans('admin_validation.Currency code is required'),
            'currency_code.unique' => trans('admin_validation.Currency code already exist'),
            'currency_icon.required' => trans('admin_validation.Currency icon is required'),
            'currency_icon.unique' => trans('admin_validation.Currency icon already exist'),
            'currency_rate.required' => trans('admin_validation.Currency rate is required'),
            'currency_rate.numeric' => trans('admin_validation.Currency rate must be number'),
        ];

        $this->validate($request, $rules,$customMessages);

        $currency = new MultiCurrency();

        if($request->is_default == 'Yes'){
            DB::table('multi_currencies')->update(['is_default' => 'No']);
        }

        $currency->currency_name = $request->currency_name;
        $currency->country_code = $request->country_code;
        $currency->currency_code = $request->currency_code;
        $currency->currency_icon = $request->currency_icon;
        $currency->currency_rate = $request->currency_rate;
        $currency->is_default = $request->is_default;
        $currency->currency_position = $request->currency_position;
        $currency->status = $request->status;
        $currency->save();

        $notification=trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $multiCurrency = MultiCurrency::findOrFail($id);
        return view('admin.edit_currency', compact('multiCurrency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'currency_name'=>'required|unique:multi_currencies,currency_name,'.$id,
            'country_code'=>'required|unique:multi_currencies,country_code,'.$id,
            'currency_code'=>'required|unique:multi_currencies,currency_code,'.$id,
            'currency_icon'=>'required',
            'currency_rate'=>'required|numeric',
        ];
        $customMessages = [
            'currency_name.required' => trans('admin_validation.Currency name is required'),
            'currency_name.unique' => trans('admin_validation.Currency name already exist'),
            'country_code.required' => trans('admin_validation.Country code is required'),
            'country_code.unique' => trans('admin_validation.Country code already exist'),
            'currency_code.required' => trans('admin_validation.Currency code is required'),
            'currency_code.unique' => trans('admin_validation.Currency code already exist'),
            'currency_icon.required' => trans('admin_validation.Currency icon is required'),
            'currency_icon.unique' => trans('admin_validation.Currency icon already exist'),
            'currency_rate.required' => trans('admin_validation.Currency rate is required'),
            'currency_rate.numeric' => trans('admin_validation.Currency rate must be number'),
        ];

        $this->validate($request, $rules,$customMessages);

        $currency = MultiCurrency::findOrFail($id);

        if($request->is_default == 'Yes'){
            DB::table('multi_currencies')->where('id', '!=', $currency->id)->update(['is_default' => 'No']);
        }

        if($currency->is_default == 'Yes' && $request->is_default == 'No'){
            DB::table('multi_currencies')->where('id', 1)->update(['is_default' => 'Yes']);
        }

        $currency->currency_name = $request->currency_name;
        $currency->country_code = $request->country_code;
        $currency->currency_code = $request->currency_code;
        $currency->currency_icon = $request->currency_icon;
        $currency->currency_rate = $request->currency_rate;
        $currency->is_default = $request->is_default;
        $currency->currency_position = $request->currency_position;
        $currency->status = $request->status;

        $currency->save();

        $notification=trans('admin_validation.Updated Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.currency.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currency = MultiCurrency::find($id);
        $is_flutterwave = Flutterwave::where('currency_id', $id)->first();
        $is_instamojo = InstamojoPayment::where('currency_id', $id)->first();
        $is_paypal = PaypalPayment::where('currency_id', $id)->first();
        $is_paystack = PaystackAndMollie::where('paystack_currency_id', $id)->first();
        $is_mollie = PaystackAndMollie::where('mollie_currency_id', $id)->first();
        $is_razorpay = RazorpayPayment::where('currency_id', $id)->first();
        $is_sslcommerz = SslcommerzPayment::where('currency_id', $id)->first();
        $is_stripe = StripePayment::where('currency_id', $id)->first();

        if($is_flutterwave || $is_instamojo || $is_paypal || $is_paystack || $is_mollie || $is_razorpay || $is_sslcommerz || $is_stripe){
            $notification = trans('admin_validation.You can not delete this currency. Because there are one or more payment method has been created in this currency.');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('admin.currency.index')->with($notification);
        }else{
            $currency->delete();
            $notification = trans('admin_validation.Delete Successfully');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->route('admin.currency.index')->with($notification);
        }

    }
}
