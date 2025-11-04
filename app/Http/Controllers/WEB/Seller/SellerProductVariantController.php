<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariantItem;
use App\Models\ShoppingCartVariant;
class SellerProductVariantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index($productId)
    {
        $product = Product::find($productId);
        if($product){
            if($product->vendor_id == 0) return $this->errorGenerate();
            $variants = ProductVariant::with('variantItems')->where('product_id',$productId)->get();

            return view('seller.variant',compact('variants','product'));

        }else return $this->errorGenerate();

    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'product_id' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'product_id.required' => trans('admin_validation.Product is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $product = Product::find($request->product_id)->first();
        if($product){
            $variant = new ProductVariant();
            $variant->name = $request->name;
            $variant->product_id = $request->product_id;
            $variant->status = $request->status;
            $variant->save();

            $notification = trans('admin_validation.Created Successfully');
            $notification=array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }else{
            $notification = trans('admin_validation.Something went wrong');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

    }

    public function update(Request $request,$id){
        $rules = [
            'name' => 'required',
            'product_id' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'product_id.required' => trans('admin_validation.Product is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $variant = ProductVariant::find($id);
        $variant->name = $request->name;
        $variant->status = $request->status;
        $variant->save();

        ProductVariantItem::where('product_variant_id',$variant->id)->update(['product_variant_name' => $variant->name]);

        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
    }


    public function destroy($id)
    {
        $variant = ProductVariant::find($id);
        $variant->delete();

        ShoppingCartVariant::where('variant_id', $id)->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function changeStatus($id){
        $variant = ProductVariant::find($id);
        if($variant->status == 1){
            $variant->status = 0;
            $variant->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $variant->status = 1;
            $variant->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function errorGenerate(){
        $notification = trans('admin_validation.Something went wrong');
        $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
    }


    public function show($id){
        $variant = ProductVariant::find($id);
        return response()->json(['variant' => $variant],200);
    }
}

