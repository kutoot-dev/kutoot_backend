<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Nnjeim\World\World;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\OrderProduct;
use App\Models\Wishlist;
use App\Models\ProductReport;
use App\Models\GoogleRecaptcha;
use App\Models\BannerImage;
use App\Models\User;
use App\Models\CompareProduct;
use App\Rules\Captcha;
use Image;
use File;
use Str;
use Hash;
use Slug;
use App\Models\PurchasedCoins;
use App\Models\UserCoupons;
use App\Models\UserCoins;

use App\Events\SellerToUser;

use App\Models\OrderAddress;
use App\Models\OrderProductVariant;
use App\Models\Address;

use App\Models\ShoppingCart;
use App\Models\ShoppingCartVariant;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function remove_account(){
        $user = Auth::guard('api')->user();
        $id = $user->id;
        $orders = Order::where('user_id', $user->id)->get();
        foreach($orders as $order){
            OrderAddress::where(['order_id' => $order->id])->delete();
            $orderProducts = OrderProduct::where('order_id',$id)->get();
            foreach($orderProducts as $orderProduct){
                OrderProductVariant::where('order_product_id',$orderProduct->id)->delete();
                $orderProduct->delete();
            }
            $order->delete();
        }

        ProductReport::where('user_id',$id)->delete();
        ProductReview::where('user_id',$id)->delete();
        Address::where('user_id',$id)->delete();
        Wishlist::where('user_id',$id)->delete();
        CompareProduct::where('user_id',$id)->delete();

        $cart_items = ShoppingCart::where(['user_id' => $user->id])->get();
        foreach($cart_items as $cart_item){
            ShoppingCartVariant::where(['shopping_cart_id' => $cart_item->id])->delete();
            $cart_item->delete();
        }

        $user = User::find($id);
        $user_image = $user->image;
        if($user_image){
            if(File::exists(public_path().'/'.$user_image))unlink(public_path().'/'.$user_image);
        }
        $user->delete();

        return response()->json(['message' => trans('Your account has been successfully removed')]);
    }

    public function dashboard(){
        $user = Auth::guard('api')->user();
        $orders = Order::where('user_id',$user->id)->get();
        $totalOrder = $orders->count();
        $completeOrder = $orders->where('order_status',3)->count();
        $pendingOrder = $orders->where('order_status',0)->count();
        $declinedOrder = $orders->where('order_status',4)->count();

        $personInfo = User::select('id','name','phone','email','image','country_id','state_id','city_id','zip_code','address')->find($user->id);
        $sellerInfo = Vendor::select('id','user_id','banner_image','phone','email','shop_name','slug','open_at','closed_at','address')->where('user_id', $personInfo->id)->first();
        $is_seller = $sellerInfo ? true : false;

        return response()->json([
            'personInfo' => $personInfo,
            'is_seller' => $is_seller,
            'sellerInfo' => $sellerInfo,
            'totalOrder' => $totalOrder,
            'completeOrder' => $completeOrder,
            'pendingOrder' => $pendingOrder,
            'declinedOrder' => $declinedOrder,
        ]);
    }


    public function order(){
        $user = Auth::guard('api')->user();
        $orders = Order::orderBy('id','desc')->where('user_id', $user->id)->paginate(10);

        return response()->json(['orders' => $orders]);
    }

    public function pendingOrder(){
        $user = Auth::guard('api')->user();
        $orders = Order::orderBy('id','desc')->where('user_id', $user->id)->where('order_status',0)->paginate(10);

        return response()->json(['orders' => $orders]);
    }

    public function completeOrder(){
        $user = Auth::guard('api')->user();
        $orders = Order::orderBy('id','desc')->where('user_id', $user->id)->where('order_status',3)->paginate(10);

        return response()->json(['orders' => $orders]);
    }

    public function declinedOrder(){
        $user = Auth::guard('api')->user();
        $orders = Order::orderBy('id','desc')->where('user_id', $user->id)->where('order_status',4)->paginate(10);
        $setting = Setting::first();
        return response()->json(['orders' => $orders]);
    }

    public function orderShow($orderId){
        $user = Auth::guard('api')->user();
        $order = Order::with('orderProducts.orderProductVariants','orderAddress')->where('user_id', $user->id)->where('order_id',$orderId)->first();

        return response()->json(['order' => $order]);
    }


    public function wishlist(){
        $user = Auth::guard('api')->user();
        $wishlists = Wishlist::with('product')->where(['user_id' => $user->id])->paginate(10);

        return response()->json(['wishlists' => $wishlists]);
    }


    public function v1updateProfile(Request $request)
    {
        $rules = [
            'identifier' => 'required',
            'name' => 'required|string|max:255',
        ];

        $customMessages = [
            'identifier.required' => trans('user_validation.Email or phone is required'),
            'name.required' => trans('user_validation.Full name is required'),
        ];

        $this->validate($request, $rules, $customMessages);

        $identifier = $request->identifier;
        $name = $request->name;

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $existuser = User::where('email', $identifier)->first();

            if (!$existuser) {
                return response()->json(['message' => trans('Already Email ID exists')], 404);
            }

        } elseif (is_numeric($identifier)) {
            $existuser = User::where('phone', $identifier)->first();

            if (!$existuser) {
                return response()->json(['message' => trans('Already Phone number exists')], 404);
            }

        } else {
            return response()->json(['message' => trans('user_validation.Invalid identifier')], 422);
        }


        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['message' => trans('user_validation.User not found')], 404);
        }

        // Optional: Check if user is verified
        // if ($user->email_verified != 1) {
        //     return response()->json(['message' => trans('user_validation.Please verify your account first')], 403);
        // }

        // Update name and re-confirm identifier (email/phone) if needed
        $user->name = $name;

        // Optional: if identifier was just temporary, allow user to update it here
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user->email = $identifier;
        } else {
            $user->phone = $identifier;
        }

        $user->email_verified=1;
        $user->is_completed=1;
        $user->save();

        return response()->json([
            'message' => trans('user_validation.Profile updated successfully'),
            'user' => $user
        ], 200);
    }

public function newupdateProfile(Request $request)
{


    $rules = [
        'identifier'   => 'required',
        'name'         => 'required|string|max:255',
        'image'        => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'gender'       => 'required|in:male,female,other',
        'phone'        => 'required|string|max:20',
        'address'      => 'required|string|max:255',
        'house_no'     => 'required|string|max:50',
        'street'       => 'required|string|max:255',
        'city_id'      => 'required|integer|exists:cities,id',
        'state_id'     => 'required|integer|exists:states,id',
        'country_id'   => 'required|integer|exists:countries,id',
        'zip_code'     => 'required|string|max:20',
    ];

    $customMessages = [
        'identifier.required' => trans('user_validation.Email or phone is required'),
        'name.required'       => trans('user_validation.Full name is required'),
    ];

    // $this->validate($request, $rules, $customMessages);

     $validator = Validator::make($request->all(), $rules, $customMessages);

   if ($validator->fails()) {
        return response()->json([
            'message' => 'Please enter all the required fields',
            'errors'  => $validator->errors()
        ], 422);
    }


    $identifier = $request->identifier;
    $name       = $request->name;

    // Check if identifier exists
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $existuser = User::where('email', $identifier)->first();

        if (!$existuser) {
            return response()->json(['message' => trans('Already Email ID exists')], 404);
        }
    } elseif (is_numeric($identifier)) {
        $existuser = User::where('phone', $identifier)->first();

        if (!$existuser) {
            return response()->json(['message' => trans('Already Phone number exists')], 404);
        }
    } else {
        return response()->json(['message' => trans('user_validation.Invalid identifier')], 422);
    }

    $user = Auth::guard('api')->user();

    if (!$user) {
        return response()->json(['message' => trans('user_validation.User not found')], 404);
    }

    // Update basic info
    $user->name = $name;

    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $user->email = $identifier;
    } else {
        $user->phone = $identifier;
    }

    // Extra profile fields
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('uploads/users', 'public');
        $user->image = $path;
    }

    $user->gender     = $request->gender;
    $user->phone      = $request->phone ?? $user->phone; // keep existing if not passed
    $user->address    = $request->address;
    $user->house_no   = $request->house_no;
    $user->street     = $request->street;
    $user->city_id    = $request->city_id;
    $user->state_id   = $request->state_id;
    $user->country_id = $request->country_id;
    $user->zip_code   = $request->zip_code;

    // Flags
    $user->email_verified = 1;
    $user->is_completed   = 1;

    $user->save();

    return response()->json([
        'message' => trans('user_validation.Profile updated successfully'),
        'user'    => $user
    ], 200);
}






    public function myProfile(){
        $user = Auth::guard('api')->user();
        $personInfo = User::select('id','name','email','phone','gender','house_no','street','image','country_id','state_id','city_id','zip_code','address')->find($user->id);

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

        $defaultProfile = BannerImage::select('id','image')->whereId('15')->first();

        // $creditCoins = UserCoins::where('user_id', $user->id)->where('type', 'credit')->sum('coins');

        // $debitCoins = UserCoins::where('user_id', $user->id)->where('type', 'debit')->sum('coins');

        // $balanceCoins = $creditCoins-$debitCoins;

        $coins = UserCoins::selectRaw("
                SUM(CASE WHEN type = 'credit' THEN coins ELSE 0 END) as credit,
                SUM(CASE WHEN type = 'debit' THEN coins ELSE 0 END) as debit
            ")
            ->where('user_id', $user->id)
            ->whereDate('coin_expires', '>=', now()->toDateString())
            ->first();

        $creditCoins = $coins->credit ?? 0;
        $debitCoins = $coins->debit ?? 0;
        $balanceCoins = $creditCoins - $debitCoins;


        $activeCoupons =UserCoupons::whereHas('purchasedCampaign', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->where('is_claimed', 0)
                    ->where('status', 1)
                    ->whereDate('coupon_expires', '>=', now())
                    ->count();


        return response()->json([
            'personInfo' => $personInfo,
            'countries' => $countries,
            'credit_coins' => intval($creditCoins),
            'debit_coins' => intval($debitCoins),
            'balance_coins' => $balanceCoins,
            'active_prize_coupons' => $activeCoupons,
            'states' => $states,
            'cities' => $cities,
            'defaultProfile' => $defaultProfile
        ]);
    }

    public function updateProfile(Request $request){
        $user = Auth::guard('api')->user();

        $rules = [
            'name'    => 'required',
            'email'   => 'required|unique:users,email,' . $user->id,
            'phone'   => 'required',
            'country' => 'nullable',
            'address' => 'nullable',
            'gender'  => 'nullable|in:male,female,other',
            'zip_code' => 'nullable|string|max:10',
        ];

        $customMessages = [
            'name.required'  => trans('user_validation.Name is required'),
            'email.required' => trans('user_validation.Email is required'),
            'email.unique'   => trans('user_validation.Email already exist'),
            'phone.required' => trans('user_validation.Phone is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->country_id = $request->country;
        $user->state_id = $request->state;
        $user->city_id = $request->city;
        $user->address = $request->address;
        $user->zip_code = $request->zip_code;
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

        $notification = trans('user_validation.Update Successfully');
        return response()->json(['notification' => $notification]);
    }


    public function updatePassword(Request $request){

        $rules = [
            'current_password'=>'required',
            'password'=>'required|min:4|confirmed',
        ];

        $customMessages = [
            'current_password.required' => trans('user_validation.Current password is required'),
            'password.required' => trans('user_validation.Password is required'),
            'password.min' => trans('user_validation.Password minimum 4 character'),
            'password.confirmed' => trans('user_validation.Confirm password does not match'),
        ];

        $this->validate($request, $rules,$customMessages);

        $user = Auth::guard('api')->user();
        if(Hash::check($request->current_password, $user->password)){
            $user->password = Hash::make($request->password);
            $user->save();
            $notification = 'Password change successfully';
            return response()->json(['notification' => $notification]);
        }else{
            $notification = trans('user_validation.Current password does not match');
            return response()->json(['notification' => $notification],403);
        }

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

    public function sellerRegistration(){
        $setting = Setting::first();
        return response()->json(['setting' => $setting]);
    }

    public function sellerRequest(Request $request){

        $user = Auth::guard('api')->user();
        $seller = Vendor::where('user_id',$user->id)->first();

        if($seller){
            $notification = 'Request Already exist';
            return response()->json(['notification' => $notification],400);
        }

        $rules = [
            'banner_image'=>'required',
            'logo'=>'required',
            'shop_name'=>'required|unique:vendors',
            'email'=>'required|unique:vendors',
            'phone'=>'required',
            'address'=>'required',
            'open_at'=>'required',
            'closed_at'=>'required',
            'agree_terms_condition' => 'required'
        ];

        $customMessages = [
            'logo.required' => trans('user_validation.Logo is required'),
            'banner_image.required' => trans('user_validation.Banner image is required'),
            'shop_name.required' => trans('user_validation.Shop name is required'),
            'shop_name.unique' => trans('user_validation.Shop name already exist'),
            'email.required' => trans('user_validation.Email is required'),
            'email.unique' => trans('user_validation.Email already exist'),
            'phone.required' => trans('user_validation.Phone is required'),
            'address.required' => trans('user_validation.Address is required'),
            'open_at.required' => trans('user_validation.Open at is required'),
            'closed_at.required' => trans('user_validation.Close at is required'),
            'agree_terms_condition.required' => trans('user_validation.Agree field is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = Auth::guard('api')->user();
        $seller = new Vendor();
        $seller->shop_name = $request->shop_name;
        $seller->slug = Str::slug($request->shop_name);
        $seller->email = $request->email;
        $seller->phone = $request->phone;
        $seller->address = $request->address;
        $seller->greeting_msg = trans('user_validation.Welcome to'). ' '. $request->shop_name;
        $seller->open_at = $request->open_at;
        $seller->closed_at = $request->closed_at;
        $seller->user_id = $user->id;
        $seller->seo_title = $request->shop_name;
        $seller->seo_description = $request->shop_name;

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

        if($request->logo){
            $extention = $request->logo->getClientOriginalExtension();
            $banner_name = 'seller-logo'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/custom-images/'.$banner_name;
            Image::make($request->logo)
                ->save(public_path().'/'.$banner_name);
            $seller->logo = $banner_name;
            $seller->save();

        }

        $seller->save();
        $notification = trans('user_validation.Request submited successfully');
        return response()->json(['notification' => $notification],200);

    }

    public function addToWishlist($id){
        $user = Auth::guard('api')->user();
        $product = Product::find($id);
        $isExist = Wishlist::where(['user_id' => $user->id, 'product_id' => $product->id])->count();
        if($isExist == 0){
            $wishlist = new Wishlist();
            $wishlist->product_id = $id;
            $wishlist->user_id = $user->id;
            $wishlist->save();
            $message = trans('user_validation.Wishlist added successfully');
            return response()->json(['message' => $message]);
        }else{
            $message = trans('user_validation.Product Already added');
            return response()->json(['message' => $message],403);
        }
    }

    public function removeWishlist($id){
        $wishlist = Wishlist::find($id);
        if($wishlist){
           $wishlist->delete();
        }

        $notification = trans('user_validation.Removed successfully');
        return response()->json(['notification' => $notification]);
    }

    public function clearWishlist(){
        $user = Auth::guard('api')->user();
        Wishlist::where(['user_id' => $user->id])->delete();

        $notification = trans('user_validation.Clear successfully');
        return response()->json(['notification' => $notification]);
    }



    public function storeProductReport(Request $request){

        $rules = [
            'subject'=>'required',
            'description'=>'required',
            'product_id'=>'required',
        ];

        $customMessages = [
            'subject.required' => trans('user_validation.Subject filed is required'),
            'description.required' => trans('user_validation.Description filed is required'),
            'product_id.required' => trans('user_validation.Product is required')
        ];
        $this->validate($request, $rules,$customMessages);

        $product = Product::find($request->product_id);
        $user = Auth::guard('api')->user();
        $report = new ProductReport();
        $report->user_id = $user->id;
        $report->seller_id = $product->vendor_id;
        $report->product_id = $request->product_id;
        $report->subject = $request->subject;
        $report->description = $request->description;
        $report->save();

        $message = trans('user_validation.Report Submited successfully');
        return response()->json(['message' => $message]);

    }

    public function review(){
        $user = Auth::guard('api')->user();
        $reviews = ProductReview::with('product')->orderBy('id','desc')->where(['user_id' => $user->id])->paginate(10);

        return response()->json(['reviews' => $reviews]);
    }

    public function showReview($id){
        $user = Auth::guard('api')->user();
        $review = ProductReview::with('product')->where(['user_id' => $user->id, 'status' => 1, 'id' => $id])->first();

        return response()->json(['review' => $review]);
    }

    public function storeProductReview(Request $request){
        $rules = [
            'rating'=>'required',
            'review'=>'required',
            'product_id'=>'required',
            'g-recaptcha-response'=>new Captcha()
        ];
        $customMessages = [
            'rating.required' => trans('user_validation.Rating is required'),
            'review.required' => trans('user_validation.Review is required'),
            'product_id.required' => trans('user_validation.Product is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = Auth::guard('api')->user();
        $isExistOrder = false;
        $orders = Order::where(['user_id' => $user->id])->get();
        foreach ($orders as $key => $order) {
            foreach ($order->orderProducts as $key => $orderProduct) {
                if($orderProduct->product_id == $request->product_id){
                    $isExistOrder = true;
                }
            }
        }

        if($isExistOrder){
            $isReview = ProductReview::where(['product_id' => $request->product_id, 'user_id' => $user->id])->count();
            if($isReview > 0){
                $message = trans('user_validation.You have already submited review');
                return response()->json(['message' => $message],403);
            }

            $product = Product::find($request->product_id);
            $review = new ProductReview();
            $review->user_id = $user->id;
            $review->rating = $request->rating;
            $review->review = $request->review;
            $review->product_vendor_id = $product->vendor_id;
            $review->product_id = $request->product_id;
            $review->save();
            $message = trans('user_validation.Review Submited successfully');
            return response()->json(['message' => $message]);
        }else{
            $message = trans('user_validation.Opps! You can not review this product');
            return response()->json(['message' => $message],403);
        }

    }

    public function compareProducts(){
        $user = Auth::guard('api')->user();
        $compareProducts = CompareProduct::where('user_id', $user->id)->get();

        $product_arr = [];
        foreach($compareProducts as $compareProduct){
            $product_arr[] = $compareProduct->product_id;
        }

        $products = Product::whereIn('id', $product_arr)->with('specifications.key','activeVariants.activeVariantItems')->where(['status' => 1])->select('id','name', 'short_name', 'slug', 'thumb_image','qty','sold_qty', 'price', 'offer_price')->get();


        return response()->json(['products' => $products]);
    }

    public function addCompareProducts($id){
        $user = Auth::guard('api')->user();

        $total =CompareProduct::where(['user_id' => $user->id])->count();

        if(3 <= $total){
            $notification = trans('user_validation.Already 3 items added');
            return response()->json(['notification' => $notification],403);
        }

        $isExist = CompareProduct::where(['user_id' => $user->id, 'product_id' => $id])->count();

        if($isExist == 0){
            $compare = new CompareProduct();
            $compare->user_id = $user->id;
            $compare->product_id = $id;
            $compare->save();

            $notification = trans('user_validation.Item added successfully');
            return response()->json(['notification' => $notification]);
        }else{
            $notification = trans('user_validation.Already added this item');
            return response()->json(['notification' => $notification],403);
        }

        return response()->json(['compareProducts' => $compareProducts]);
    }


    public function deleteCompareProduct($id){
        $user = Auth::guard('api')->user();
        CompareProduct::where(['user_id' => $user->id, 'product_id' => $id])->delete();

        $notification = trans('user_validation.Item remmoved successfully');
        return response()->json(['notification' => $notification]);
    }




}
