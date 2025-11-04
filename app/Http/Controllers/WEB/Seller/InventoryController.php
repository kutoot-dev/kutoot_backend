<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Setting;
use Auth;
class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }


    public function index(){
        $seller = Auth::guard('web')->user()->seller;

        $products = Product::where(['vendor_id' => $seller->id])->orderBy('id','desc')->get();

        $setting = Setting::first();

        return view('seller.inventory')->with(['products' => $products, 'setting' => $setting]);
    }

    public function show_inventory($id){
        $product = Product::where('id', $id)->first();

        $histories = Inventory::where('product_id', $id)->orderBy('id','desc')->get();

        return view('seller.stock_history')->with(['product' => $product, 'histories' => $histories]);
    }

    public function add_stock(Request $request){
        $rules = [
            'product_id' =>'required',
            'stock_in' =>'required',
        ];
        $customMessages = [
            'product_id.required' => trans('Product is required'),
            'stock_in.required' => trans('Quantity is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $inventory = new Inventory();
        $inventory->product_id = $request->product_id;
        $inventory->stock_in = $request->stock_in;
        $inventory->save();

        $product = Product::where('id', $request->product_id)->first();
        $product->qty = $product->qty + $request->stock_in;
        $product->save();

        $notification=trans('Added Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }

    public function delete_stock($id){
        $inventory = Inventory::find($id);
        $product = Product::where('id', $inventory->product_id)->first();
        $update_qty = $product->qty - $inventory->stock_in;
        $product->qty = $update_qty < 0 ? 0 : $update_qty;
        $product->save();
        $inventory->delete();

        $notification=trans('Deleted Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }


}
