<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\City;
use App\Models\Vendor;
use App\Models\VendorSocialLink;
use App\Models\SellerWithdraw;
use App\Models\SellerMailLog;
use App\Models\OrderProduct;
use App\Models\Setting;
use App\Models\BannerImage;
use Auth;
use Image;
use File;
use Str;
use Hash;
class SellerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(){
        $user = Auth::guard('api')->user();

        $seller = Vendor::with('user','socialLinks','products')->where('user_id', $user->id)->first();
        $countries = Country::orderBy('name','asc')->where('status',1)->get();
        $states = CountryState::orderBy('name','asc')->where(['status' => 1, 'country_id' => $user->country_id])->get();
        $cities = City::orderBy('name','asc')->where(['status' => 1, 'country_state_id' => $user->state_id])->get();

        $totalWithdraw = SellerWithdraw::where('seller_id',$seller->id)->where('status',1)->sum('total_amount');
        $totalPendingWithdraw = SellerWithdraw::where('seller_id',$seller->id)->where('status',0)->sum('withdraw_amount');

        $totalAmount = 0;
        $totalSoldProduct = 0;
        $orderProducts = OrderProduct::with('order')->where('seller_id', $seller->id)->get();
        foreach($orderProducts as $orderProduct){
            if($orderProduct->order->payment_status == 1 && $orderProduct->order->order_status == 3){
                $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                $totalAmount = $totalAmount + $price;
                $totalSoldProduct = $totalSoldProduct + $orderProduct->qty;
            }
        }

        $defaultProfile = BannerImage::whereId('15')->first();
        $setting = Setting::first();


        return response()->json(['user' => $user, 'countries' => $countries, 'states' => $states, 'cities' => $cities, 'seller' => $seller, 'totalWithdraw' => $totalWithdraw, 'totalAmount' => $totalAmount, 'totalPendingWithdraw' => $totalPendingWithdraw, 'totalSoldProduct' => $totalSoldProduct, 'setting' => $setting, 'defaultProfile' => $defaultProfile]);
    }

    public function changePassword(){
        $user = Auth::guard('api')->user();
        $setting = Setting::first();
        return view('seller.change_password', compact('user','setting'));
    }

    public function stateByCountry($id){
        $states = CountryState::where(['status' => 1, 'country_id' => $id])->get();
        return response()->json(['states'=>$states]);
    }

    public function cityByState($id){
        $cities = City::where(['status' => 1, 'country_state_id' => $id])->get();
        return response()->json(['cities'=>$cities]);
    }

    public function updateSellerProfile(Request $request){
        $user = Auth::guard('api')->user();
        $rules = [
            'name'=>'required',
            'email'=>'required|unique:users,email,'.$user->id,
            'phone'=>'required',
            'country'=>'required',
            'zip_code'=>'required',
            'address'=>'required',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'email.required' => trans('Email is required'),
            'email.unique' => trans('Email already exist'),
            'phone.required' => trans('Phone is required'),
            'country.required' => trans('Country is required'),
            'zip_code.required' => trans('Zip code is required'),
            'address.required' => trans('Address is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->country_id = $request->country;
        $user->state_id = $request->state;
        $user->city_id = $request->city;
        $user->zip_code = $request->zip_code;
        $user->address = $request->address;
        $user->save();

        if($request->file('image')){
            $old_image=$user->image;
            $user_image=$request->image;
            $extention=$user_image->getClientOriginalExtension();
            $image_name= Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name='uploads/custom-images/'.$image_name;

            Image::make($user_image)
                ->save(public_path().'/'.$image_name);

            $user->image=$image_name;
            $user->save();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }

        $notification= trans('Update Successfully');
        return response()->json(['notification' => $notification]);
    }

    public function updatePassword(Request $request){
        $user = Auth::guard('api')->user();
        $rules = [
            'password'=>'required|min:4|confirmed',
        ];

        $customMessages = [
            'password.required' => trans('Password is required'),
            'password.min' => trans('Password must be 4 characters'),
            'password.confirmed' => trans('Confirm password does not match'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user->password = Hash::make($request->password);
        $user->save();
        $notification= trans('Password Change Successfully');
        return response()->json(['notification' => $notification]);
    }

    public function myShop(){
        $user = Auth::guard('api')->user();
        $seller = Vendor::with('socialLinks')->where('user_id',$user->id)->first();

        return response()->json(['seller' => $seller]);
    }

    public function updateSellerSop(Request $request){

        $user = Auth::guard('api')->user();
        $seller = Vendor::where('user_id',$user->id)->first();
        $rules = [
            'shop_name'=>'required|unique:vendors,email,'.$seller->id,
            'email'=>'required|unique:vendors,email,'.$seller->id,
            'phone'=>'required',
            'opens_at'=>'required',
            'closed_at'=>'required',
            'address'=>'required',
            'greeting_msg'=>'required',
        ];
        $customMessages = [
            'shop_name.required' => trans('Shop name is required'),
            'shop_name.unique' => trans('Shop anme is required'),
            'email.required' => trans('Email is required'),
            'email.unique' => trans('Email already exist'),
            'phone.required' => trans('Phone is required'),
            'greeting_msg.required' => trans('Greeting Messsage is required'),
            'opens_at.required' => trans('Opens at is required'),
            'closed_at.required' => trans('Close at is required'),
            'address.required' => trans('Address is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $seller->phone = $request->phone;
        $seller->open_at = $request->opens_at;
        $seller->closed_at = $request->closed_at;
        $seller->address = $request->address;
        $seller->greeting_msg = $request->greeting_msg;
        $seller->seo_title = $request->seo_title ? $request->seo_title : $request->shop_name;
        $seller->seo_description = $request->seo_description ? $request->seo_description : $request->shop_name;
        $seller->save();

        if($request->logo){
            $exist_banner = $seller->logo;
            $extention = $request->logo->getClientOriginalExtension();
            $banner_name = 'seller-banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->logo)
                ->save(public_path().'/'.$banner_name);
            $seller->logo = $banner_name;
            $seller->save();
            if($exist_banner){
                if(File::exists(public_path().'/'.$exist_banner))unlink(public_path().'/'.$exist_banner);
            }
        }

        if($request->banner_image){
            $exist_banner = $seller->banner_image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'seller-banner'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $seller->banner_image = $banner_name;
            $seller->save();
            if($exist_banner){
                if(File::exists(public_path().'/'.$exist_banner))unlink(public_path().'/'.$exist_banner);
            }
        }

        if(count($request->links) > 0){
            $socialLinks = $seller->socialLinks;
            foreach($socialLinks as $link){
                $link->delete();
            }
            foreach($request->links as $index=> $link){
                if($request->links[$index] != null && $request->icons[$index] != null){
                    $socialLink = new VendorSocialLink();
                    $socialLink->vendor_id = $seller->id;
                    $socialLink->icon=$request->icons[$index];
                    $socialLink->link=$request->links[$index];
                    $socialLink->save();
                }
            }
        }

        $notification= trans('Update Successfully');
        return response()->json(['notification' => $notification]);
    }

    public function removeSellerSocialLink($id){
        $socialLink = VendorSocialLink::find($id);
        $socialLink->delete();
        return response()->json(['success' => trans('Delete Successfully')]);
    }

    public function emailHistory(){
        $user = Auth::guard('api')->user();
        $seller = $user->seller;
        $emails = SellerMailLog::where('seller_id',$seller->id)->orderBy('id','desc')->get();

        return response()->json(['emails' => $emails, 'user' => $user]);

    }
}
