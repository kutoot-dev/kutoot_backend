<?php

namespace App\Http\Controllers\WEB\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductGallery;
use App\Models\Product;
use Illuminate\Http\Request;
use Image;
use File;
use Str;
class SellerProductGalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index($productId)
    {
        $product = Product::find($productId);
        if($product){
            if($product->vendor_id == 0){
                $notification = trans('admin_validation.Something went wrong');
                return response()->json(['message' => $notification],400);
            }
            $gallery = ProductGallery::where('product_id',$productId)->get();
            return view('seller.product_image_gallery',compact('gallery','product'));
        }else{
            $notification = trans('admin_validation.Something went wrong');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('seller.product.index')->with($notification);
        }

    }


    public function store(Request $request)
    {
        $rules = [
            'images' => 'required',
            'product_id' => 'required',
        ];
        $customMessages = [
            'images.required' => trans('admin_validation.Images is required'),
            'product_id.required' => trans('admin_validation.Product is required'),
        ];
        $this->validate($request, $rules, $customMessages);

        $product = Product::find($request->product_id)->first();
        if($product){
            if($request->images){
                foreach($request->images as $index => $image){
                    $extention = $image->getClientOriginalExtension();
                    $image_name = 'Gallery'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
                    $image_name = 'uploads/custom-images/'.$image_name;
                    Image::make($image)
                        ->save(public_path().'/'.$image_name);
                    $gallery = new ProductGallery();
                    $gallery->product_id = $request->product_id;
                    $gallery->image = $image_name;
                    $gallery->save();
                }

                $notification = trans('admin_validation.Uploaded Successfully');
                $notification = array('messege'=>$notification,'alert-type'=>'success');
                return redirect()->back()->with($notification);
            }
        }else{
            $notification = trans('admin_validation.Something went wrong');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }

    }


    public function destroy($id)
    {
        $gallery = ProductGallery::find($id);
        $old_image = $gallery->image;
        $gallery->delete();
        if($old_image){
            if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
        }

        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function changeStatus($id){
        $gallery = ProductGallery::find($id);
        if($gallery->status == 1){
            $gallery->status = 0;
            $gallery->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $gallery->status = 1;
            $gallery->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}

