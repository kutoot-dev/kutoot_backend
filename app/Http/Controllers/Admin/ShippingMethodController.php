<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipping;
use App\Models\City;
use App\Models\Setting;
class ShippingMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $shippings = Shipping::with('city')->orderBy('id','asc')->get();

        return response()->json(['shippings' => $shippings], 200);
    }

    public function create(){
        $cities = City::where('status',1)->orderBy('name','asc')->get();
        return response()->json(['cities' => $cities], 200);
    }

    public function store(Request $request){
        $rules = [
            'city_id' => 'required',
            'shipping_rule' => 'required',
            'type' => 'required',
            'shipping_fee' => 'required|numeric',
            'condition_from' => 'required|numeric',
            'condition_to' => 'required|numeric',
        ];
        $customMessages = [
            'city_id.required' => trans('City is required'),
            'shipping_rule.required' => trans('Shipping rule is required'),
            'type.required' => trans('Type is required'),
            'shipping_fee.required' => trans('Shipping fee is required'),
            'condition_from.required' => trans('Condition from is required'),
            'condition_to.required' => trans('Condition to is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $shipping = new Shipping();
        $shipping->city_id = $request->city_id;
        $shipping->shipping_rule = $request->shipping_rule;
        $shipping->type = $request->type;
        $shipping->shipping_fee = $request->shipping_fee;
        $shipping->condition_from = $request->condition_from;
        $shipping->condition_to = $request->condition_to;
        $shipping->save();

        $notification=trans('Created Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function show($id){
        $shipping = Shipping::with('city')->find($id);
        return response()->json(['shipping' => $shipping], 200);
    }

    public function cityWiseShipping($city_id){
        $shippings = Shipping::with('city')->where('city_id', $city_id)->get();
        return response()->json(['shippings' => $shippings], 200);
    }





    public function update(Request $request, $id){

        $rules = [
            'shipping_rule' => 'required',
            'type' => 'required',
            'shipping_fee' => 'required|numeric',
            'condition_from' => 'required|numeric',
            'condition_to' => 'required|numeric',
        ];
        $customMessages = [
            'shipping_rule.required' => trans('Shipping rule is required'),
            'type.required' => trans('Type is required'),
            'shipping_fee.required' => trans('Shipping fee is required'),
            'condition_from.required' => trans('Condition from is required'),
            'condition_to.required' => trans('Condition to is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $shipping = Shipping::find($id);
        $shipping->shipping_rule = $request->shipping_rule;
        $shipping->type = $request->type;
        $shipping->shipping_fee = $request->shipping_fee;
        $shipping->condition_from = $request->condition_from;
        $shipping->condition_to = $request->condition_to;
        $shipping->save();

        $notification=trans('Update Successfully');
        return response()->json(['notification' => $notification], 200);

    }

    public function destroy($id){
        $shipping = Shipping::find($id);
        $shipping->delete();
        $notification=trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }
}
