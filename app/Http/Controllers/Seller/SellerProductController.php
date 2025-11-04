<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\ProductGallery;
use App\Models\Brand;
use App\Models\ProductSpecificationKey;
use App\Models\ProductSpecification;
use App\Models\User;
use App\Models\Vendor;
use App\Models\OrderProduct;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\Wishlist;
use App\Models\Setting;
use App\Models\ShoppingCart;
use App\Models\FlashSaleProduct;
use App\Models\ShoppingCartVariant;
use App\Models\CompareProduct;
use Image;
use File;
use Str;
use Auth;


class SellerProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $seller = Auth::guard('api')->user()->seller;
        $products = Product::with('category','seller','brand')->orderBy('id','desc')->where('status',1)->where('vendor_id',$seller->id)->get();
        $orderProducts = OrderProduct::all();
        return response()->json(['products' => $products, 'orderProducts' => $orderProducts]);


    }

    public function pendingProduct(){
        $seller = Auth::guard('api')->user()->seller;
        $products = Product::with('category','seller','brand')->orderBy('id','desc')->where('status',0)->where('vendor_id',$seller->id)->get();
        $orderProducts = OrderProduct::all();

        return response()->json(['products' => $products, 'orderProducts' => $orderProducts]);
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $specificationKeys = ProductSpecificationKey::all();

        return response()->json(['categories' => $categories , 'brands' => $brands, 'specificationKeys' => $specificationKeys], 200);
    }


    public function getSubcategoryByCategory($id){
        $subCategories=SubCategory::where('category_id',$id)->get();
        return response()->json(['subCategories'=>$subCategories]);
    }

    public function getChildcategoryBySubCategory($id){
        $childCategories=ChildCategory::where('sub_category_id',$id)->get();
        return response()->json(['childCategories'=>$childCategories]);
    }

    public function store(Request $request)
    {

        $rules = [
            'short_name' => 'required',
            'name' => 'required',
            'slug' => 'required|unique:products',
            'thumb_image' => 'required',
            'category' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'price' => 'required|numeric',
            'status' => 'required',
            'weight' => 'required',
            'quantity' => 'required|numeric',
        ];
        $customMessages = [
            'short_name.required' => trans('Short name is required'),
            'short_name.unique' => trans('Short name is required'),
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name is required'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'category.required' => trans('Category is required'),
            'thumb_image.required' => trans('thumbnail is required'),
            'short_description.required' => trans('Short description is required'),
            'long_description.required' => trans('Long description is required'),
            'price.required' => trans('Price is required'),
            'status.required' => trans('Status is required'),
            'quantity.required' => trans('Quantity is required'),
            'weight.required' => trans('Weight is required'),
        ];
        $this->validate($request, $rules,$customMessages);


        $seller = Auth::guard('api')->user()->seller;
        $product = new Product();
        if($request->thumb_image){
            $extention = $request->thumb_image->getClientOriginalExtension();
            $image_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->thumb_image)
                ->save(public_path().'/'.$image_name);
            $product->thumb_image=$image_name;
        }

        $product->vendor_id = $seller->id;
        $product->short_name = $request->short_name;
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category ? $request->sub_category : 0;
        $product->child_category_id = $request->child_category ? $request->child_category : 0;
        $product->brand_id = $request->brand ? $request->brand : 0;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->offer_price = $request->offer_price;
        $product->qty = $request->quantity ? $request->quantity : 0;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->tags = $request->tags;
        $product->status = $request->status;
        $product->weight = $request->weight;
        $product->is_undefine = 1;
        $product->is_specification = $request->is_specification ? 1 : 0;
        $product->seo_title = $request->seo_title ? $request->seo_title : $request->name;
        $product->seo_description = $request->seo_description ? $request->seo_description : $request->name;
        $product->is_top = $request->top_product ? 1 : 0;
        $product->new_product = $request->new_arrival ? 1 : 0;
        $product->is_best = $request->best_product ? 1 : 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->save();

        if($request->is_specification){
            $exist_specifications=[];
            if($request->keys){
                foreach($request->keys as $index => $key){
                    if($key){
                        if($request->specifications[$index]){
                            if(!in_array($key, $exist_specifications)){
                                $productSpecification= new ProductSpecification();
                                $productSpecification->product_id = $product->id;
                                $productSpecification->product_specification_key_id = $key;
                                $productSpecification->specification = $request->specifications[$index];
                                $productSpecification->save();
                            }
                            $exist_specifications[] = $key;
                        }
                    }
                }
            }
        }
        $notification = trans('Created Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function show($id)
    {
        $product = Product::with('category','brand','gallery','specifications','reviews','variants','variantItems')->find($id);
        if($product->vendor_id == 0){
            $notification = 'Something went wrong';
            return response()->json(['error'=>$notification],403);
        }

        return response()->json(['product' => $product], 200);
    }

    public function edit($id)
    {
        $product = Product::with('category','brand','gallery','variants','variantItems')->find($id);
        if($product->vendor_id == 0){
            $notification = 'Something went wrong';
            return response()->json(['error'=>$notification],403);
        }
        $categories = Category::all();
        $subCategories = SubCategory::where('category_id',$product->category_id)->get();
        $childCategories = ChildCategory::where('sub_category_id', $product->sub_category_id)->get();
        $brands = Brand::all();
        $specificationKeys = ProductSpecificationKey::all();
        $productSpecifications = ProductSpecification::where('product_id',$product->id)->get();
        $tagArray = json_decode($product->tags);
        $tags = '';
        if($product->tags){
            foreach($tagArray as $index => $tag){
                $tags .= $tag->value.',';
            }
        }

        return response()->json(['product' => $product, 'categories' => $categories , 'brands' => $brands, 'specificationKeys' => $specificationKeys, 'productSpecifications' => $productSpecifications, 'subCategories' => $subCategories, 'childCategories' => $childCategories , ], 200);

    }

    public function update(Request $request, $id)
    {


        $product = Product::find($id);

        $rules = [
            'short_name' => 'required',
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id,
            'category' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'price' => 'required|numeric',
            'status' => 'required',
            'weight' => 'required',
            'quantity' => 'required|numeric',
        ];
        $customMessages = [
            'short_name.required' => trans('Short name is required'),
            'short_name.unique' => trans('Short name is required'),
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name is required'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'category.required' => trans('Category is required'),
            'thumb_image.required' => trans('thumbnail is required'),
            'banner_image.required' => trans('Banner is required'),
            'short_description.required' => trans('Short description is required'),
            'long_description.required' => trans('Long description is required'),
            'brand.required' => trans('Brand is required'),
            'price.required' => trans('Price is required'),
            'quantity.required' => trans('Quantity is required'),
            'status.required' => trans('Status is required'),
            'weight.required' => trans('Weight is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        if($request->thumb_image){
            $old_thumbnail = $product->thumb_image;
            $extention = $request->thumb_image->getClientOriginalExtension();
            $image_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->thumb_image)
                ->save(public_path().'/'.$image_name);
            $product->thumb_image=$image_name;
            $product->save();
            if($old_thumbnail){
                if(File::exists(public_path().'/'.$old_thumbnail))unlink(public_path().'/'.$old_thumbnail);
            }
        }


        $product->short_name = $request->short_name;
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category ? $request->sub_category : 0;
        $product->child_category_id = $request->child_category ? $request->child_category : 0;
        $product->brand_id = $request->brand ? $request->brand : 0;
        $product->qty = $request->quantity ? $request->quantity : 0;
        $product->sold_qty = 0;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->offer_price = $request->offer_price;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->tags = $request->tags;
        $product->status = $request->status;
        $product->weight = $request->weight;
        $product->is_specification = $request->is_specification ? 1 : 0;
        $product->seo_title = $request->seo_title ? $request->seo_title : $request->name;
        $product->seo_description = $request->seo_description ? $request->seo_description : $request->name;
        $product->is_top = $request->top_product ? 1 : 0;
        $product->new_product = $request->new_arrival ? 1 : 0;
        $product->is_best = $request->best_product ? 1 : 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->save();

        $exist_specifications=[];
        if($request->keys){
            foreach($request->keys as $index => $key){
                if($key){
                    if($request->specifications[$index]){
                        if(!in_array($key, $exist_specifications)){
                            $existSroductSpecification = ProductSpecification::where(['product_id' => $product->id,'product_specification_key_id' => $key])->first();
                            if($existSroductSpecification){
                                $existSroductSpecification->specification = $request->specifications[$index];
                                $existSroductSpecification->save();
                            }else{
                                $productSpecification = new ProductSpecification();
                                $productSpecification->product_id = $product->id;
                                $productSpecification->product_specification_key_id = $key;
                                $productSpecification->specification = $request->specifications[$index];
                                $productSpecification->save();
                            }
                        }
                        $exist_specifications[] = $key;
                    }
                }
            }
        }
        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $gallery = $product->gallery;
        $old_thumbnail = $product->thumb_image;
        $product->delete();
        if($old_thumbnail){
            if(File::exists(public_path().'/'.$old_thumbnail))unlink(public_path().'/'.$old_thumbnail);
        }
        foreach($gallery as $image){
            $old_image = $image->image;
            $image->delete();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }
        ProductVariant::where('product_id',$id)->delete();
        ProductVariantItem::where('product_id',$id)->delete();
        ProductReport::where('product_id',$id)->delete();
        FlashSaleProduct::where('product_id',$id)->delete();
        ProductReview::where('product_id',$id)->delete();
        ProductSpecification::where('product_id',$id)->delete();
        Wishlist::where('product_id',$id)->delete();

        $cartProducts = ShoppingCart::where('product_id',$id)->get();
        foreach($cartProducts as $cartProduct){
            ShoppingCartVariant::where('shopping_cart_id', $cartProduct->id)->delete();
            $cartProduct->delete();
        }
        CompareProduct::where('product_id',$id)->delete();

        $notification = trans('Delete Successfully');
        return response()->json(['message' => $notification],200);

    }

    public function changeStatus($id){
        $product = Product::find($id);
        if($product->status == 1){
            $product->status = 0;
            $product->save();
            $message = trans('Inactive Successfully');
        }else{
            $product->status = 1;
            $product->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }

    public function removedProductExistSpecification($id){
        $productSpecification = ProductSpecification::find($id);
        $productSpecification->delete();
        $message = trans('Removed Successfully');
        return response()->json($message);
    }

}
