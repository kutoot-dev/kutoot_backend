<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReport;
use Auth;
class SellerProductReportControler extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(){
        $user = Auth::guard('api')->user();
        $seller = $user->seller;
        $reports = ProductReport::with('user','product')->orderBy('id','desc')->where('seller_id',$seller->id)->get();

        return response()->json(['reports' => $reports], 200);
    }

    public function show($id){
        $report = ProductReport::with('user','product')->find($id);
        $product = $report->product;
        $totalReport = ProductReport::where('product_id',$product->id)->count();

        return response()->json(['report' => $report, 'totalReport' => $totalReport], 200);
    }

}
