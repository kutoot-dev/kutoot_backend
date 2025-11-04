<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\City;
use App\Models\User;
use App\Models\VendorSocialLink;
use App\Models\ProductReview;
use App\Models\Product;
use App\Helpers\MailHelper;
use App\Models\SellerMailLog;
use App\Mail\SendSingleSellerMail;
use App\Mail\ApprovedSellerAccount;
use App\Models\WithdrawMethod;
use App\Models\SellerWithdraw;
use App\Models\OrderProduct;
use App\Models\BannerImage;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Auth;
use Image;
use File;
use Mail;
class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $sellers = Vendor::with('user','products')->orderBy('id','desc')->where('status',1)->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        $products = Product::all();
        $setting = Setting::first();

        return view('admin.seller', compact('sellers','defaultProfile','products','setting'));

    }

    public function pendingSellerList(){
        $sellers = Vendor::with('user','socialLinks','products')->orderBy('id','desc')->where('status',0)->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        $products = Product::all();
        $setting = Setting::first();

        return view('admin.seller', compact('sellers','defaultProfile','products','setting'));
    }

    public function show($id){
        $seller = Vendor::with('user','socialLinks','products')->find($id);
        if($seller){
            $countries = Country::with('countryStates')->orderBy('name','asc')->where('status',1)->get();
            $states = CountryState::with('cities','country')->orderBy('name','asc')->where(['status' => 1, 'country_id' => $seller->user->country_id])->get();
            $cities = City::with('countryState')->orderBy('name','asc')->where(['status' => 1, 'country_state_id' => $seller->user->state_id])->get();
            $user = $seller->user;
            $totalWithdraw = SellerWithdraw::with('seller')->where('seller_id',$seller->id)->where('status',1)->sum('total_amount');
            $totalPendingWithdraw = SellerWithdraw::with('seller')->where('seller_id',$seller->id)->where('status',0)->sum('withdraw_amount');

            $totalAmount = 0;
            $totalSoldProduct = 0;
            $orderProducts = OrderProduct::with('order')->where('seller_id',$id)->get();
            foreach($orderProducts as $orderProduct){
                if($orderProduct->order->payment_status == 1 && $orderProduct->order->order_status == 3){
                    $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                    $totalAmount = $totalAmount + $price;
                    $totalSoldProduct = $totalSoldProduct + $orderProduct->qty;
                }
            }

            $defaultProfile = BannerImage::whereId('15')->select('image','title')->first();
            $setting = Setting::select('currency_icon')->first();

            return view('admin.show_seller',compact('seller','countries','cities','states','user','totalWithdraw','totalAmount','totalSoldProduct','totalPendingWithdraw','defaultProfile','setting'));

        }else{
            $notification = trans('admin_validation.Something went wrong');
            return response()->json(['notification' => $notification], 500);
        }

    }

    public function stateByCountry($id){
        $states = CountryState::where(['status' => 1, 'country_id' => $id])->get();
        $response='<option value="">'.trans('admin_validation.Select a State').'</option>';
        if($states->count() > 0){
            foreach($states as $state){
                $response .= "<option value=".$state->id.">".$state->name."</option>";
            }
        }
        return response()->json(['states'=>$response]);
    }

    public function cityByState($id){
        $cities = City::where(['status' => 1, 'country_state_id' => $id])->get();
        $response='<option value="">'.trans('admin_validation.Select a City').'</option>';
        if($cities->count() > 0){
            foreach($cities as $city){
                $response .= "<option value=".$city->id.">".$city->name."</option>";
            }
        }
        return response()->json(['cities'=>$response]);
    }

    public function updateSeller(Request $request , $id){
        $user = User::find($id);
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

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function sellerShopDetail($id){
        $seller = Vendor::with('user','socialLinks')->where('id', $id)->first();
        $user = $seller->user;
        $setting = Setting::first();
        return view('admin.seller_shop', compact('seller','user','setting'));
    }

    public function removeSellerSocialLink($id){
        $socialLink = VendorSocialLink::find($id);
        $socialLink->delete();
        return response()->json(['success' => 'Delete Successfully']);
    }

    public function destroy($id)
    {
        $seller = Vendor::find($id);
        $banner_image = $seller->banner_image;
        $seller->delete();
        if($banner_image){
            if(File::exists(public_path().'/'.$banner_image))unlink(public_path().'/'.$banner_image);
        }

        SellerMailLog::where('seller_id',$id)->delete();
        SellerWithdraw::where('seller_id',$id)->delete();
        VendorSocialLink::where('vendor_id',$id)->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.seller-list')->with($notification);
    }

    public function changeStatus($id){
        $seller = Vendor::find($id);
        if($seller->status == 1){
            $seller->status = 0;
            $seller->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $seller->status = 1;
            $seller->save();

            $user = User::find($seller->user_id);
            MailHelper::setMailConfig();
            $template = EmailTemplate::where('id',7)->first();
            $subject = $template->subject;
            $message = $template->description;
            $message = str_replace('{{name}}',$user->name,$message);
            Mail::to($user->email)->send(new ApprovedSellerAccount($message,$subject));

            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function updateSellerSop(Request $request, $id){
        $seller = Vendor::find($id);
        $rules = [
            'shop_name'=>'required|unique:vendors,email,'.$seller->id,
            'email'=>'required|unique:vendors,email,'.$seller->id,
            'phone'=>'required',
            'greeting_msg'=>'required',
            'opens_at'=>'required',
            'closed_at'=>'required',
            'address'=>'required',
        ];
        $customMessages = [
            'shop_name.required' => trans('admin_validation.Shop name is required'),
            'shop_name.unique' => trans('admin_validation.Shop anme is required'),
            'email.required' => trans('admin_validation.Email is required'),
            'email.unique' => trans('admin_validation.Email already exist'),
            'phone.required' => trans('admin_validation.Phone is required'),
            'greeting_msg.required' => trans('admin_validation.Greeting Message is required'),
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


        if($request->links){
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
        }


        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function sellerReview($id){

        $seller = Vendor::where('id', $id)->first();
        $user = $seller->user;
        $reviews = ProductReview::with('user','product')->orderBy('id','desc')->where('product_vendor_id',$seller->id)->get();


        return view('admin.seller_product_review', compact('reviews','user','seller'));
    }

    public function sendEmailToSeller($id){
        $seller = Vendor::find($id);
        $user = User::find($seller->user_id);
        $setting = Setting::first();

        return view('admin.send_seller_email', compact('user','setting','seller'));
    }

    public function showSellerReviewDetails($id){
        $review = ProductReview::with('user','product')->find($id);
        $seller = Vendor::where('id', $review->product_vendor_id)->first();


        return view('admin.show_seller_product_review', compact('review','seller'));
    }

    public function sendMailtoSingleSeller(Request $request, $id){
        $rules = [
            'subject'=>'required',
            'message'=>'required'
        ];
        $customMessages = [
            'subject.required' => trans('admin_validation.Subject is required'),
            'message.required' => trans('admin_validation.Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = User::with('seller')->find($id);
        $seller = $user->seller;
        MailHelper::setMailConfig();
        Mail::to($user->email)->send(new SendSingleSellerMail($request->subject,$request->message));
        $sellerMail = new SellerMailLog();
        $sellerMail->seller_id = $seller->id;
        $sellerMail->subject = $request->subject;
        $sellerMail->message = $request->message;
        $sellerMail->save();
        $notification = trans('admin_validation.Email Send Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function emailHistory($id){
        $seller = Vendor::where('id', $id)->first();
        $user = $seller->user;
        $emails = SellerMailLog::where('seller_id',$seller->id)->orderBy('id','desc')->get();

        return view('admin.email_history', compact('emails','user','seller'));
    }

    public function productBySaller($id){
        $user = User::find($id);
        $seller = Vendor::where('user_id', $user->id)->first();
        $products = Product::with('category','brand')->where('vendor_id',$seller->id)->get();
        $setting = Setting::select('currency_icon')->first();

        return view('admin.product_by_seller', compact('products','user','seller','setting'));
    }

    public function sendEmailToAllSeller(){
        $setting = Setting::first();
        return view('admin.send_email_to_all_seller',compact('setting'));
    }


    public function sendMailToAllSeller(Request $request){
        $rules = [
            'subject'=>'required',
            'message'=>'required'
        ];
        $customMessages = [
            'subject.required' => trans('admin_validation.Subject is required'),
            'message.required' => trans('admin_validation.Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $sellers = Vendor::with('user')->where('status',1)->get();
        MailHelper::setMailConfig();
        foreach($sellers as $seller){
            Mail::to($seller->user->email)->send(new SendSingleSellerMail($request->subject,$request->message));
            $sellerMail = new SellerMailLog();
            $sellerMail->seller_id = $seller->id;
            $sellerMail->subject = $request->subject;
            $sellerMail->message = $request->message;
            $sellerMail->save();
        }

        $notification = trans('admin_validation.Email Send Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function sellerWithdrawList($id){
        $seller = Vendor::find($id);
        $user = $seller->user;
        $withdraws = SellerWithdraw::where('seller_id',$id)->get();
        $setting = Setting::select('currency_icon')->first();

        return view('admin.seller_withdraw_list', compact('withdraws','user','setting'));
    }

}
