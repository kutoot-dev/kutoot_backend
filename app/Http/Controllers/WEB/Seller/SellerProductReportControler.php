<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReport;
use Auth;
class SellerProductReportControler extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index(){
        $user = Auth::guard('web')->user();
        $seller = $user->seller;
        $reports = ProductReport::with('user','product')->orderBy('id','desc')->where('seller_id',$seller->id)->get();

        return view('seller.product_report',compact('reports'));
    }

    public function show($id){
        $report = ProductReport::with('user','product')->find($id);
        $product = $report->product;
        $totalReport = ProductReport::where('product_id',$product->id)->count();

        return view('seller.show_product_report',compact('report','totalReport'));
    }

}
