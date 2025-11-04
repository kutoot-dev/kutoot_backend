<?php

namespace App\Http\Controllers\Deliveryman;

use Auth;
use File;
use Hash;
use Image;
use App\Models\Order;
use App\Models\Setting;
use App\Models\DeliveryMan;
use App\Models\OrderAmount;
use Illuminate\Http\Request;
use App\Models\DeliveryManWithdraw;
use App\Http\Controllers\Controller;

class DeliveryManProfileController extends Controller
{
    public function index(){
        $id=Auth::guard('deliveryman-api')->user()->id;
        $deliveryman=DeliveryMan::findOrFail($id);
        $setting = Setting::first();
        $completeOrder=Order::where('delivery_man_id', $id)->where('order_status', '=', 3)->get();
        $runingOrder=Order::where('delivery_man_id', $id)->where('order_status', '!=', 3)->where('order_status', '!=', 4)->get();
        $tota_earn=Order::where('delivery_man_id', $id)->where('order_status', '=', 3)->sum('shipping_cost');
        $deliveryManWithdraw=DeliveryManWithdraw::where('delivery_man_id', $id)->where('status', 1)->sum('total_amount');
        
        $order_total_amount=Order::where('delivery_man_id', $id)->where('order_status', '=', 3)->where('cash_on_delivery', '=', 1)->sum('total_amount');
        
        $given_amount=OrderAmount::where('delivery_man_id', $id)->sum('total_amount');
        
        $current_product_amount= $order_total_amount-$given_amount;
        
        return response()->json([
            'deliveryman' => $deliveryman,
            'setting' => $setting,
            'completeOrder' => $completeOrder->count(),
            'runingOrder' => $runingOrder->count(),
            'tota_earn' => $tota_earn,
            'deliveryManWithdraw' => $deliveryManWithdraw,
            'current_product_amount' => $current_product_amount,
        ]);
    }
    public function edit(){
        $id=Auth::guard('deliveryman-api')->user()->id;
        $deliveryman=DeliveryMan::findOrFail($id);
        return response()->json($deliveryman);
    }
    public function update(Request $request){
        $id=Auth::guard('deliveryman-api')->user()->id;
        $rules = [
            'fname'=>'required',
            'lname'=>'required',
            'email'=>'required|email|unique:delivery_men,email,'.$id,
            'man_type'=>'required',
            'idn_type'=>'required',
            'idn_num'=>'required',
            'phone'=>'required',
        ];
        $customMessages = [
            'man_image.required' => trans('Delivery man image is required'),
            'fname.required' => trans('First name is required'),
            'lname.required' => trans('Last name is required'),
            'email.required' => trans('Email is required'),
            'email.email' => trans('Email must email type'),
            'email.unique' => trans('Email already exist'),
            'man_type.required' => trans('Delivery man type is required'),
            'idn_type.required' => trans('Identity type is required'),
            'idn_num.required' => trans('Identity number is required'),
            'idn_image.required' => trans('Identity image is required'),
            'phone.required' => trans('Phone is required'),
        ];
        $this->validate($request, $rules,$customMessages);
        $man = DeliveryMan::findOrFail($id);
        $man_old_image=$man->man_image;
        $idn_old_image=$man->idn_image;
        if($request->man_image){
            $man_extention=$request->man_image->getClientOriginalExtension();
            $man_image_name = 'man-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$man_extention;
            $man_image_name ='uploads/custom-images/'.$man_image_name;
            Image::make($request->man_image)->save(public_path().'/'.$man_image_name);
            $man->man_image = $man_image_name;
            if($man_old_image){
                if(File::exists(public_path().'/'.$man_old_image))unlink(public_path().'/'.$man_old_image);
            }
        }
        $man->fname=$request->fname;
        $man->lname=$request->lname;
        $man->email=$request->email;
        $man->man_type=$request->man_type;
        $man->idn_type=$request->idn_type;
        $man->idn_num=$request->idn_num;
        if($request->idn_image){
            $idn_extention=$request->idn_image->getClientOriginalExtension();
            $idn_image_name = 'identity-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$idn_extention;
            $idn_image_name ='uploads/custom-images/'.$idn_image_name;
            Image::make($request->idn_image)->save(public_path().'/'.$idn_image_name);
            $man->idn_image = $idn_image_name;
            if($idn_old_image){
                if(File::exists(public_path().'/'.$idn_old_image))unlink(public_path().'/'.$idn_old_image);
            }
        }
        $man->phone=$request->phone;
        $man->save();
        $notification= trans('admin_validation.Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return response()->json(['message' => $notification],200);
    }

    public function updatePassword(Request $request){
        $id=Auth::guard('deliveryman-api')->user()->id;
        $rules = [
            'password'=>'required|min:4',
            'c_password' =>'required|same:password',
        ];
        $customMessages = [
            'password.required' => trans('Password is required'),
            'password.min' => trans('Password minimum 4 character'),
            'c_password.required' => trans('Confirm password is required'),
            'c_password.same' => trans('Confirm password do not match'),
        ];
        $this->validate($request, $rules,$customMessages);
        $man = DeliveryMan::findOrFail($id);
        $man->password=Hash::make($request->password);
        $man->save();
        $notification= trans('admin_validation.Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return response()->json(['message' => $notification],200);
    }
}
