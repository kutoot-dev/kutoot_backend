<?php

namespace App\Http\Controllers\WEB\Admin;

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
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function dashobard(){
        $todayOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereDay('created_at', now()->day)->get();

        $todayTotalOrder = $todayOrders->count();
        $todayPendingOrder = $todayOrders->where('order_status',0)->count();
        $todayEarning = round($todayOrders->sum('amount_real_currency'),2);
        $todayPendingEarning = round($todayOrders->where('payment_status',0)->sum('amount_real_currency'),2);
        $todayProductSale = $todayOrders->where('order_status',3)->sum('product_qty');


        $totalOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->get();
        $totalOrder = $totalOrders->count();
        $totalPendingOrder = $totalOrders->where('order_status',0)->count();
        $totalDeclinedOrder = $totalOrders->where('order_status',4)->count();
        $totalCompleteOrder = $totalOrders->where('order_status',3)->count();
        $totalEarning = round($totalOrders->sum('amount_real_currency'),2);
        $totalProductSale = $totalOrders->where('order_status',3)->sum('product_qty');



        $monthlyOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereMonth('created_at', now()->month)->get();
        $thisMonthEarning = round($monthlyOrders->sum('amount_real_currency'),2);
        $thisMonthProductSale = $monthlyOrders->where('order_status',3)->sum('product_qty');


        $yearlyOrders = Order::with('user','orderProducts','orderAddress')->orderBy('id','desc')->whereYear('created_at', now()->year)->get();
        $thisYearEarning = round($yearlyOrders->sum('amount_real_currency'),2);
        $thisYearProductSale = $yearlyOrders->where('order_status',3)->sum('product_qty');

        $setting = Setting::first();
        $products = Product::all();
        $reviews = ProductReview::all();
        $reports = ProductReport::all();
        $users = User::all();
        $sellers = Vendor::all();
        $subscribers = Subscriber::where('is_verified',1)->get();
        $blogs = Blog::all();
        $categories = Category::get();
        $brands = Brand::get();


        return view('admin.dashboard')->with([
            'todayOrders' => $todayOrders,
            'todayTotalOrder' => $todayTotalOrder,
            'todayPendingOrder' => $todayPendingOrder,
            'todayEarning' => $todayEarning,
            'todayPendingEarning' => $todayPendingEarning,
            'todayProductSale' => $todayProductSale,
            'thisMonthOrder' => $monthlyOrders->count(),
            'thisMonthEarning' => $thisMonthEarning,
            'thisMonthProductSale' => $thisMonthProductSale,
            'thisYearOrder' => $yearlyOrders->count(),
            'thisYearEarning' => $thisYearEarning,
            'thisYearProductSale' => $thisYearProductSale,
            'totalOrder' => $totalOrder,
            'totalPendingOrder' => $totalPendingOrder,
            'totalDeclinedOrder' => $totalDeclinedOrder,
            'totalCompleteOrder' => $totalCompleteOrder,
            'totalEarning' => $totalEarning,
            'totalProductSale' => $totalProductSale,
            'setting' => $setting,
            'totalProduct' => $products->count(),
            'reviews' => $reviews->count(),
            'reports' => $reports->count(),
            'users' => $users->count(),
            'sellers' => $sellers->count(),
            'subscribers' => $subscribers->count(),
            'blogs' => $blogs->count(),
            'categories' => $categories->count(),
            'brands' => $brands->count()
        ]);




    }


}
