<?php

namespace App\Http\Controllers\Admin;

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
use Image;
use File;
use Artisan;
use Validator;
class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function clearDatabase(){
        AboutUs::truncate();
        BillingAddress::truncate();
        Blog::truncate();
        BlogCategory::truncate();
        BlogComment::truncate();
        Brand::truncate();
        Campaign::truncate();
        CampaignProduct::truncate();
        Category::truncate();
        ChildCategory::truncate();
        ContactMessage::truncate();
        ContactPage::truncate();
        Coupon::truncate();
        CustomPage::truncate();
        Faq::truncate();
        FooterLink::truncate();
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
        ProductTax::truncate();
        ProductVariant::truncate();
        ProductVariantItem::truncate();
        ReturnPolicy::truncate();
        SellerMailLog::truncate();
        SellerWithdraw::truncate();
        Service::truncate();
        ShippingAddress::truncate();
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

        $shippings = ShippingMethod::where('id', '!=', 1)->get();
        foreach($shippings as $shipping){
            $shipping->delete();
        }


        $folderPath = public_path('uploads/custom-images');
        $response = File::deleteDirectory($folderPath);

        $path = public_path('uploads/custom-images');
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $notification = trans('Database Cleared Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function index(){
        $setting = Setting::first();
        $cookieConsent = CookieConsent::first();
        $googleRecaptcha = GoogleRecaptcha::first();
        $facebookComment = FacebookComment::first();
        $tawkChat = TawkChat::first();
        $googleAnalytic = GoogleAnalytic::first();
        $customPaginations = CustomPagination::all();
        $socialLogin = SocialLoginInformation::first();
        $facebookPixel = FacebookPixel::first();
        $pusher = PusherCredentail::first();
        $currencies = Currency::orderBy('name','asc')->get();

        return response()->json(['setting' => $setting, 'cookieConsent' => $cookieConsent, 'googleRecaptcha' => $googleRecaptcha, 'facebookComment' => $facebookComment, 'tawkChat' => $tawkChat, 'googleAnalytic' => $googleAnalytic, 'customPaginations' => $customPaginations, 'socialLogin' => $socialLogin, 'facebookPixel' => $facebookPixel, 'pusher' => $pusher, 'currencies' => $currencies]);
    }

    public function updateThemeColor(Request $request){
        $setting = Setting::first();
        $setting->theme_one = $request->theme_one;
        $setting->theme_two = $request->theme_two;
        $setting->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updateCustomPagination(Request $request){

        foreach($request->quantities as $index => $quantity){
            if($request->quantities[$index]==''){
                $notification=array(
                    'messege'=> trans('Every field is required'),
                    'alert-type'=>'error'
                );

                return redirect()->back()->with($notification);
            }

            $customPagination=CustomPagination::find($request->ids[$index]);
            $customPagination->qty=$request->quantities[$index];
            $customPagination->save();
        }

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);

    }

    public function updateGeneralSetting(Request $request){
        $rules = [
            'user_register' => 'required',
            'multivendor' => 'required',
            'layout' => 'required',
            'lg_header' => 'required',
            'sm_header' => 'required',
            'contact_email' => 'required',
            'currency_name' => 'required',
            'currency_icon' => 'required',
            'timezone' => 'required',
        ];
        $customMessages = [
            'user_register.required' => trans('User register is required'),
            'multivendor.required' => trans('Multivendor is required'),
            'layout.required' => trans('Layout is required'),
            'lg_header.required' => trans('Sidebar large header is required'),
            'sm_header.required' => trans('Sidebar small header is required'),
            'contact_email.required' => trans('Contact email is required'),
            'currency_name.required' => trans('Currency name is required'),
            'currency_icon.required' => trans('Currency icon is required'),
            'timezone.required' => trans('Timezone is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $setting = Setting::first();
        $setting->enable_user_register = $request->user_register;
        $setting->enable_multivendor = $request->multivendor;
        $setting->text_direction = $request->layout;
        $setting->sidebar_lg_header = $request->lg_header;
        $setting->sidebar_sm_header = $request->sm_header;
        $setting->contact_email = $request->contact_email;
        $setting->currency_name = $request->currency_name;
        $setting->currency_icon = $request->currency_icon;
        $setting->timezone = $request->timezone;
        $setting->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updateCookieConset(Request $request){
        $rules = [
            'allow' => 'required',
            'border' => 'required',
            'corners' => 'required',
            'background_color' => 'required',
            'text_color' => 'required',
            'border_color' => 'required',
            'button_color' => 'required',
            'btn_text_color' => 'required',
            'link_text' => 'required',
            'btn_text' => 'required',
            'message' => 'required',
        ];
        $customMessages = [
            'allow.required' => trans('Allow is required'),
            'border.required' => trans('Border is required'),
            'corners.required' => trans('Corner is required'),
            'background_color.required' => trans('Background color is required'),
            'text_color.required' => trans('Text color is required'),
            'border_color.required' => trans('Border Color is required'),
            'button_color.required' => trans('Button color is required'),
            'btn_text_color.required' => trans('Button text color is required'),
            'link_text.required' => trans('Link text is required'),
            'btn_text.required' => trans('Button text is required'),
            'message.required' => trans('Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $cookieConsent = CookieConsent::first();
        $cookieConsent->status = $request->allow;
        $cookieConsent->border = $request->border;
        $cookieConsent->corners = $request->corners;
        $cookieConsent->background_color = $request->background_color;
        $cookieConsent->text_color = $request->text_color;
        $cookieConsent->border_color = $request->border_color;
        $cookieConsent->btn_bg_color = $request->button_color;
        $cookieConsent->btn_text_color = $request->btn_text_color;
        $cookieConsent->link_text = $request->link_text;
        $cookieConsent->btn_text = $request->btn_text;
        $cookieConsent->message = $request->message;
        $cookieConsent->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updateFacebookComment(Request $request){
        $rules = [
            'comment_type' => 'required',
            'app_id' => $request->comment_type == 0 ?  'required' : ''
        ];
        $customMessages = [
            'app_id.required' => trans('App id is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $facebookComment = FacebookComment::first();
        $facebookComment->comment_type = $request->comment_type;
        $facebookComment->app_id = $request->app_id;
        $facebookComment->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updateTawkChat(Request $request){
        $rules = [
            'allow' => 'required',
            'chat_link' => $request->allow == 1 ?  'required' : ''
        ];
        $customMessages = [
            'allow.required' => trans('Allow is required'),
            'chat_link.required' => trans('Chat link is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $tawkChat = TawkChat::first();
        $tawkChat->status = $request->allow;
        $tawkChat->chat_link = $request->chat_link;
        $tawkChat->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updateGoogleAnalytic(Request $request){
        $rules = [
            'allow' => 'required',
            'analytic_id' => $request->allow == 1 ?  'required' : ''
        ];
        $customMessages = [
            'allow.required' => trans('Allow is required'),
            'analytic_id.required' => trans('Analytic id is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $googleAnalytic = GoogleAnalytic::first();
        $googleAnalytic->status = $request->allow;
        $googleAnalytic->analytic_id = $request->analytic_id;
        $googleAnalytic->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }


    public function updateGoogleRecaptcha(Request $request){

        $rules = [
            'site_key' => $request->allow == 1 ?  'required' : '',
            'secret_key' => $request->allow == 1 ?  'required' : '',
            'allow' => 'required',
        ];
        $customMessages = [
            'site_key.required' => trans('Site key is required'),
            'secret_key.required' => trans('Secret key is required'),
            'allow.required' => trans('Allow is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $googleRecaptcha = GoogleRecaptcha::first();
        $googleRecaptcha->status = $request->allow;
        $googleRecaptcha->site_key = $request->site_key;
        $googleRecaptcha->secret_key = $request->secret_key;
        $googleRecaptcha->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);

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

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
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
            'facebook_app_id.required' => trans('Facebook app id is required'),
            'facebook_app_secret.required' => trans('Facebook app secret is required'),
            'gmail_client_id.required' => trans('Gmail client id is required'),
            'gmail_secret_id.required' => trans('Gmail secret id is required'),
            'gmail_redirect_url.required' => trans('Gmail redirect url is required'),
            'facebook_redirect_url.required' => trans('Facebook redirect url is required'),
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

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updateFacebookPixel(Request $request){

        $rules = [
            'allow_facebook_pixel' => 'required',
            'app_id' => $request->allow_facebook_pixel ?  'required' : '',
        ];
        $customMessages = [
            'app_id.required' => trans('App id is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $facebookPixel = FacebookPixel::first();
        $facebookPixel->app_id = $request->app_id;
        $facebookPixel->status = $request->allow_facebook_pixel ? 1 : 0;
        $facebookPixel->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updatePusher(Request $request){
        $rules = [
            'app_id' => 'required',
            'app_key' => 'required',
            'app_secret' => 'required',
            'app_cluster' => 'required',
        ];
        $customMessages = [
            'app_id.required' => trans('App id is required'),
            'app_key.required' => trans('App key is required'),
            'app_secret.required' => trans('App secret is required'),
            'app_cluster.required' => trans('App cluster is required'),
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


        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }
}
