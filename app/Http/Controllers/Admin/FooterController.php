<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Footer;
use Image;
use File;
class FooterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $footer = Footer::first();
        return response()->json(['footer' => $footer], 200);
    }

    public function update(Request $request, $id){
        $rules = [
            'email' =>'required',
            'phone' =>'required',
            'address' =>'required',
            'copyright' =>'required',
            'first_column' =>'required',
            'second_column' =>'required',
            'third_column' =>'required',
        ];
        $customMessages = [
            'email.required' => trans('Email is required'),
            'phone.required' => trans('Phone is required'),
            'address.required' => trans('Address is required'),
            'copyright.required' => trans('Copyright is required'),
            'first_column.required' => trans('First column title is required'),
            'second_column.required' => trans('Second column title is required'),
            'third_column.required' => trans('Third column title is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $footer = Footer::first();
        $footer->email = $request->email;
        $footer->phone = $request->phone;
        $footer->address = $request->address;
        $footer->copyright = $request->copyright;
        $footer->first_column = $request->first_column;
        $footer->second_column = $request->second_column;
        $footer->third_column = $request->third_column;
        $footer->save();
        if($request->card_image){
            $old_logo=$footer->payment_image;
            $image=$request->card_image;
            $ext=$image->getClientOriginalExtension();
            $logo_name= 'payment-card-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$ext;
            $logo_name='uploads/website-images/'.$logo_name;
            $logo=Image::make($image)
                    ->save(public_path().'/'.$logo_name);
            $footer->payment_image=$logo_name;
            $footer->save();
            if($old_logo){
                if(File::exists(public_path().'/'.$old_logo))unlink(public_path().'/'.$old_logo);
            }
        }


        $notification = trans('Update Successfully');
        return response()->json(['notification' => $notification], 200);

    }
}
