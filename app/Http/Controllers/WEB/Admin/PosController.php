<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Category;
use App\Models\ShoppingCart;
use App\Models\User;
use App\Models\Coupon;
use App\Models\DeliveryMan;
use App\Models\Shipping;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Helpers\MailHelper;
use App\Models\EmailTemplate;
use App\Models\Country;
use App\Models\City;
use App\Models\CountryState;
use App\Models\ProductVariantItem;
use App\Models\ShoppingCartVariant;
use App\Models\Address;
use App\Models\Brand;
use App\Models\OrderProductVariant;
use App\Models\FlashSaleProduct;
use App\Models\FlashSale;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Mail\UserRegistrationFromAdmin;
use DB;


use Illuminate\Support\Facades\Session;
use Auth;
use Hash;
use Mail;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function Index()
    {
        Paginator::useBootstrap();
        
        $data['brands'] = Brand::all();
        $data['products'] = Product::with('activeVariants')->where(['vendor_id' => 0])->where(['status' => 1])->orderBy('id','desc')->paginate(18);
        $data['setting'] = Setting::first();
        $data['categories'] = Category::with('subCategories','products')->get();
        $data['cart_products'] = ShoppingCart::where('user_id',Auth::guard('admin')->user()->id)->orderBy('id','desc')->get();
        $data['customers'] = User::where('status',1)->select('id','name')->orderBy('id','asc')->get();
        $data['coupon'] = Coupon::where(['code' => 'fdfgdfg', 'status' => 1])->first();
        $data['shippings'] = Shipping::all();
        $data['countries'] = Country::all();
        $data['city'] = City::all();
        $data['state'] = CountryState::all();
        $data['couponValue'] = 'dfgdfg';
        return view('admin.pos.index',$data);
    }

    public function categoryIndex($id)
    {
        Paginator::useBootstrap();
        $data['products'] = Product::with('activeVariants')->where(['vendor_id' => 0])->where(['status' => 1])->where(['category_id' => $id])->orderBy('id','desc')->paginate(18);
        $data['setting'] = Setting::first();
        $data['categories'] = Category::with('subCategories','products')->get();
        $data['cart_products'] = ShoppingCart::where('user_id',Auth::guard('admin')->user()->id)->orderBy('id','desc')->get();
        $data['customers'] = User::where('status',1)->select('id','name')->orderBy('id','asc')->get();
        $data['coupon'] = Coupon::where(['code' => 'fdfgdfg', 'status' => 1])->first();
        $data['shippings'] = Shipping::all();
        $data['countries'] = Country::all();
        $data['city'] = City::all();
        $data['state'] = CountryState::all();
        $data['brands'] = Brand::all();
        $data['couponValue'] = 'dfgdfg';
        return view('admin.pos.index',$data);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        Paginator::useBootstrap();
        $productsQuery = Product::with('activeVariants')->where('vendor_id', 0)
        ->where('status', 1);
            if (!empty($query)) {
            $productsQuery->where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('name', 'like', '%' . $query . '%')
                ->orWhere('short_name', 'like', '%' . $query . '%');
            });
            }
        $data['products'] = $productsQuery->paginate(18);
        $data['setting'] = Setting::first();
        $data['categories'] = Category::with('subCategories','products')->get();
        $data['cart_products'] = ShoppingCart::where('user_id',Auth::guard('admin')->user()->id)->orderBy('id','desc')->get();
        $data['customers'] = User::where('status',1)->select('id','name')->orderBy('id','asc')->get();
        $data['coupon'] = Coupon::where(['code' => 'fdfgdfg', 'status' => 1])->first();
        $data['shippings'] = Shipping::all();
        $data['countries'] = Country::all();
        $data['city'] = City::all();
        $data['state'] = CountryState::all();
        $data['couponValue'] = 'dfgdfg';
        $data['brands'] = Brand::all();
        return view('admin.pos.index',$data);
    }

    public function AddProduct($id)
    {
        if(Auth::guard('admin')->user()){
            $check_stock = Product::where('id',$id)->select('qty')->first();
            $qty = $check_stock->qty;
            if ($qty > 0) {
                if(ShoppingCart::where('product_id', $id)->where('user_id',Auth::guard('admin')->user()->id)->exists())
                {
                    $cart = ShoppingCart::where('product_id', $id)->where('user_id',Auth::guard('admin')->user()->id)->first();
                    $qty = $cart->qty + 1;
                    $datacode= array();
                    $datacode['qty'] = $qty;
                    $code_reg = ShoppingCart::where('product_id',$id)->where('user_id',Auth::guard('admin')->user()->id)
                     ->update($datacode);
                    $notification = trans('admin_validation.Product Quantity Updated');
                    $notification=array('messege'=>$notification,'alert-type'=>'success');
                    return redirect()->back()->with($notification);

                }else{
                    $cart = new ShoppingCart();
                    $cart->user_id = Auth::guard('admin')->user()->id;
                    $cart->product_id = $id;
                    $cart->qty = 1;
                    $cart->save();
                    $notification = trans('admin_validation.Product Added');
                    $notification=array('messege'=>$notification,'alert-type'=>'success');
                    return redirect()->back()->with($notification);
                }
            }else{
                $notification = trans('admin_validation.This Product Are Out of Stock');
                $notification=array('messege'=>$notification,'alert-type'=>'error');
                return redirect()->back()->with($notification);
            }
        }else{
            $notification = trans('admin_validation.Sry First You Need To Login');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

    }

    public function AddProductWithDetils(Request $request, $id)
    {
        $selectedValues = $request->input('selectedValues');
        if(Auth::guard('admin')->user()){
            $check_stock = Product::where('id',$id)->select('qty')->first();
            $qty = $check_stock->qty;
            if ($qty > 0) {
                if(ShoppingCart::where('product_id', $id)->where('user_id',Auth::guard('admin')->user()->id)->exists())
                {
                    $cart = ShoppingCart::where('product_id', $id)->where('user_id',Auth::guard('admin')->user()->id)->first();
                    $qty = $cart->qty + $request->quantity;
                    $datacode= array();
                    $datacode['qty'] = $qty;
                    $code_reg = ShoppingCart::where('product_id',$id)->where('user_id',Auth::guard('admin')->user()->id)
                     ->update($datacode);
                     if ($selectedValues) {
                        foreach ($selectedValues as $variantName => $selectedValue) {
                            $cart_variation = new ShoppingCartVariant();
                            $cart_variation->shopping_cart_id = $cart->id;
                            $cart_variation->variant_id = $variantName;
                            $cart_variation->variant_item_id = $selectedValue;
                            $cart_variation->save();
                        }
                    }

                    $notification = trans('admin_validation.Product Quantity Updated');
                    $notification=array('messege'=>$notification,'alert-type'=>'success');
                    return redirect()->back()->with($notification);

                }else{
                    $cart = new ShoppingCart();
                    $cart->user_id = Auth::guard('admin')->user()->id;
                    $cart->product_id = $id;
                    $cart->qty = $request->quantity;
                    $cart->save();
                    if ($selectedValues) {
                        foreach ($selectedValues as $variantName => $selectedValue) {
                            $cart_variation = new ShoppingCartVariant();
                            $cart_variation->shopping_cart_id = $cart->id;
                            $cart_variation->variant_id = $variantName;
                            $cart_variation->variant_item_id = $selectedValue;
                            $cart_variation->save();
                        }
                    }
                    $notification = trans('admin_validation.Product Added');
                    $notification=array('messege'=>$notification,'alert-type'=>'success');
                    return redirect()->back()->with($notification);
                }
            }else{
                $notification = trans('admin_validation.This Product Are Out of Stock');
                $notification=array('messege'=>$notification,'alert-type'=>'error');
                return redirect()->back()->with($notification);
            }
        }else{
            $notification = trans('admin_validation.Sry First You Need To Login');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

    }

    public function cartIncremet($id) {

        $check = ShoppingCart::where('id',$id)->first();
        if($check->user_id == Auth::guard('admin')->user()->id){
            $incress_qty = $check->qty + 1;
            $datacode= array();
                     $datacode['qty'] = $incress_qty;
                     $code_reg = ShoppingCart::where('id',$id)
                      ->update($datacode);

            $notification = trans('admin_validation.Product Quantity Updated');
            $notification=array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }else{
            $notification = trans('admin_validation.Sry Somthin Went To Wrong');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

    }

    public function cartDecrement($id) {
        $check = ShoppingCart::where('id',$id)->first();
        if($check->user_id == Auth::guard('admin')->user()->id && 1 < $check->qty){
            $incress_qty = $check->qty - 1;
            $datacode= array();
                     $datacode['qty'] = $incress_qty;
                     $code_reg = ShoppingCart::where('id',$id)
                      ->update($datacode);

            $notification = trans('admin_validation.Product Quantity Updated');
            $notification=array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }else{
            $notification = trans('admin_validation.Sry Somthin Went To Wrong');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

    }



    public function Destroy($id) {
        $check = ShoppingCart::where('id',$id)->first();
        if($check->user_id == Auth::guard('admin')->user()->id){
            $varient_data = ShoppingCartVariant::where('shopping_cart_id',$id);
            $varient_data -> delete();
            $data = ShoppingCart::where('id',$id);
            $data->delete();
            $notification = trans('admin_validation.Product Removed Successfully');
            $notification=array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }else{
            $notification = trans('admin_validation.Sry Somthin Went To Wrong');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }


    }

    public function clearCart() {
        $data = ShoppingCart::where('user_id',Auth::guard('admin')->user()->id);
        $data -> delete();
        $notification = trans('admin_validation.Product Cart Clear Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.pos.index')->with($notification);

    }

    public function addCustomer(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'email.required' => trans('admin_validation.Email is required'),
            'email.unique' => trans('admin_validation.Email already exist'),
            'phone.required' => trans('admin_validation.Phone Number is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $user = new User();
        $user->name =$request->name;
        $user->email =$request->email;
        $user->phone =$request->phone;
        $user->country_id =$request->country;
        $user->state_id =$request->State;
        $user->city_id =$request->city;
        $user->address =$request->address;
        $user->status = 1;
        $user->password =Hash::make(1234);
        if($user->save()){
            $address = new Address();
            $address->user_id =$user->id;
            $address->name =$request->name;
            $address->email =$request->email;
            $address->phone =$request->phone;
            $address->country_id =$request->country;
            $address->state_id =$request->state;
            $address->city_id =$request->city;
            $address->address =$request->address;
            $address->type =$request->location;
            $address->default_shipping = 1;
            $address->default_billing = 1;
            $address->save();

            
            MailHelper::setMailConfig();
            $template=EmailTemplate::where('id',9)->first();
            $subject=$template->subject;
            $message=$template->description;
            $message = str_replace('{{user_name}}',$request->name,$message);
            Mail::to($user->email)->send(new UserRegistrationFromAdmin($message,$subject,$user));

            $notification = trans('admin_validation.Customer Create Successfully');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }else{
            $notification = trans('admin_validation.Customer Create Not Successfully');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }
    }
    public function applyCupon(Request $request)
    {
        $couponValue = $request->query('coupon');
        if($request->coupon == null){
            $notification = trans('admin_validation.Coupon Field is required');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }


        $data['coupon'] = Coupon::where(['code' => $request->coupon, 'status' => 1])->first();

        if(!$data['coupon']){
            $notification = trans('admin_validation.Invalid Coupon');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        if($data['coupon']->expired_date < date('Y-m-d')){
            $notification = trans('admin_validation.Coupon already expired');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        if($data['coupon']->apply_qty >=  $data['coupon']->max_quantity ){
            $notification = trans('admin_validation.Sorry! You can not apply this coupon');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        Paginator::useBootstrap();
        $data['products'] = Product::with('activeVariants')->where(['vendor_id' => 0])->where(['status' => 1])->orderBy('id','desc')->paginate(18);
        $data['setting'] = Setting::first();
        $data['categories'] = Category::with('subCategories','products')->get();
        $data['cart_products'] = ShoppingCart::where('user_id',Auth::guard('admin')->user()->id)->orderBy('id','desc')->get();
        $data['customers'] = User::where('status',1)->select('id','name')->orderBy('id','asc')->get();
        $data['shippings'] = Shipping::all();
        $data['couponValue'] = $request->query('coupon');
        $data['countries'] = Country::all();
        $data['city'] = City::all();
        $data['brands'] = Brand::all();
        $data['state'] = CountryState::all();
        return view('admin.pos.index',$data);
    }

    public function orderSubmit(Request $request){

        $admin_id = Auth::guard('admin')->user()->id;
        $total_price = 0;
        $sub_total = $request->sub_total;
        $discount = $request->discount;
        $cupon = $request->cupon;
        $tax = $request->tax;
        $customer_id = $request->customer_id;
        $shipping_id = $request->shipping_id;
        $payment_method = $request->payment_method;
        $order_status = $request->order_status;
        if($request->payment_method == 'Cash'){
            $paymetn_status = 1;
        }else{
            $paymetn_status = 0;
        }

        $cartProducts = ShoppingCart::with("product", "variants.variantItem")
        ->where("user_id", $admin_id)
        ->select("id", "product_id", "qty")
        ->get();

    if ($cartProducts->count() == 0) {
        $notification = trans('admin_validation.Your shopping cart is empty');
        $notification = array('messege'=>$notification,'alert-type'=>'error');
        return redirect()->back()->with($notification);
    }

    $check_cupon = Coupon::where(['code' => $cupon, 'status' => 1])->first();
        if($check_cupon){

            $datacode= array();
            $datacode['apply_qty'] = $check_cupon->apply_qty + 1;
            $code_reg = Coupon::where(['code' => $cupon, 'status' => 1])
            ->update($datacode);
        }

    $shipping = Shipping::find($shipping_id);
        if(!$shipping){
            $notification = trans('admin_validation.Shipping method not found');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }
        if($shipping->shipping_fee == 0){
            $shipping_fee = 0;
        }else{
            $shipping_fee = $shipping->shipping_fee;
        }

        $total_price = ($sub_total - $discount) + $shipping_fee + $tax;

        $totalProduct = ShoppingCart::where('user_id', $admin_id)->sum('qty');

        $order = new Order();

        $orderId = substr(rand(0, time()), 0, 10);
        $order->order_id = $orderId;
        $order->user_id = $customer_id;
        $order->total_amount = $total_price;
        $order->product_qty = $totalProduct;
        $order->payment_method = $payment_method;
        $order->transection_id = $payment_method;
        $order->payment_status = $paymetn_status;
        $order->shipping_method = $shipping->shipping_rule;
        $order->shipping_cost = $shipping_fee;
        $order->coupon_coast = $discount;
        $order->order_status = $order_status;
        $order->cash_on_delivery = $order_status;
        $order->save();
        $order_details = "";

        $setting = Setting::first();
        foreach ($cartProducts as $key => $cartProduct) {
            $variantPrice = 0;
            if ($cartProduct->variants) {
                foreach ($cartProduct->variants as $item_index => $var_item) {
                    $item = ProductVariantItem::find(
                        $var_item->variant_item_id
                    );
                    if ($item) {
                        $variantPrice += $item->price;
                    }
                }
            }

            // calculate product price

            $product = Product::select(
                "id",
                "price",
                "offer_price",
                "weight",
                "vendor_id",
                "qty",
                "name"
            )->find($cartProduct->product_id);

            $price = $product->offer_price
                ? $product->offer_price
                : $product->price;

            $price = $price + $variantPrice;
            $isFlashSale = FlashSaleProduct::where([
                "product_id" => $product->id,
                "status" => 1,
            ])->first();

            $today = date("Y-m-d H:i:s");
            if ($isFlashSale) {
                $flashSale = FlashSale::first();
                if ($flashSale->status == 1) {
                    if ($today <= $flashSale->end_time) {
                        $offerPrice = ($flashSale->offer / 100) * $price;

                        $price = $price - $offerPrice;
                    }
                }
            }

            // store ordre product

            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $cartProduct->product_id;
            $orderProduct->seller_id = $product->vendor_id;
            $orderProduct->product_name = $product->name;
            $orderProduct->unit_price = $price;
            $orderProduct->qty = $cartProduct->qty;
            $orderProduct->save();

            // update product stock

            $qty = $product->qty - $cartProduct->qty;
            $product->qty = $qty;
            $product->save();

            // store prouct variant

            // return $cartProduct->variants;
            foreach ($cartProduct->variants as $index => $variant) {
                $item = ProductVariantItem::find($variant->variant_item_id);
                $productVariant = new OrderProductVariant();
                $productVariant->order_product_id = $orderProduct->id;
                $productVariant->product_id = $cartProduct->product_id;
                $productVariant->variant_name = $item->product_variant_name;
                $productVariant->variant_value = $item->name;
                $productVariant->save();
            }

            $order_details .= "Product: " . $product->name . "<br>";
            $order_details .= "Quantity: " . $cartProduct->qty . "<br>";
            $order_details .=
                "Price: " .
                $setting->currency_icon .
                $cartProduct->qty * $price .
                "<br>";
        }

         // store shipping and billing address

         $billing = Address::where('user_id',$request->customer_id)->first();
         $shipping = Address::where('user_id',$request->customer_id)->first();
         $orderAddress = new OrderAddress();
         $orderAddress->order_id = $order->id;
         $orderAddress->billing_name = $billing->name;
         $orderAddress->billing_email = $billing->email;
         $orderAddress->billing_phone = $billing->phone;
         $orderAddress->billing_address = $billing->address;
         $orderAddress->billing_country = $billing->country->name;
         $orderAddress->billing_state = $billing->countryState->name;
         $orderAddress->billing_city = $billing->city->name;
         $orderAddress->billing_address_type = $billing->type;
         $orderAddress->shipping_name = $shipping->name;
         $orderAddress->shipping_email = $shipping->email;
         $orderAddress->shipping_phone = $shipping->phone;
         $orderAddress->shipping_address = $shipping->address;
         $orderAddress->shipping_country = $shipping->country->name;
         $orderAddress->shipping_state = $shipping->countryState->name;
         $orderAddress->shipping_city = $shipping->city->name;
         $orderAddress->shipping_address_type = $shipping->type;
         $orderAddress->save();

         foreach ($cartProducts as $cartProduct) {
             ShoppingCartVariant::where(
                 "shopping_cart_id",
                 $cartProduct->id
             )->delete();

             $cartProduct->delete();
         }

        $notification = trans('admin_validation.Order Created SuccesFully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.pos.index')->with($notification);

    }


    public function bulkOrder(){
        return view('admin.pos.bulk_order');
    }

    public function bulkOrderSerch(Request $request){
        // Define the form input parameters
        $from = $request->form;
        $to = $request->to;
        $payment_status = $request->payment_status;
        $order_status = $request->order_status;

        // Start building the query
        $query = Order::query();

        // Apply filters based on form inputs
        if (!empty($from) && !empty($to)) {
            // If both "from" and "to" dates are provided, filter by date range
            $query->whereBetween('created_at', [$from, $to]);
        }

        if (!empty($payment_status)) {
            // Filter by payment_status if provided
            $query->where('payment_status', $payment_status);
        }

        if (!empty($order_status)) {
            // Filter by order_status if provided
            $query->where('order_status', $order_status);
        }

        // Execute the query
        $filteredOrders = $query->get();
        $setting = Setting::first();

        return view('admin.pos.bulk_order_change',compact('filteredOrders','setting'));
    }

    public function updateOrderStatus(Request $request)
    {
        if($request->orderIds == ''){
            $notification = trans('admin_validation.You Not Select Any Order');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        if($request->newStatus == ''){
            $notification = trans('admin_validation.You Not Select Any status');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

        $validatedData = $request->validate([
            'newStatus' => 'required',
            'orderIds' => 'required|array',
        ]);

        $newStatus = $validatedData['newStatus'];
        $orderIds = $validatedData['orderIds'];

        try {
            Order::whereIn('id', $orderIds)->update(['order_status' => $newStatus]);

            $notification = trans('admin_validation.Order statuses updated successfully');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->route('admin.pos.bulk.order')->with($notification);
        } catch (\Exception $e) {
            $notification = trans('admin_validation.Error updating order statuses');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('admin.pos.bulk.order')->with($notification);
        }
    }

    public function updatePosCart(Request $request)
    {
    $rules = [
        'qty_update.*' => ['required', 'integer', 'min:1'],
    ];

    $messages = [
        'qty_update.*.required' => 'admin_validation.Quantity is required for all products.',
        'qty_update.*.integer' => 'admin_validation.Quantity must be an integer.',
        'qty_update.*.min' => 'admin_validation.Quantity cannot be negative.',
    ];
    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

        $qtyUpdate = $request->input('qty_update');

        if(!$qtyUpdate)
        {
            $notification = trans('admin_validation.Please add product first.');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }
        foreach ($qtyUpdate as $productId => $quantity) {
            $cartProduct = ShoppingCart::find($productId);
            $cartProduct->qty = $quantity;
            $cartProduct->save();
        }
        $notification = trans('admin_validation.Update Order Quantity');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }



}
