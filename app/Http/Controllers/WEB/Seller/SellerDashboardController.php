<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\Vendor;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Brand;
use App\Models\OrderProduct;
use App\Models\SellerWithdraw;
use Carbon\Carbon;
use Auth;
class SellerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index(){

        $user = Auth::guard('web')->user();
        $seller = $user->seller;

        $todayOrders = Order::with('user')->whereHas('orderProducts',function($query) use ($user){
            $query->where('seller_id', $user->seller->id);
        })->orderBy('id','desc')->whereDay('created_at', now()->day)->get();

        $todayTotalOrder = $todayOrders->where('order_status',0)->count();

        $todayEarning = 0;
        $todayProductSale = 0;
        foreach ($todayOrders as $key => $todayOrder) {
            $orderProducts = $todayOrder->orderProducts->where('seller_id',$seller->id);
            foreach ($orderProducts as $key => $orderProduct) {
                $price = ($orderProduct->unit_price * $orderProduct->qty);
                $todayEarning = $todayEarning + $price;
                $todayProductSale = $todayProductSale + $orderProduct->qty;
            }
        }


        $todayPendingEarning = 0;
        foreach ($todayOrders->where('order_status',0) as $key => $todayOrder) {
            $orderProducts = $todayOrder->orderProducts->where('seller_id',$seller->id);
            foreach ($orderProducts as $key => $orderProduct) {
                $price = ($orderProduct->unit_price * $orderProduct->qty);
                $todayPendingEarning = $todayPendingEarning + $price;
            }
        }




        $totalOrders = Order::with('user')->whereHas('orderProducts',function($query) use ($user){
            $query->where('seller_id', $user->seller->id);
        })->orderBy('id','desc')->get();

        $totalOrder = $totalOrders->count();
        $totalPendingOrder = $totalOrders->where('order_status',0)->count();
        $totalDeclinedOrder = $totalOrders->where('order_status',4)->count();
        $totalCompleteOrder = $totalOrders->where('order_status',3)->count();

        $totalEarning = 0;
        $totalProductSale = 0;
        foreach ($totalOrders as $key => $totalOrder) {
            $orderProducts = $totalOrder->orderProducts->where('seller_id',$seller->id);
            foreach ($orderProducts as $key => $orderProduct) {
                $price = ($orderProduct->unit_price * $orderProduct->qty);
                $totalEarning = $totalEarning + $price;
                $totalProductSale = $totalProductSale + $orderProduct->qty;
            }
        }

        $monthlyOrders = Order::with('user')->whereHas('orderProducts',function($query) use ($user){
            $query->where('seller_id', $user->seller->id);
        })->orderBy('id','desc')->whereMonth('created_at', now()->month)->get();

        $monthlyTotalOrder = $monthlyOrders->count();
        $thisMonthEarning = 0;
        $thisMonthProductSale = 0;
        foreach ($monthlyOrders as $key => $monthlyOrder) {
            $orderProducts = $monthlyOrder->orderProducts->where('seller_id',$seller->id);
            foreach ($orderProducts as $key => $orderProduct) {
                $price = ($orderProduct->unit_price * $orderProduct->qty);
                $thisMonthEarning = $thisMonthEarning + $price;
                $thisMonthProductSale = $thisMonthProductSale + $orderProduct->qty;
            }
        }

        $yearlyOrders = Order::with('user')->whereHas('orderProducts',function($query) use ($user){
            $query->where('seller_id', $user->seller->id);
        })->orderBy('id','desc')->whereYear('created_at', now()->year)->get();

        $yearlyTotalOrder = $yearlyOrders->count();
        $thisYearEarning = 0;
        $thisYearProductSale = 0;
        foreach ($yearlyOrders as $key => $yearlyOrder) {
            $orderProducts = $yearlyOrder->orderProducts->where('seller_id',$seller->id);
            foreach ($orderProducts as $key => $orderProduct) {
                $price = ($orderProduct->unit_price * $orderProduct->qty);
                $thisYearEarning = $thisYearEarning + $price;
                $thisYearProductSale = $thisYearProductSale + $orderProduct->qty;
            }
        }


        $setting = Setting::first();
        $products = Product::where('vendor_id', $seller->id)->get();

        $reviews = ProductReview::where('product_vendor_id', $seller->id)->get();
        $reports = ProductReport::where('seller_id', $seller->id)->get();

        $totalWithdraw = SellerWithdraw::where('seller_id',$seller->id)->where('status',1)->sum('withdraw_amount');
        $totalPendingWithdraw = SellerWithdraw::where('seller_id',$seller->id)->where('status',0)->sum('withdraw_amount');

        return view('seller.dashboard',compact('todayOrders','totalOrders','setting','monthlyOrders','yearlyOrders','products','reviews','reports','seller','totalWithdraw','totalPendingWithdraw'));

        return response()->json([
            'todayTotalOrder' => $todayTotalOrder,
            'todayOrders' => $todayOrders,
            'todayEarning' => $todayEarning,
            'todayPendingEarning' => $todayPendingEarning,
            'todayProductSale' => $todayProductSale,
            'monthlyTotalOrder' => $monthlyTotalOrder,
            'thisMonthEarning' => $thisMonthEarning,
            'thisMonthProductSale' => $thisMonthProductSale,
            'yearlyTotalOrder' => $yearlyTotalOrder,
            'thisYearEarning' => $thisYearEarning,
            'thisYearProductSale' => $thisYearProductSale,
            'totalOrder' => $totalOrder->count(),
            'totalPendingOrder' => $totalPendingOrder,
            'totalDeclinedOrder' => $totalDeclinedOrder,
            'totalCompleteOrder' => $totalCompleteOrder,
            'totalEarning' => $totalEarning,
            'totalProductSale' => $totalProductSale,
            'total_product' => $products->count(),
            'reviews' => $reviews->count(),
            'reports' => $reports->count(),
            'seller' => $seller,
            'totalWithdraw' => $totalWithdraw,
            'totalPendingWithdraw' => $totalPendingWithdraw
        ]);
    }
}
