<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Image;
use File;

class FlashSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }


    public function index()
    {
        $flash_sale = FlashSale::first();


        return response()->json(['flash_sale' => $flash_sale], 200);
    }

    public function update(Request $request){
        $rules = [
            'title'=>'required',
            'end_time'=>'required|date',
            'offer'=>'required|numeric',
            'status'=>'required',
        ];
        $customMessages = [
            'title.required' => trans('Title is required'),
            'offer.required' => trans('Offer is required'),
            'end_time.required' => trans('End time is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $flash_sale = FlashSale::first();
        $flash_sale->title = $request->title;
        $flash_sale->offer = $request->offer;
        $flash_sale->end_time = $request->end_time;
        $flash_sale->status = $request->status;
        $flash_sale->save();

        if($request->homepage_image){
            $old_image = $flash_sale->homepage_image;
            $extention=$request->homepage_image->getClientOriginalExtension();
            $image_name = 'flash_sale-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name ='uploads/website-images/'.$image_name;
            $request->homepage_image->move(public_path('uploads/website-images/'),$image_name);
            $flash_sale->homepage_image = $image_name;
            $flash_sale->save();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }

        if($request->flashsale_page_image){
            $old_image = $flash_sale->flashsale_page_image;
            $extention=$request->flashsale_page_image->getClientOriginalExtension();
            $image_name = 'flash_sale-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name ='uploads/website-images/'.$image_name;
            $request->flashsale_page_image->move(public_path('uploads/website-images/'),$image_name);
            $flash_sale->flashsale_page_image = $image_name;
            $flash_sale->save();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }



        $notification=trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }


    public function flash_sale_product(){
        $products = Product::where('status',1)->get();
        $flash_sale_products = FlashSaleProduct::with('product')->get();

        return response()->json(['flash_sale_products' => $flash_sale_products, 'products' => $products], 200);
    }

    public function store(Request $request){

        $isProductExist = FlashSaleProduct::where(['product_id' => $request->product_id])->count();
        $rules = [
            'product_id'=> $isProductExist == 0 ? 'required' : 'required|unique:flash_sale_products',
            'status'=>'required',
        ];
        $customMessages = [
            'product_id.required' => trans('Product is required'),
            'product_id.unique' => trans('Product already exist'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $flash_sale_product = new FlashSaleProduct();
        $flash_sale_product->product_id = $request->product_id;
        $flash_sale_product->status = $request->status;
        $flash_sale_product->save();

        $notification=trans('Added Successfully');
        return response()->json(['message' => $notification], 200);

    }


    public function changeStatus($id){
        $flash_sale_product = FlashSaleProduct::find($id);
        if($flash_sale_product->status==1){
            $flash_sale_product->status=0;
            $flash_sale_product->save();
            $message= trans('Inactive Successfully');
        }else{
            $flash_sale_product->status=1;
            $flash_sale_product->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }

    public function destroy($id)
    {
        $flash_sale_product = FlashSaleProduct::find($id);
        $flash_sale_product->delete();

        $notification=trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }
}
