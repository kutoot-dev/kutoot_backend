<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\HomePageOneVisibility;

use App\Models\Brand;

use App\Models\Slider;

use App\Models\Category;

use App\Models\SubCategory;

use App\Models\ChildCategory;

use App\Models\PopularCategory;

use App\Models\Product;

use App\Models\BannerImage;

use App\Models\Service;

use App\Models\Blog;

use App\Models\AboutUs;

use App\Models\ContactPage;

use App\Models\PopularPost;

use App\Models\BlogCategory;

use App\Models\BreadcrumbImage;

use App\Models\CustomPagination;

use App\Models\Faq;

use App\Models\CustomPage;

use App\Models\TermsAndCondition;

use App\Models\Vendor;

use App\Models\Subscriber;

use App\Mail\SubscriptionVerification;

use App\Mail\ContactMessageInformation;

use App\Helpers\MailHelper;

use App\Models\EmailTemplate;

use App\Models\ProductReview;

use App\Models\ProductSpecification;

use App\Models\ProductGallery;

use App\Models\Setting;

use App\Models\ContactMessage;

use App\Models\BlogComment;

use App\Models\ProductVariant;

use App\Models\ProductVariantItem;

use App\Models\Testimonial;

use App\Models\GoogleRecaptcha;

use App\Models\Order;

use App\Models\ShopPage;

use App\Models\SeoSetting;

use App\Models\FlashSale;

use App\Models\FlashSaleProduct;

use App\Rules\Captcha;

use Illuminate\Support\Facades\Mail;

use Str;

use Session;

use Cart;

use Carbon\Carbon;

use Route;



use App\Models\FooterSocialLink;

use App\Models\AnnouncementModal;

use App\Models\MegaMenuCategory;

use App\Models\MenuVisibility;

use App\Models\GoogleAnalytic;

use App\Models\FacebookPixel;

use App\Models\TawkChat;

use App\Models\CookieConsent;

use App\Models\FeaturedCategory;

use App\Models\FooterLink;

use App\Models\Footer;

use App\Models\MultiCurrency;

use App\Models\MaintainanceText;
use App\Models\PusherCredentail;

use App\Enums\ProductApprovalStatus;

use Artisan;



class HomeController extends Controller

{

    public function websiteSetup(){

        $language = [
                    "id" => 1,
                    "lang_code" => "en",
                    "lang_name" => "English",
                    "is_default" => "Yes",
                    "status" => 1,
                    "lang_direction" => "left_to_right",
                    "created_at" => "2024-10-13T18:42:20.000000Z",
                    "updated_at" => "2024-10-13T18:47:22.000000Z"
                ];

        $listall = include(resource_path('lang/en/user.php'));

        $currencies = MultiCurrency::where('status',1)->orderBy('currency_name','asc')->get();

        $setting = Setting::with('currency')->select('currency_id','logo','favicon','enable_user_register','phone_number_required','default_phone_code','enable_multivendor','text_direction','timezone','topbar_phone','topbar_email','currency_icon','currency_name','show_product_progressbar','theme_one','theme_two','seller_condition')->first();

        $announcementModal = AnnouncementModal::first();

        $productCategories = Category::with('activeSubCategories.activeChildCategories')->where(['status' => 1])->select('id','name','slug','icon')->get();

        $megaMenuCategories = MegaMenuCategory::with('category','subCategories.subCategory')->orderBy('serial','asc')->where('status',1)->get();

        $megaMenuBanner = BannerImage::find(23);

        $customPages = CustomPage::where('status',1)->get();

        $googleAnalytic = GoogleAnalytic::first();

        $facebookPixel = FacebookPixel::first();

        $tawk_setting = TawkChat::first();

        $cookie_consent = CookieConsent::first();

        $maintainance = MaintainanceText::first();



        $flashSale = FlashSale::select('status','offer','end_time')->first();

        $flashSaleProducts = FlashSaleProduct::where('status',1)->select('product_id')->get();

        $flashSaleActive = $flashSale->status == 1 ? true : false;



        $seo_setting = SeoSetting::all();



        $shop_page = ShopPage::first();

        $filter_price_range = $shop_page->filter_price_range;





        $first_col_links = FooterLink::where('column',1)->get();

        $footer = Footer::first();

        $columnTitle = $footer->first_column;

        $footer_first_col = array(

            'col_links' => $first_col_links,

            'columnTitle' => $columnTitle

        );

        $footer_first_col = (object)$footer_first_col;



        $second_col_links = FooterLink::where('column',2)->get();

        $columnTitle = $footer->second_column;

        $footer_second_col = array(

            'col_links' => $second_col_links,

            'columnTitle' => $columnTitle

        );

        $footer_second_col = (object)$footer_second_col;



        $third_col_links = FooterLink::where('column',3)->get();

        $columnTitle = $footer->third_column;

        $footer_third_col = array(

            'col_links' => $third_col_links,

            'columnTitle' => $columnTitle

        );

        $footer_third_col = (object)$footer_third_col;



        $social_links = FooterSocialLink::all();



        $image_content = Setting::select('empty_cart','empty_wishlist', 'change_password_image', 'become_seller_avatar', 'become_seller_banner','login_image','error_page')->first();

        $pusher_info = PusherCredentail::first();


        return response()->json([
            'default_language' => $language,
            'language_list' => [$language],
            'language' => $listall,
            'setting' => $setting,

            'currencies' => $currencies,

            'maintainance' => $maintainance,

            'flashSaleActive' => $flashSaleActive,

            'flashSale' => $flashSale,

            'flashSaleProducts' => $flashSaleProducts,

            'announcementModal' => $announcementModal,

            'productCategories' => $productCategories,

            'megaMenuCategories' => $megaMenuCategories,

            'megaMenuBanner' => $megaMenuBanner,

            'customPages' => $customPages,

            'googleAnalytic' => $googleAnalytic,

            'facebookPixel' => $facebookPixel,

            'tawk_setting' => $tawk_setting,

            'cookie_consent' => $cookie_consent,

            'seo_setting' => $seo_setting,

            'filter_price_range' => $filter_price_range,

            'footer_first_col' => $footer_first_col,

            'footer_second_col' => $footer_second_col,

            'footer_third_col' => $footer_third_col,

            'footer' => $footer,

            'social_links' => $social_links,

            'image_content' => $image_content,
            'pusher_info' => $pusher_info,

        ]);



    }



    public function subCategoriesByCategory($id){

        $subCategories = SubCategory::where(['category_id' => $id, 'status' => 1])->get();

        return response()->json(['subCategories' => $subCategories]);

    }



    public function childCategoriesBySubCategory($id){

        $childCategories = ChildCategory::where(['sub_category_id' => $id, 'status' => 1])->get();

        return response()->json(['childCategories' => $childCategories]);

    }



    public function categoryList(){

        $categories = Category::where('status', 1)->get();

        return response()->json(['categories' => $categories]);

    }

    public function category($id){

        $category = Category::find($id);

        return response()->json(['category' => $category]);

    }





    public function subCategory($id){

        $sub_category = SubCategory::find($id);

        return response()->json(['sub_category' => $sub_category]);

    }



    public function childCategory($id){

        $child_category = ChildCategory::find($id);

        return response()->json(['child_category' => $child_category]);

    }





    public function productByCategory($id){

        $category = Category::find($id);

        $products = Product::with('activeVariants.activeVariantItems')->where(['category_id' => $id, 'status' => 1,'approval_status' => ProductApprovalStatus::APPROVED])->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id')->orderBy('id','desc')->get();

        return response()->json(['category' => $category, 'products' => $products]);

    }





    public function index()

    {



        // $sliderVisibilty = HomePageOneVisibility::find(1);

        // $sliders = Slider::orderBy('serial','asc')->where(['status' => 1])->get()->take($sliderVisibilty->qty);

        // $sliderVisibilty = $sliderVisibilty->status == 1 ? true : false;


$sliderVisibilty = HomePageOneVisibility::find(1);

$qty = $sliderVisibilty ? $sliderVisibilty->qty : 0;
$status = $sliderVisibilty ? $sliderVisibilty->status : 0;

$sliders = Slider::where('status', 1)
    ->orderBy('serial', 'asc')
    ->take($qty)
    ->get();

$sliderVisibilty = $status == 1;







        $sliderBannerOne = BannerImage::select('id','product_slug','image','banner_location','title_one','title_two','badge','status')->find(16);

        $sliderBannerTwo = BannerImage::select('id','product_slug','image','banner_location','title_one','title_two','badge','status')->find(17);



        $serviceVisibilty = HomePageOneVisibility::find(2);

        $services = Service::where('status',1)->get()->take($serviceVisibilty->qty);

        $serviceVisibilty = $serviceVisibilty->status == 1 ? true : false;



        $popularCategoryVisibilty = HomePageOneVisibility::find(4);

        $popularCategories = PopularCategory::with('category')->get();

        $category_arr = [];

        foreach($popularCategories as $popularCategory){

            $category_arr[] = $popularCategory->category_id;

        }

        $setting = Setting::first();



        $popularCategoryProducts = Product::with('activeVariants.activeVariantItems')->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id')->whereIn('category_id', $category_arr)->where('status',1)->where('approval_status', ProductApprovalStatus::APPROVED)->orderBy('id','desc')->get()->take($popularCategoryVisibilty->qty);

        $popularCategoryVisibilty = $popularCategoryVisibilty->status == 1 ? true : false;

        $popularCategorySidebarBanner = $setting->popular_category_banner;



        $brandVisibility = HomePageOneVisibility::find(5);

        $brands = Brand::where(['status' => 1])->get()->take($brandVisibility->qty);

        $brandVisibility = $brandVisibility->status == 1 ? true : false;



        $flashSale = FlashSale::first();

        $flashSaleSidebarBanner = BannerImage::select('id','link as play_store','image','banner_location','status','title as app_store')->find(24);



        $topRatedVisibility = HomePageOneVisibility::find(6);

        $topRatedProducts = Product::with('activeVariants.activeVariantItems')->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id')->where(['is_top' => 1, 'status' => 1,'approval_status' => ProductApprovalStatus::APPROVED])->orderBy('id','desc')->get()->take($topRatedVisibility->qty);

        $topRatedVisibility = $topRatedVisibility->status == 1 ? true : false;



        $sellerVisibility = HomePageOneVisibility::find(7);

        $sellers = Vendor::where(['status' => 1])->select('id','logo','banner_image','shop_name','slug')->get()->take($sellerVisibility->qty);

        $sellerVisibility = $sellerVisibility->status == 1 ? true : false;



        $twoColumnBannerOne = BannerImage::select('id','product_slug','image','banner_location','status','title_one','title_two','badge')->find(19);

        $twoColumnBannerTwo = BannerImage::select('id','product_slug','image','banner_location','status','title_one','title_two','badge')->find(20);



        $featuredCategorySidebarBanner = $setting->featured_category_banner;



        $featuredProductVisibility = HomePageOneVisibility::find(8);

        $featuredCategories = FeaturedCategory::with('category')->get();

        $category_arr = [];

        foreach($featuredCategories as $featuredCategory){

            $category_arr[] = $featuredCategory->category_id;

        }





        $featuredCategoryProducts = Product::with('activeVariants.activeVariantItems')->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id')->whereIn('category_id', $category_arr)->where(['status' => 1,'approval_status' => ProductApprovalStatus::APPROVED])->orderBy('id','desc')->get()->take($featuredProductVisibility->qty);

        $featuredProductVisibility = $featuredProductVisibility->status == 1 ? true : false;



        $singleBannerOne = BannerImage::select('id','product_slug','image','banner_location','status','title_one','title_two')->find(21);



        $newArrivalProductVisibility = HomePageOneVisibility::find(9);

        $newArrivalProducts = Product::with('activeVariants.activeVariantItems')->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id')->where(['new_product' => 1, 'status' => 1, 'approval_status' => ProductApprovalStatus::APPROVED])->orderBy('id','desc')->get()->take($newArrivalProductVisibility->qty);

        $newArrivalProductVisibility = $newArrivalProductVisibility->status == 1 ? true : false;



        $singleBannerTwo = BannerImage::select('id','product_slug','image','banner_location','status','title_one')->find(22);



        $bestProductVisibility = HomePageOneVisibility::find(10);

        $bestProducts = Product::with('activeVariants.activeVariantItems')->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id')->where(['is_best' => 1, 'status' => 1, 'approval_status' => ProductApprovalStatus::APPROVED])->orderBy('id','desc')->get()->take($bestProductVisibility->qty);

        $bestProductVisibility = $bestProductVisibility->status == 1 ? true : false;



        $subscriptionBanner = BannerImage::select('id','image','banner_location','header','title')->find(27);



        $seoSetting = SeoSetting::find(1);



        $setting = Setting::first();

        $section_title = json_decode($setting->homepage_section_title);

        Artisan::call('optimize:clear');



        $homepage_categories = Category::where(['status' => 1])->select('id','name','slug','icon','image')->get()->take(15);





        return response()->json([

            'section_title' => $section_title,

            'seoSetting' => $seoSetting,

            'sliderVisibilty' => $sliderVisibilty,

            'sliders' => $sliders,

            'sliderBannerOne' => $sliderBannerOne,

            'sliderBannerTwo' => $sliderBannerTwo,

            'serviceVisibilty' => $serviceVisibilty,

            'services' => $services,

            'homepage_categories' => $homepage_categories,

            'popularCategorySidebarBanner' => $popularCategorySidebarBanner,

            'popularCategoryVisibilty' => $popularCategoryVisibilty,

            'popularCategories' => $popularCategories,

            'popularCategoryProducts' => $popularCategoryProducts,

            'brandVisibility' => $brandVisibility,

            'brands' => $brands,

            'flashSale' => $flashSale,

            'flashSaleSidebarBanner' => $flashSaleSidebarBanner,

            'topRatedVisibility' => $topRatedVisibility,

            'topRatedProducts' => $topRatedProducts,

            'sellerVisibility' => $sellerVisibility,

            'sellers' => $sellers,

            'twoColumnBannerOne' => $twoColumnBannerOne,

            'twoColumnBannerTwo' => $twoColumnBannerTwo,

            'featuredProductVisibility' => $featuredProductVisibility,

            'featuredCategorySidebarBanner' => $featuredCategorySidebarBanner,

            'featuredCategories' => $featuredCategories,

            'featuredCategoryProducts' => $featuredCategoryProducts,

            'singleBannerOne' => $singleBannerOne,

            'newArrivalProductVisibility' => $newArrivalProductVisibility,

            'newArrivalProducts' => $newArrivalProducts,

            'bestProductVisibility' => $bestProductVisibility,

            'singleBannerTwo' => $singleBannerTwo,

            'bestProducts' => $bestProducts,

            'subscriptionBanner' => $subscriptionBanner,

        ]);

    }




//  protected function sectionConfig(int $id): array
//     {
//         $config = HomePageOneVisibility::find($id);

//         return [
//             'qty' => $config->qty ?? 0,
//             'visible' => ($config->status ?? 0) === 1,
//         ];
//     }



// public function index()
// {
//     $setting = Setting::first();
//     $seoSetting = SeoSetting::find(1);


//     $sliderConfig = $this->sectionConfig(1);
//     $sliders = Slider::where('status', 1)
//         ->orderBy('serial')
//         ->limit($sliderConfig['qty'])
//         ->get();


//     $serviceConfig = $this->sectionConfig(2);
//     $services = Service::where('status', 1)
//         ->limit($serviceConfig['qty'])
//         ->get();

//     $popularCategoryConfig = $this->sectionConfig(4);
//     $popularCategories = PopularCategory::with('category')->get();
//     $popularCategoryIds = $popularCategories->pluck('category_id')->toArray();

//     $popularCategoryProducts = Product::with('activeVariants.activeVariantItems')
//         ->whereIn('category_id', $popularCategoryIds)
//         ->where([
//             'status' => 1,
//             'approval_status' => ProductApprovalStatus::APPROVED
//         ])
//         ->orderByDesc('id')
//         ->limit($popularCategoryConfig['qty'])
//         ->get();

//     $brandConfig = $this->sectionConfig(5);
//     $brands = Brand::where('status', 1)
//         ->limit($brandConfig['qty'])
//         ->get();


//     $topRatedConfig = $this->sectionConfig(6);
//     $topRatedProducts = Product::with('activeVariants.activeVariantItems')
//         ->where([
//             'is_top' => 1,
//             'status' => 1,
//             'approval_status' => ProductApprovalStatus::APPROVED
//         ])
//         ->orderByDesc('id')
//         ->limit($topRatedConfig['qty'])
//         ->get();


//     $sellerConfig = $this->sectionConfig(7);
//     $sellers = Vendor::where('status', 1)
//         ->select('id','logo','banner_image','shop_name','slug')
//         ->limit($sellerConfig['qty'])
//         ->get();


//     $featuredConfig = $this->sectionConfig(8);
//     $featuredCategories = FeaturedCategory::with('category')->get();
//     $featuredCategoryIds = $featuredCategories->pluck('category_id')->toArray();

//     $featuredCategoryProducts = Product::with('activeVariants.activeVariantItems')
//         ->whereIn('category_id', $featuredCategoryIds)
//         ->where([
//             'status' => 1,
//             'approval_status' => ProductApprovalStatus::APPROVED
//         ])
//         ->orderByDesc('id')
//         ->limit($featuredConfig['qty'])
//         ->get();


//     $newArrivalConfig = $this->sectionConfig(9);
//     $newArrivalProducts = Product::with('activeVariants.activeVariantItems')
//         ->where([
//             'new_product' => 1,
//             'status' => 1,
//             'approval_status' => ProductApprovalStatus::APPROVED
//         ])
//         ->orderByDesc('id')
//         ->limit($newArrivalConfig['qty'])
//         ->get();


//     $bestConfig = $this->sectionConfig(10);
//     $bestProducts = Product::with('activeVariants.activeVariantItems')
//         ->where([
//             'is_best' => 1,
//             'status' => 1,
//             'approval_status' => ProductApprovalStatus::APPROVED
//         ])
//         ->orderByDesc('id')
//         ->limit($bestConfig['qty'])
//         ->get();


//     $banners = BannerImage::whereIn('id', [
//         16,17,19,20,21,22,24,27
//     ])->get()->keyBy('id');


//     $homepage_categories = Category::where('status', 1)
//         ->select('id','name','slug','icon','image')
//         ->limit(15)
//         ->get();

//     $section_title = json_decode($setting->homepage_section_title ?? '{}');

//     return response()->json([
//         'section_title' => $section_title,
//         'seoSetting' => $seoSetting,

//         'sliderVisibilty' => $sliderConfig['visible'],
//         'sliders' => $sliders,

//         'serviceVisibilty' => $serviceConfig['visible'],
//         'services' => $services,

//         'popularCategoryVisibilty' => $popularCategoryConfig['visible'],
//         'popularCategories' => $popularCategories,
//         'popularCategoryProducts' => $popularCategoryProducts,
//         'popularCategorySidebarBanner' => $setting->popular_category_banner ?? null,

//         'brandVisibility' => $brandConfig['visible'],
//         'brands' => $brands,

//         'topRatedVisibility' => $topRatedConfig['visible'],
//         'topRatedProducts' => $topRatedProducts,

//         'sellerVisibility' => $sellerConfig['visible'],
//         'sellers' => $sellers,

//         'featuredProductVisibility' => $featuredConfig['visible'],
//         'featuredCategories' => $featuredCategories,
//         'featuredCategoryProducts' => $featuredCategoryProducts,
//         'featuredCategorySidebarBanner' => $setting->featured_category_banner ?? null,

//         'newArrivalProductVisibility' => $newArrivalConfig['visible'],
//         'newArrivalProducts' => $newArrivalProducts,

//         'bestProductVisibility' => $bestConfig['visible'],
//         'bestProducts' => $bestProducts,

//         'homepage_categories' => $homepage_categories,

//         'sliderBannerOne' => $banners[16] ?? null,
//         'sliderBannerTwo' => $banners[17] ?? null,
//         'twoColumnBannerOne' => $banners[19] ?? null,
//         'twoColumnBannerTwo' => $banners[20] ?? null,
//         'singleBannerOne' => $banners[21] ?? null,
//         'singleBannerTwo' => $banners[22] ?? null,
//         'flashSaleSidebarBanner' => $banners[24] ?? null,
//         'subscriptionBanner' => $banners[27] ?? null,
//     ]);
// }

























    public function aboutUs(){

        $aboutUs = AboutUs::first();

        $seoSetting = SeoSetting::find(2);

        $testimonials = Testimonial::where(['status' => 1])->get();

        $services = Service::where('status',1)->get();

        $blogs = PopularPost::with('blog.activeComments')->where('status',1)->orderBy('id','desc')->get()->take(10);



        return response()->json([

            'aboutUs' => $aboutUs,

            'seoSetting' => $seoSetting,

            'testimonials' => $testimonials,

            'services' => $services,

            'blogs' => $blogs,

        ]);

    }



    public function contactUs(){

        $contact = ContactPage::first();

        $recaptchaSetting = GoogleRecaptcha::first();

        $seoSetting = SeoSetting::find(3);



        return response()->json([

            'contact' => $contact,

            'recaptchaSetting' => $recaptchaSetting,

            'seoSetting' => $seoSetting

        ]);

    }



    public function sendContactMessage(Request $request){

        $rules = [

            'name'=>'required',

            'email'=>'required',

            'subject'=>'required',

            'message'=>'required',

            'g-recaptcha-response'=>new Captcha()

        ];

        $this->validate($request, $rules);



        $setting = Setting::first();

        if($setting->enable_save_contact_message == 1){

            $contact = new ContactMessage();

            $contact->name = $request->name;

            $contact->email = $request->email;

            $contact->subject = $request->subject;

            $contact->phone = $request->phone;

            $contact->message = $request->message;

            $contact->save();

        }



        MailHelper::setMailConfig();

        $template = EmailTemplate::where('id',2)->first();

        $message = $template->description;

        $subject = $template->subject;

        $message = str_replace('{{name}}',$request->name,$message);

        $message = str_replace('{{email}}',$request->email,$message);

        $message = str_replace('{{phone}}',$request->phone,$message);

        $message = str_replace('{{subject}}',$request->subject,$message);

        $message = str_replace('{{message}}',$request->message,$message);



        Mail::to($setting->contact_email)->send(new ContactMessageInformation($message,$subject));



        $notification = trans('user_validation.Message send successfully');

        return response()->json(['notification' => $notification]);

    }



    public function blog(Request $request){

        $paginateQty = CustomPagination::whereId('1')->first()->qty;

        $blogs = Blog::with('activeComments')->orderBy('id','desc')->where(['status' => 1]);



        if($request->search){

            $blogs = $blogs->where('title','LIKE','%'.$request->search.'%');

        }



        if($request->category){

            $category = BlogCategory::where('slug',$request->category)->first();

            $blogs = $blogs->where('blog_category_id', $category->id);

        }



        $blogs = $blogs->paginate($paginateQty);

        $seoSetting = SeoSetting::find(6);



        return response()->json(['blogs' => $blogs, 'seoSetting' => $seoSetting]);

    }



    public function blogDetail($slug){

        $blog = Blog::where(['status' => 1, 'slug'=>$slug])->first();

        $blog->views += 1;

        $blog->save();



        $popularPosts = PopularPost::with('blog')->where(['status' => 1])->get();



        $categories = BlogCategory::where(['status' => 1])->get();



        $recaptchaSetting = GoogleRecaptcha::first();



        $paginateQty = CustomPagination::whereId('4')->first()->qty;

        $activeComments = BlogComment::where('blog_id', $blog->id)->orderBy('id','desc')->paginate($paginateQty);



        return response()->json(['blog' => $blog, 'popularPosts' => $popularPosts, 'categories' => $categories, 'recaptchaSetting' => $recaptchaSetting, 'activeComments' => $activeComments]);

    }



    public function blogComment(Request $request){

        $rules = [

            'name'=>'required',

            'email'=>'required',

            'comment'=>'required',

            'blog_id'=>'required',

            'g-recaptcha-response'=>new Captcha()

        ];

        $this->validate($request, $rules);



        $comment = new BlogComment();

        $comment->blog_id = $request->blog_id;

        $comment->name = $request->name;

        $comment->email = $request->email;

        $comment->comment = $request->comment;

        $comment->save();



        $notification = trans('user_validation.Blog comment submited successfully');



        return response()->json(['message' => $notification],200);

    }



    public function faq(){

        $faqs = FAQ::orderBy('id','desc')->where('status',1)->get();

        return response()->json(['faqs' => $faqs]);

    }



    public function trackOrderResponse($id){

        if(!$id){

            $message = trans('user_validation.Order id is required');

            return response()->json(['status'=> 0, 'message' => $message]);

        }

        $order = Order::where('order_id',$id)->first();

        if($order){



            return response()->json(['order'=> $order]);

        }else{

            $message = trans('user_validation.Order not found');

            return response()->json(['status'=> 0, 'message' => $message]);

        }

    }





    public function allCustomPage(){

        $pages = CustomPage::where(['status' => 1])->get();

        return response()->json(['pages'=> $pages]);

    }



    public function customPage($slug){

        $page = CustomPage::where(['slug' => $slug, 'status' => 1])->first();

        return response()->json(['page'=> $page]);

    }



    public function termsAndCondition(){

        $terms_conditions = TermsAndCondition::select('terms_and_condition')->first();

        return response()->json(['terms_conditions'=> $terms_conditions]);

    }



    public function sellerTemsCondition(){

        $seller_tems_conditions = Setting::select('seller_condition')->first();

        return response()->json(['seller_tems_conditions'=> $seller_tems_conditions]);

    }







    public function privacyPolicy(){

        $privacyPolicy = TermsAndCondition::select('privacy_policy')->first();

        return response()->json(['privacyPolicy'=> $privacyPolicy]);

    }



    public function seller(){



        $sellers = Vendor::orderBy('id','desc')->where('status',1)->select('id','banner_image','shop_name','slug','open_at','closed_at','address','email','logo','phone')->paginate(20);

        $seoSetting = SeoSetting::find(5);



        return response()->json([

            'sellers' => $sellers,

            'seoSetting' => $seoSetting,

        ]);



    }



    public function sellerDetail(Request $request, $shop_name){

        $slug = $shop_name;

        $seller = Vendor::where(['status' => 1, 'slug' => $slug])->select('id','banner_image','shop_name','slug','open_at','closed_at','address','email','phone','seo_title','seo_description','logo')->first();

        if(!$seller){

            return response()->json(['message' => 'Seller not found'],403);

        }



        $searchCategoryArr = [];

        $searchBrandArr = [];

        $categories = Category::with('activeSubCategories.activeChildCategories')->where(['status' => 1])->select('id','name','slug','icon')->get();

        $brands = Brand::where(['status' => 1])->select('id','name','slug')->get();

        $activeVariants = ProductVariant::with('activeVariantItems')->select('name','id')->groupBy('name')->get();



        $paginateQty = CustomPagination::whereId('2')->first()->qty;

        $products = Product::with('activeVariants.activeVariantItems')->orderBy('id','desc')->where(['status' => 1, 'vendor_id' => $seller->id, 'approval_status' => ProductApprovalStatus::APPROVED]);



        if($request->category) {

            $category = Category::where('slug',$request->category)->first();

            $products = $products->where('category_id', $category->id);

            $searchCategoryArr[] = $category->id;

        }



        if($request->sub_category) {

            $sub_category = SubCategory::where('slug',$request->sub_category)->first();

            $products = $products->where('sub_category_id', $sub_category->id);

            $searchCategoryArr[] = $sub_category->category_id;

        }



        if($request->child_category) {

            $child_category = ChildCategory::where('slug',$request->child_category)->first();

            $products = $products->where('child_category_id', $child_category->id);

            $searchCategoryArr[] = $child_category->category_id;

        }



        if($request->brand) {

            $brand = Brand::where('slug',$request->brand)->first();

            $products = $products->where('brand_id', $brand->id);

            $searchBrandArr[] = $brand->id;

        }



        if($request->search) {

            $products = $products->where('name','LIKE','%'.$request->search.'%');

        }



        $paginateQty = CustomPagination::whereId('2')->first()->qty;



        $products = $products->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id');

        if($request->per_page){

            $products = $products->paginate($request->per_page);

        }else{

            $products = $products->paginate($paginateQty);

        }

        $products = $products->appends($request->all());



        $sellerReviewQty = ProductReview::where('status',1)->where('product_vendor_id',$seller->id)->count();

        $sellerTotalReview = ProductReview::where('status',1)->where('product_vendor_id',$seller->id)->sum('rating');



        $shopPageCenterBanner = BannerImage::select('product_slug','image','banner_location','status','after_product_qty','title_one')->find(25);

        $shopPageSidebarBanner = BannerImage::select('product_slug','image','banner_location','status','title_one','title_two')->find(26);



        return response()->json([

            'seller' => $seller,

            'sellerReviewQty' => $sellerReviewQty,

            'sellerTotalReview' => $sellerTotalReview,

            'searchCategoryArr' => $searchCategoryArr,

            'searchBrandArr' => $searchBrandArr,

            'categories' => $categories,

            'brands' => $brands,

            'activeVariants' => $activeVariants,

            'products' => $products,

            'shopPageCenterBanner' => $shopPageCenterBanner,

            'shopPageSidebarBanner' => $shopPageSidebarBanner,

        ]);

    }



    public function variantItemsByVariant($name){

        $variantItemsForSearch = ProductVariantItem::with('product','variant')->groupBy('name')->select('name','id')->where('product_variant_name', $name)->get();



        return response()->json(['variantItemsForSearch' => $variantItemsForSearch]);

    }



    public function product(Request $request){

        $searchCategoryArr = [];

        $searchBrandArr = [];

        $categories = Category::with('activeSubCategories.activeChildCategories')->where(['status' => 1])->select('id','name','slug','icon')->get();

        $brands = Brand::where(['status' => 1])->select('id','name','slug')->get();

        $activeVariants = ProductVariant::with('activeVariantItems')->select('name','id')->groupBy('name')->get();



        $paginateQty = CustomPagination::whereId('2')->first()->qty;

        $products = Product::with('activeVariants.activeVariantItems')->orderBy('id','desc')->where(['status' => 1, 'approval_status' => ProductApprovalStatus::APPROVED]);



        if($request->category) {

            $category = Category::where('slug',$request->category)->first();

            $products = $products->where('category_id', $category->id);

            $searchCategoryArr[] = $category->id;

        }



        if($request->sub_category) {

            $sub_category = SubCategory::where('slug',$request->sub_category)->first();

            $products = $products->where('sub_category_id', $sub_category->id);

            $searchCategoryArr[] = $sub_category->category_id;

        }



        if($request->child_category) {

            $child_category = ChildCategory::where('slug',$request->child_category)->first();

            $products = $products->where('child_category_id', $child_category->id);

            $searchCategoryArr[] = $child_category->category_id;

        }





        $popularCategoryArr = [];

        if($request->highlight){



            if($request->highlight == 'popular_category'){

                $pop_categories = PopularCategory::all();

                foreach($pop_categories as $pop_category){

                    $popularCategoryArr[] = $pop_category->category_id;

                }

                $products = $products->whereIn('category_id', $popularCategoryArr);

            }



            if($request->highlight == 'top_product'){

                $products = $products->where('is_top',1);

            }



            if($request->highlight == 'new_arrival'){

                $products = $products->where('new_product',1);

            }



            if($request->highlight == 'featured_product'){

                $products = $products->where('is_featured',1);

            }



            if($request->highlight == 'best_product'){

                $products = $products->where('is_best',1);

            }



        }





        if($request->brand) {

            $brand = Brand::where('slug',$request->brand)->first();

            $products = $products->where('brand_id', $brand->id);

            $searchBrandArr[] = $brand->id;

        }



        if($request->search) {

            $products = $products->where('name','LIKE','%'.$request->search.'%');

        }



        $products = $products->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id');

        $products = $products->paginate($paginateQty);

        $products = $products->appends($request->all());

        $seoSetting = SeoSetting::find(9);



        $shopPageCenterBanner = BannerImage::select('product_slug','image','banner_location','status','after_product_qty','title_one')->find(25);

        $shopPageSidebarBanner = BannerImage::select('product_slug','image','banner_location','status','title_one','title_two')->find(26);



        return response()->json([

            'searchCategoryArr' => $searchCategoryArr,

            'searchBrandArr' => $searchBrandArr,

            'categories' => $categories,

            'brands' => $brands,

            'activeVariants' => $activeVariants,

            'products' => $products,

            'seoSetting' => $seoSetting,

            'shopPageCenterBanner' => $shopPageCenterBanner,

            'shopPageSidebarBanner' => $shopPageSidebarBanner,

        ]);



        return response()->json(['banner' => $banner, 'products' => $products, 'productCategories' => $productCategories, 'brands' => $brands, 'shop_page' => $shop_page, 'variantsForSearch' => $variantsForSearch, 'seoSetting' => $seoSetting, 'currencySetting' => $currencySetting, 'setting' => $setting]);

    }



    public function searchProduct(Request $request){

        $paginateQty = CustomPagination::whereId('2')->first()->qty;

        if($request->variantItems){

            $products = Product::with('activeVariants.activeVariantItems')->whereHas('variantItems', function($query) use ($request){

                $sortArr = [];

                if($request->variantItems){

                    foreach($request->variantItems as $variantItem){

                        $sortArr[] = $variantItem;

                    }

                    $query->whereIn('name', $sortArr);

                }

            })->where('status',1)->where('approval_status', ProductApprovalStatus::APPROVED);

        }else{

            $products = Product::with('activeVariants.activeVariantItems')->where('status',1)->where('approval_status', ProductApprovalStatus::APPROVED);

        }



        if($request->shorting_id){

            if($request->shorting_id == 1){

                $products = $products->orderBy('id','desc');

            }else if($request->shorting_id == 2){

                $products = $products->orderBy('price','asc');

            }else if($request->shorting_id == 3){

                $products = $products->orderBy('price','desc');

            }

        }else{

            $products = $products->orderBy('id','desc');

        }

        if($request->category) {

            $category = Category::where('slug',$request->category)->first();

            $products = $products->where('category_id', $category->id);

        }

        if($request->sub_category) {

            $sub_category = SubCategory::where('slug',$request->sub_category)->first();

            $products = $products->where('sub_category_id', $sub_category->id);

        }



        if($request->child_category) {

            $child_category = ChildCategory::where('slug',$request->child_category)->first();

            $products = $products->where('child_category_id', $child_category->id);

        }

        if($request->brand) {

            $brand = Brand::where('slug',$request->brand)->first();

            $products = $products->where('brand_id', $brand->id);

        }



        $brandSortArr = [];

        if($request->brands){

            foreach($request->brands as $brand){

                $brandSortArr[] = $brand;

            }

            $products = $products->whereIn('brand_id', $brandSortArr);

        }



        $categorySortArr = [];

        if($request->categories){

            foreach($request->categories as $brand){
                $categorySortArr[] = $brand;
            }
            $products = $products->whereIn('category_id', $categorySortArr);

        }



        $popularCategoryArr = [];

        if($request->highlight){


            if($request->highlight == 'popular_category'){

                $pop_categories = PopularCategory::all();

                foreach($pop_categories as $pop_category){

                    $popularCategoryArr[] = $pop_category->category_id;

                }

                $products = $products->whereIn('category_id', $popularCategoryArr);

            }



            if($request->highlight == 'top_product'){

                $products = $products->where('is_top',1);

            }



            if($request->highlight == 'new_arrival'){

                $products = $products->where('new_product',1);

            }



            if($request->highlight == 'featured_product'){

                $products = $products->where('is_featured',1);

            }



            if($request->highlight == 'best_product'){

                $products = $products->where('is_best',1);

            }



        }





        if($request->min_price){

            if($request->min_price > 0){

                $products = $products->where('price', '>=', $request->min_price);

            }

        }



        if($request->max_price){

            if($request->max_price > 0){

                $products = $products->where('price', '<=', $request->max_price);

            }

        }



        if($request->shop_name){

            $slug = $request->shop_name;

            $seller = Vendor::where(['slug' => $slug])->first();

            $products = $products->where('vendor_id', $seller->id);

        }



        if($request->search){

            $products = $products->where('name', 'LIKE', '%'. $request->search. "%")

                                ->orWhere('long_description','LIKE','%'.$request->search.'%');

        }



        $products = $products->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best','category_id','sub_category_id','child_category_id','brand_id');

        if($request->per_page){

            $products = $products->paginate($request->per_page);

        }else{

            $products = $products->paginate($paginateQty);

        }



        $products = $products->appends($request->all());



        return response()->json(['products' => $products]);



    }



    public function productDetail($slug){

        $product = Product::with('category','brand','activeVariants.activeVariantItems','avgReview')->where(['status' => 1, 'slug' => $slug])->first();



        if(!$product){

            $notification = trans('user_validation.Something went wrong');

            return response()->json(['message' => $notification],403);

        }



        $paginateQty = CustomPagination::whereId('5')->first()->qty;

        $productReviews = ProductReview::with('user')->where(['status' => 1, 'product_id' =>$product->id])->get()->take(10);



        $totalProductReviewQty = ProductReview::where(['status' => 1, 'product_id' =>$product->id])->count();

        $totalReview = ProductReview::where(['status' => 1, 'product_id' =>$product->id])->sum('rating');

        $recaptchaSetting = GoogleRecaptcha::first();

        $relatedProducts = Product::with('activeVariants.activeVariantItems')->where(['category_id' => $product->category_id, 'status' => 1,'approval_status' => ProductApprovalStatus::APPROVED])->where('id' , '!=', $product->id)->get()->take(10);

        $defaultProfile = BannerImage::whereId('15')->select('image')->first();

        $specifications = ProductSpecification::with('key')->where('product_id', $product->id)->get();

        $gellery = ProductGallery::where('product_id', $product->id)->get();

        $is_seller_product = $product->vendor_id == 0 ? false : true;

        $this_seller_products = [];

        if($is_seller_product){

            $this_seller_products = Product::with('activeVariants.activeVariantItems')->where(['vendor_id' => $product->vendor_id, 'status' => 1, 'approval_status' => ProductApprovalStatus::APPROVED])->where('id' , '!=', $product->id)->get()->take(10);

        }





        $seller = Vendor::with('user')->where('id', $product->vendor_id)->first();

        $sellerTotalProducts = 0;

        if($is_seller_product){

            $sellerTotalProducts = Product::with('activeVariants.activeVariantItems')->where(['status' => 1, 'vendor_id' => $product->vendor_id, 'approval_status' => ProductApprovalStatus::APPROVED])->count();

        }

        $sellerReviewQty = 0;

        if($is_seller_product){

            $sellerReviewQty = ProductReview::where(['status' => 1, 'product_vendor_id' => $product->vendor_id])->count();

        }

        $sellerTotalReview = 0;

        if($is_seller_product){

            $sellerTotalReview = ProductReview::where(['status' => 1, 'product_vendor_id' => $product->vendor_id])->sum('rating');

        }





        $tagArray = json_decode($product->tags);

        $tags = '';

        if($product->tags){

            foreach($tagArray as $index => $tag){

                $tags .= $tag->value.',';

            }

        }





        return response()->json([

            'product' => $product,

            'gellery' => $gellery,

            'tags' => $tags,

            'totalProductReviewQty' => $totalProductReviewQty,

            'totalReview' => $totalReview,

            'productReviews' => $productReviews,

            'specifications' => $specifications,

            'recaptchaSetting' => $recaptchaSetting,

            'relatedProducts' => $relatedProducts,

            'defaultProfile' => $defaultProfile,

            'is_seller_product' => $is_seller_product,

            'seller' => $seller,

            'sellerTotalProducts' => $sellerTotalProducts,

            'this_seller_products' => $this_seller_products,

            'sellerReviewQty' => $sellerReviewQty,

            'sellerTotalReview' => $sellerTotalReview

        ]);

    }





    public function productReviewList($id){

        $reviews = ProductReview::with('user')->where(['product_id' => $id, 'status' => 1])->paginate(10);



        return response()->json(['reviews' => $reviews]);

    }



    public function addToCompare($id){

        $compare_array = [];

        foreach(Cart::instance('compare')->content() as $content){

            $compare_array[] = $content->id;

        }



        if(3 <= Cart::instance('compare')->count()){

            $notification = trans('user_validation.Already 3 items added');

            return response()->json(['status' => 0, 'message' => $notification]);

        }



        if(in_array($id,$compare_array)){

            $notification = trans('user_validation.Already added this item');

            return response()->json(['status' => 0, 'message' => $notification]);

        }else{

            $product = Product::with('tax')->find($id);

            $data=array();

            $data['id'] = $id;

            $data['name'] = 'abc';

            $data['qty'] = 1;

            $data['price'] = 1;

            $data['weight'] = 1;

            $data['options']['product'] = $product;

            Cart::instance('compare')->add($data);

            $notification = trans('user_validation.Item added successfully');

            return response()->json(['status' => 1, 'message' => $notification]);

        }



    }



    public function compare(){

        $banner = BreadcrumbImage::where(['id' => 6])->first();

        $compare_contents = Cart::instance('compare')->content();

        $currencySetting = Setting::first();

        return view('compare', compact('banner','compare_contents','currencySetting'));

    }



    public function removeCompare($id){

        Cart::instance('compare')->remove($id);

        $notification = trans('user_validation.Item remmoved successfully');

        $notification = array('messege'=>$notification,'alert-type'=>'success');

        return redirect()->back()->with($notification);

    }





    public function flashSale(){

        $flashSale = FlashSale::first();

        $flashSaleProducts = FlashSaleProduct::where('status',1)->get();

        $product_arr = [];

        foreach($flashSaleProducts as $flashSaleProduct){

            $product_arr[] = $flashSaleProduct->product_id;

        }



        $paginateQty = CustomPagination::whereId('2')->first()->qty;

        $products = Product::with('activeVariants.activeVariantItems')->whereIn('id', $product_arr)->orderBy('id','desc')->where(['status' => 1, 'approval_status' => ProductApprovalStatus::APPROVED])->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price','is_undefine','is_featured','new_product', 'is_top', 'is_best')->paginate($paginateQty);



        $seoSetting = SeoSetting::find(8);



        return response()->json([

            'flashSale' => $flashSale,

            'products' => $products,

            'seoSetting' => $seoSetting

        ]);

    }



    public function subscribeRequest(Request $request){

        if($request->email != null){

            $isExist = Subscriber::where('email', $request->email)->count();

            if($isExist == 0){

                $subscriber = new Subscriber();

                $subscriber->email = $request->email;

                $subscriber->verified_token = random_int(100000, 999999);

                $subscriber->save();



                MailHelper::setMailConfig();



                // $template=EmailTemplate::where('id',3)->first();

                // $message=$template->description;

                // $subject=$template->subject;
                $message = 'Please use this code to verify your email ';
                $subject = 'Subscription Verification Code';

                Mail::to($subscriber->email)->send(new SubscriptionVerification($subscriber,$message,$subject));



                return response()->json(['message' => trans('user_validation.Subscription successfully, please verified your email')]);



            }else{

                return response()->json(['message' => trans('user_validation.Email already exist'),403],403);

            }

        }else{

            return response()->json(['message' => trans('user_validation.Email Field is required')],403);

        }

    }



    public function subscriberVerifcation(Request $request, $token){



        $subscriber = Subscriber::where(['verified_token' => $token, 'email' => $request->email])->first();

        if($subscriber){

            $subscriber->verified_token = null;

            $subscriber->is_verified = 1;

            $subscriber->save();



            $setting = Setting::first();

            $frontend_url = $setting->frontend_url;



            return redirect($frontend_url);

        }else{

            $setting = Setting::first();

            $frontend_url = $setting->frontend_url;

            return redirect($frontend_url);

        }



    }











































}

