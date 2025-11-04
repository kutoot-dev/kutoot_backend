<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReport;
class ProductReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $reports = ProductReport::with('user','product','seller')->orderBy('id','desc')->get();

        return view('admin.product_report',compact('reports'));
    }

    public function show($id){
        $report = ProductReport::with('user','product','seller')->find($id);
        $product = $report->product;
        $totalReport = ProductReport::where('product_id',$product->id)->count();
        return view('admin.show_product_report',compact('report','totalReport'));
    }

    public function destroy($id){
        $report = ProductReport::find($id);
        $report->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product-report')->with($notification);
    }

    public function deactiveProduct($id){
        $report = ProductReport::find($id);
        $product = $report->product;
        $product->status = 0;
        $product->save();
        $notification=trans('admin_validation.Deactive Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
