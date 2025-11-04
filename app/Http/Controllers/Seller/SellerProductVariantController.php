<?php

namespace App\Http\Controllers\Seller;

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
        $this->middleware('auth:api');
    }

    public function index($productId)
    {
        $product = Product::find($productId);
        if($product){
            if($product->vendor_id == 0) return $this->errorGenerate();
            $variants = ProductVariant::with('variantItems')->where('product_id',$productId)->get();

            return response()->json(['variants' => $variants, 'product' => $product], 200);

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
            'name.required' => trans('Name is required'),
            'product_id.required' => trans('Product is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $product = Product::find($request->product_id)->first();
        if($product){
            $variant = new ProductVariant();
            $variant->name = $request->name;
            $variant->product_id = $request->product_id;
            $variant->status = $request->status;
            $variant->save();

            $notification = trans('Created Successfully');
            return response()->json(['message' => $notification],200);
        }else{
            $notification = trans('Something went wrong');
            return response()->json(['message' => $notification],200);
        }

    }

    public function update(Request $request,$id){
        $rules = [
            'name' => 'required',
            'product_id' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'product_id.required' => trans('Product is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $variant = ProductVariant::find($id);
        $variant->name = $request->name;
        $variant->status = $request->status;
        $variant->save();

        ProductVariantItem::where('product_variant_id',$variant->id)->update(['product_variant_name' => $variant->name]);

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification],200);
    }


    public function destroy($id)
    {
        $variant = ProductVariant::find($id);
        $variant->delete();

        ShoppingCartVariant::where('variant_id', $id)->delete();

        $notification = trans('Delete Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function changeStatus($id){
        $variant = ProductVariant::find($id);
        if($variant->status == 1){
            $variant->status = 0;
            $variant->save();
            $message = trans('Inactive Successfully');
        }else{
            $variant->status = 1;
            $variant->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }

    public function errorGenerate(){
        $notification = trans('Something went wrong');
        return response()->json(['message' => $notification],400);
    }


    public function show($id){
        $variant = ProductVariant::find($id);
        return response()->json(['variant' => $variant],200);
    }
}

