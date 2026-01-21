<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Nnjeim\World\World;
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
        $this->middleware('auth:web');
    }

    public function index(){
        $user = Auth::guard('web')->user();

        $seller = Vendor::with('user','socialLinks','products')->where('user_id', $user->id)->first();
        $countries = World::countries()->data;
        $states = World::states([
            'filters' => [
                'country_id' => $user->country_id,
            ],
        ])->data;
        $cities = World::cities([
            'filters' => [
                'state_id' => $user->state_id,
            ],
        ])->data;
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

        return view('seller.seller_profile', compact('user','countries','states','cities','seller','totalWithdraw','totalAmount','totalPendingWithdraw','totalSoldProduct','setting','defaultProfile'));
    }

    public function changePassword(){

         $user = Auth::guard('web')->user();
        $setting = Setting::first();
        return view('seller.change_password', compact('user','setting'));
    }

    public function stateByCountry($id){
        $states = World::states([
            'filters' => [
                'country_id' => $id,
            ],
        ])->data;
        return response()->json(['states'=>$states]);
    }

    public function cityByState($id){
        $cities = World::cities([
            'filters' => [
                'state_id' => $id,
            ],
        ])->data;
        return response()->json(['cities'=>$cities]);
    }

    public function updateSellerProfile(Request $request){
        $user = Auth::guard('web')->user();
        $rules = [
            'name'=>'required',
            'email'=>'required|unique:users,email,'.$user->id,
            'phone'=>'required',
            'address'=>'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'email.required' => trans('admin_validation.Email is required'),
            'email.unique' => trans('admin_validation.Email already exist'),
            'phone.required' => trans('admin_validation.Phone is required'),
            'country.required' => trans('admin_validation.Country is required'),
            'zip_code.required' => trans('admin_validation.Zip code is required'),
            'address.required' => trans('admin_validation.Address is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user->name = $request->name;
        $user->phone = $request->phone;
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

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updatePassword(Request $request){
        $user = Auth::guard('web')->user();
        $rules = [
            'password'=>'required|min:4|confirmed',
        ];

        $customMessages = [
            'password.required' => trans('admin_validation.Password is required'),
            'password.min' => trans('admin_validation.Password must be 4 characters'),
            'password.confirmed' => trans('admin_validation.Confirm password does not match'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user->password = Hash::make($request->password);
        $user->save();
        $notification= trans('admin_validation.Password Change Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function myShop(){
        $user = Auth::guard('web')->user();
        $seller = Vendor::with('socialLinks')->where('user_id',$user->id)->first();

        return view('seller.shop_profile', compact('user','seller'));
    }

    public function updateSellerSop(Request $request){

        $user = Auth::guard('web')->user();
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
            'shop_name.required' => trans('admin_validation.Shop name is required'),
            'shop_name.unique' => trans('admin_validation.Shop anme is required'),
            'email.required' => trans('admin_validation.Email is required'),
            'email.unique' => trans('admin_validation.Email already exist'),
            'phone.required' => trans('admin_validation.Phone is required'),
            'greeting_msg.required' => trans('admin_validation.Greeting Messsage is required'),
            'opens_at.required' => trans('admin_validation.Opens at is required'),
            'closed_at.required' => trans('admin_validation.Close at is required'),
            'address.required' => trans('admin_validation.Address is required'),
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

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function removeSellerSocialLink($id){
        $socialLink = VendorSocialLink::find($id);
        $socialLink->delete();
        return response()->json(['success' => trans('admin_validation.Delete Successfully')]);
    }

    public function emailHistory(){
        $user = Auth::guard('web')->user();
        $seller = $user->seller;
        $emails = SellerMailLog::where('seller_id',$seller->id)->orderBy('id','desc')->get();

        return response()->json(['emails' => $emails, 'user' => $user]);

    }
}
