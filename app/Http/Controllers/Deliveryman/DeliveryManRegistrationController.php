<?php

namespace App\Http\Controllers\Deliveryman;

use File;
use Image;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class DeliveryManRegistrationController extends Controller
{
    public function registration(Request $request){
        $rules = [
            'man_image'=>'required',
            'fname'=>'required',
            'lname'=>'required',
            'email'=>'required|email|unique:delivery_men,email',
            'man_type'=>'required',
            'idn_type'=>'required',
            'idn_num'=>'required',
            'idn_image'=>'required',
            'phone'=>'required',
            'password'=>'required|min:4',
            'c_password' => 'required|same:password',
        ];
        $customMessages = [
            'man_image.required' => trans('Delivery man image is required'),
            'fname.required' => trans('First name is required'),
            'lname.required' => trans('Last name is required'),
            'email.required' => trans('Email is required'),
            'email.email' => trans('Must be email'),
            'email.unique' => trans('Email already exist'),
            'man_type.required' => trans('Delivery man type is required'),
            'idn_type.required' => trans('Identity type is required'),
            'idn_num.required' => trans('Identity number is required'),
            'idn_image.required' => trans('Identity image is required'),
            'phone.required' => trans('Phone is required'),
            'password.required' => trans('Password is required'),
            'password.min' => trans('Password minimum 4 character'),
            'c_password.required' => trans('Confirm password is required'),
            'c_password.same' => trans('Confirm password do not match'),
        ];
        $this->validate($request, $rules,$customMessages);
        $man = new DeliveryMan();
        if($request->man_image){
            $man_extention=$request->man_image->getClientOriginalExtension();
            $man_image_name = 'man-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$man_extention;
            $man_image_name ='uploads/custom-images/'.$man_image_name;
            Image::make($request->man_image)->save(public_path().'/'.$man_image_name);
            $man->man_image = $man_image_name;
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
        }
        $man->phone=$request->phone;
        $man->password=Hash::make($request->password);
        $man->save();
        $notification= trans('Registration Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return response()->json(['message' => $notification],200);
    }
}
