<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use App\Models\CustomPage;
use App\Models\EmailConfiguration;
use App\Models\EmailTemplate;
use App\Models\PopularPost;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationKey;
use App\Models\ProductTax;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Models\ReturnPolicy;
use App\Models\Service;
use App\Models\TermsAndCondition;
use App\Models\User;
use App\Models\Setting;
use App\Models\CookieConsent;
use App\Models\GoogleRecaptcha;
use App\Models\FacebookComment;
use App\Models\TawkChat;
use App\Models\GoogleAnalytic;
use App\Models\CustomPagination;
use App\Models\SocialLoginInformation;
use App\Models\FacebookPixel;
use App\Models\About;
use App\Models\Currency;
use App\Models\AboutUs;
use App\Models\BillingAddress;
use App\Models\Campaign;
use App\Models\City;
use App\Models\ContactPage;
use App\Models\Country;
use App\Models\CountryState;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\FooterLink;
use App\Models\FooterSocialLink;
use App\Models\MegaMenuCategory;
use App\Models\MegaMenuSubCategory;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\OrderProductVariant;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\SellerMailLog;
use App\Models\SellerWithdraw;
use App\Models\Vendor;
use App\Models\VendorSocialLink;
use App\Models\Wishlist;
use App\Models\WithdrawMethod;
use App\Models\CampaignProduct;
use App\Models\ShippingMethod;
use App\Models\ContactMessage;
use App\Models\ShippingAddress;
use App\Models\Slider;
use App\Models\Subscriber;
use App\Models\Admin;
use App\Models\PusherCredentail;
use App\Models\Message;
use App\Models\Address;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartVariant;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\CompareProduct;
use App\Models\FeaturedCategory;
use App\Models\PopularCategory;
use App\Models\Shipping;
use App\Models\MultiCurrency;
use Image;
use File;
use Artisan;
use Validator;
class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function clearDatabase(){
        Address::truncate();
        AboutUs::truncate();
        ShoppingCart::truncate();
        ShoppingCartVariant::truncate();
        Blog::truncate();
        BlogCategory::truncate();
        BlogComment::truncate();
        Brand::truncate();
        FlashSaleProduct::truncate();
        Category::truncate();
        FeaturedCategory::truncate();
        ChildCategory::truncate();
        ContactMessage::truncate();
        ContactPage::truncate();
        Coupon::truncate();
        CompareProduct::truncate();
        CustomPage::truncate();
        Faq::truncate();
        FooterLink::truncate();
        PopularCategory::truncate();
        FooterSocialLink::truncate();
        MegaMenuCategory::truncate();
        MegaMenuSubCategory::truncate();
        Message::truncate();
        Order::truncate();
        OrderAddress::truncate();
        OrderProduct::truncate();
        OrderProductVariant::truncate();
        PopularPost::truncate();
        Product::truncate();
        ProductGallery::truncate();
        ProductReport::truncate();
        ProductReview::truncate();
        ProductSpecification::truncate();
        ProductSpecificationKey::truncate();
        ProductVariant::truncate();
        ProductVariantItem::truncate();
        SellerMailLog::truncate();
        SellerWithdraw::truncate();
        Service::truncate();
        Shipping::truncate();
        Slider::truncate();
        Subscriber::truncate();
        SubCategory::truncate();
        TermsAndCondition::truncate();
        User::truncate();
        Vendor::truncate();
        VendorSocialLink::truncate();
        Wishlist::truncate();
        WithdrawMethod::truncate();

        $setting = Setting::first();
        $setting->seller_condition = '';
        $setting->save();

        // pending ----
        $admins = Admin::where('id', '!=', 1)->get();
        foreach($admins as $admin){
            $admin_image = $admin->image;
            $admin->delete();
            if($admin_image){
                if(File::exists(public_path().'/'.$admin_image))unlink(public_path().'/'.$admin_image);
            }
        }


        $folderPath = public_path('uploads/custom-images');
        $response = File::deleteDirectory($folderPath);

        $path = public_path('uploads/custom-images');
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $notification = trans('admin_validation.Database Cleared Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function index(){
        $setting = Setting::with('currency')->first();
        $cookieConsent = CookieConsent::first();
        $googleRecaptcha = GoogleRecaptcha::first();
        $facebookComment = FacebookComment::first();
        $tawkChat = TawkChat::first();
        $googleAnalytic = GoogleAnalytic::first();
        $customPaginations = CustomPagination::all();
        $socialLogin = SocialLoginInformation::first();
        $facebookPixel = FacebookPixel::first();
        $pusher = PusherCredentail::first();
        $currencies = MultiCurrency::where('status',1)->orderBy('currency_name','asc')->get();


        return view('admin.setting',compact('setting','cookieConsent','googleRecaptcha','facebookComment','tawkChat','googleAnalytic','customPaginations','socialLogin','facebookPixel','currencies','pusher'));
    }


    public function update_database(){
        Artisan::call('migrate');

        $setting = Setting::first();
        $setting->current_version = '3.9.4';
        $setting->save();

        $notification = trans('Database generated successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }
    public function updateThemeColor(Request $request){
        $setting = Setting::first();
        $setting->theme_one = $request->theme_one;
        $setting->theme_two = $request->theme_two;
        $setting->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateCustomPagination(Request $request){

        foreach($request->quantities as $index => $quantity){
            if($request->quantities[$index]==''){
                $notification=array(
                    'messege'=> trans('admin_validation.Every field is required'),
                    'alert-type'=>'error'
                );

                return redirect()->back()->with($notification);
            }

            $customPagination=CustomPagination::find($request->ids[$index]);
            $customPagination->qty=$request->quantities[$index];
            $customPagination->save();
        }

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }

    public function updateGeneralSetting(Request $request){
        $rules = [
            'frontend_url' => 'required',
            'multivendor' => 'required',
            'layout' => 'required',
            'lg_header' => 'required',
            'sm_header' => 'required',
            'contact_email' => 'required',
            'timezone' => 'required',
        ];
        $customMessages = [
            'frontend_url.required' => trans('admin_validation.Frontend url is required'),
            'multivendor.required' => trans('admin_validation.Multivendor is required'),
            'layout.required' => trans('admin_validation.Layout is required'),
            'lg_header.required' => trans('admin_validation.Sidebar large header is required'),
            'sm_header.required' => trans('admin_validation.Sidebar small header is required'),
            'contact_email.required' => trans('admin_validation.Contact email is required'),
            'timezone.required' => trans('admin_validation.Timezone is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $setting = Setting::first();
        $setting->frontend_url = $request->frontend_url;
        $setting->phone_number_required = $request->phone_number_required;
        $setting->default_phone_code = $request->default_phone_code;
        $setting->enable_multivendor = $request->multivendor;
        $setting->text_direction = $request->layout;
        $setting->sidebar_lg_header = $request->lg_header;
        $setting->sidebar_sm_header = $request->sm_header;
        $setting->contact_email = $request->contact_email;
        $setting->tax = $request->tax;
        $setting->timezone = $request->timezone;
        $setting->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateCookieConset(Request $request){
        $rules = [
            'allow' => 'required',
            'message' => 'required',
        ];
        $customMessages = [
            'allow.required' => trans('admin_validation.Allow is required'),
            'message.required' => trans('admin_validation.Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $cookieConsent = CookieConsent::first();
        $cookieConsent->status = $request->allow;
        $cookieConsent->message = $request->message;
        $cookieConsent->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateFacebookComment(Request $request){
        $rules = [
            'comment_type' => 'required',
            'app_id' => $request->comment_type == 0 ?  'required' : ''
        ];
        $customMessages = [
            'app_id.required' => trans('admin_validation.App id is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $facebookComment = FacebookComment::first();
        $facebookComment->comment_type = $request->comment_type;
        $facebookComment->app_id = $request->app_id;
        $facebookComment->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateTawkChat(Request $request){
        $rules = [
            'allow' => 'required',
            'widget_id' => $request->allow == 1 ?  'required' : '',
            'property_id' => $request->allow == 1 ?  'required' : ''
        ];
        $customMessages = [
            'allow.required' => trans('admin_validation.Allow is required'),
            'widget_id.required' => trans('admin_validation.Widget Id is required'),
            'property_id.required' => trans('admin_validation.Property Id is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $tawkChat = TawkChat::first();
        $tawkChat->status = $request->allow;
        $tawkChat->widget_id = $request->widget_id;
        $tawkChat->property_id = $request->property_id;
        $tawkChat->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateGoogleAnalytic(Request $request){
        $rules = [
            'allow' => 'required',
            'analytic_id' => $request->allow == 1 ?  'required' : ''
        ];
        $customMessages = [
            'allow.required' => trans('admin_validation.Allow is required'),
            'analytic_id.required' => trans('admin_validation.Analytic id is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $googleAnalytic = GoogleAnalytic::first();
        $googleAnalytic->status = $request->allow;
        $googleAnalytic->analytic_id = $request->analytic_id;
        $googleAnalytic->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function updateGoogleRecaptcha(Request $request){

        $rules = [
            'site_key' => $request->allow == 1 ?  'required' : '',
            'secret_key' => $request->allow == 1 ?  'required' : '',
            'allow' => 'required',
        ];
        $customMessages = [
            'site_key.required' => trans('admin_validation.Site key is required'),
            'secret_key.required' => trans('admin_validation.Secret key is required'),
            'allow.required' => trans('admin_validation.Allow is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $googleRecaptcha = GoogleRecaptcha::first();
        $googleRecaptcha->status = $request->allow;
        $googleRecaptcha->site_key = $request->site_key;
        $googleRecaptcha->secret_key = $request->secret_key;
        $googleRecaptcha->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }

    public function updateLogoFavicon(Request $request){
        $setting = Setting::first();
        if($request->logo){
            $old_logo=$setting->logo;
            $image=$request->logo;
            $ext=$image->getClientOriginalExtension();
            $logo_name= 'logo-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$ext;
            $logo_name='uploads/website-images/'.$logo_name;
            $logo=Image::make($image)
                    ->save(public_path().'/'.$logo_name);
            $setting->logo=$logo_name;
            $setting->save();
            if($old_logo){
                if(File::exists(public_path().'/'.$old_logo))unlink(public_path().'/'.$old_logo);
            }
        }

        if($request->favicon){
            $old_favicon=$setting->favicon;
            $favicon=$request->favicon;
            $ext=$favicon->getClientOriginalExtension();
            $favicon_name= 'favicon-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$ext;
            $favicon_name='uploads/website-images/'.$favicon_name;
            Image::make($favicon)
                    ->save(public_path().'/'.$favicon_name);
            $setting->favicon=$favicon_name;
            $setting->save();
            if($old_favicon){
                if(File::exists(public_path().'/'.$old_favicon))unlink(public_path().'/'.$old_favicon);
            }
        }

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function showClearDatabasePage(){
        return view('admin.clear_database');
    }




    public function updateSocialLogin(Request $request){

        $rules = [
            'facebook_app_id' => $request->allow_facebook_login ?  'required' : '',
            'facebook_app_secret' => $request->allow_facebook_login ?  'required' : '',
            'gmail_client_id' => $request->allow_gmail_login ?  'required' : '',
            'gmail_secret_id' => $request->allow_gmail_login ?  'required' : '',
            'gmail_redirect_url' => $request->allow_gmail_login ?  'required' : '',
            'facebook_redirect_url' => $request->allow_gmail_login ?  'required' : '',
        ];
        $customMessages = [
            'facebook_app_id.required' => trans('admin_validation.Facebook app id is required'),
            'facebook_app_secret.required' => trans('admin_validation.Facebook app secret is required'),
            'gmail_client_id.required' => trans('admin_validation.Gmail client id is required'),
            'gmail_secret_id.required' => trans('admin_validation.Gmail secret id is required'),
            'gmail_redirect_url.required' => trans('admin_validation.Gmail redirect url is required'),
            'facebook_redirect_url.required' => trans('admin_validation.Facebook redirect url is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $socialLogin = SocialLoginInformation::first();
        $socialLogin->is_facebook = $request->allow_facebook_login ? 1 : 0;
        $socialLogin->facebook_client_id = $request->facebook_app_id;
        $socialLogin->facebook_secret_id = $request->facebook_app_secret;
        $socialLogin->facebook_redirect_url = $request->facebook_redirect_url;
        $socialLogin->is_gmail = $request->allow_gmail_login ? 1 : 0;
        $socialLogin->gmail_client_id = $request->gmail_client_id;
        $socialLogin->gmail_secret_id = $request->gmail_secret_id;
        $socialLogin->gmail_redirect_url = $request->gmail_redirect_url;
        $socialLogin->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateFacebookPixel(Request $request){

        $rules = [
            'allow_facebook_pixel' => 'required',
            'app_id' => $request->allow_facebook_pixel ?  'required' : '',
        ];
        $customMessages = [
            'app_id.required' => trans('admin_validation.App id is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $facebookPixel = FacebookPixel::first();
        $facebookPixel->app_id = $request->app_id;
        $facebookPixel->status = $request->allow_facebook_pixel ? 1 : 0;
        $facebookPixel->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updatePusher(Request $request){
        $rules = [
            'app_id' => 'required',
            'app_key' => 'required',
            'app_secret' => 'required',
            'app_cluster' => 'required',
        ];
        $customMessages = [
            'app_id.required' => trans('admin_validation.App id is required'),
            'app_key.required' => trans('admin_validation.App key is required'),
            'app_secret.required' => trans('admin_validation.App secret is required'),
            'app_cluster.required' => trans('admin_validation.App cluster is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $pusher = PusherCredentail::first();
        $pusher->app_id = $request->app_id;
        $pusher->app_key = $request->app_key;
        $pusher->app_secret = $request->app_secret;
        $pusher->app_cluster = $request->app_cluster;
        $pusher->save();

        Artisan::call("env:set PUSHER_APP_ID='". $request->app_id ."'");
        Artisan::call("env:set PUSHER_APP_KEY='". $request->app_key ."'");
        Artisan::call("env:set PUSHER_APP_SECRET='". $request->app_secret ."'");
        Artisan::call("env:set PUSHER_APP_CLUSTER='". $request->app_cluster ."'");


        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
