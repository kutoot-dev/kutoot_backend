<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReport;
class ProductReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $reports = ProductReport::with('user','product','seller')->orderBy('id','desc')->get();

        return response()->json(['reports' => $reports], 200);
    }

    public function show($id){
        $report = ProductReport::with('user','product','seller')->find($id);
        $product = $report->product;
        $totalReport = ProductReport::where('product_id',$product->id)->count();
        return response()->json(['report' => $report, 'product' => $product, 'totalReport' => $totalReport], 200);
    }

    public function destroy($id){
        $report = ProductReport::find($id);
        $report->delete();
        $notification=trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function deactiveProduct($id){
        $report = ProductReport::find($id);
        $product = $report->product;
        $product->status = 0;
        $product->save();
        $notification=trans('Deactive Successfully');
        return response()->json(['notification' => $notification], 200);
    }
}
