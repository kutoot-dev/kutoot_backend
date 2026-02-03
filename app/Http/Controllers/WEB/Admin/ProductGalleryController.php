<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductGallery;
use App\Models\Product;
use Illuminate\Http\Request;
use Image;
use File;
use Str;
/**
 * @group Product Gallery
 */
class ProductGalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index($productId)
    {
        $product = Product::find($productId);
        if($product){
            $gallery = ProductGallery::where('product_id',$productId)->get();

            return view('admin.product_image_gallery',compact('gallery','product'));

        }else{
            $notification = trans('admin_validation.Something went wrong');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->route('admin.product.index')->with($notification);
        }

    }

    public function store(Request $request)
    {
        $rules = [
            'images' => 'required',
            'product_id' => 'required',
        ];

        $customMessages = [
            'images.required' => trans('admin_validation.Image is required'),
            'product_id.required' => trans('admin_validation.Product is required'),
        ];
        $this->validate($request, $rules,$customMessages);

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
                $notification=array('messege'=>$notification,'alert-type'=>'success');
                return redirect()->back()->with($notification);
            }
        }else{
            $notification = trans('admin_validation.Something went wrong');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
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

 public function changeStatus($id)
{
    $gallery = ProductGallery::find($id);

    if (!$gallery) {
        return response()->json(['error' => 'Gallery not found'], 404);
    }

    $gallery->status = $gallery->status == 1 ? 0 : 1;
    $gallery->save();

    $message = $gallery->status == 1 
        ? trans('admin_validation.Active Successfully') 
        : trans('admin_validation.Inactive Successfully');

    return response()->json($message);
}

}
