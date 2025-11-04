<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use  Image;
use File;
use Str;
class ProductBrandController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $brands=Brand::all();

        return response()->json(['brands' => $brands], 200);

    }

    public function create()
    {
        return view('admin.create_product_brand');
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:brands',
            'slug' => 'required|unique:brands',
            'status' => 'required',
            'logo' => 'required'
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'logo.required' => trans('Logo is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $brand = new Brand();
        if($request->logo){
            $extention = $request->logo->getClientOriginalExtension();
            $logo_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $logo_name = 'uploads/custom-images/'.$logo_name;
            Image::make($request->logo)
                ->save(public_path().'/'.$logo_name);
            $brand->logo=$logo_name;
        }
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status;
        $brand->save();

        $notification = trans('Created Successfully');
        return response()->json(['message' => $notification],200);
    }



    public function show($id)
    {
        $brand = Brand::find($id);
        return response()->json(['brand' => $brand],200);
    }

    public function edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.edit_product_brand',compact('brand'));
    }


    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        $rules = [
            'name' => 'required|unique:brands,name,'.$brand->id,
            'slug' => 'required|unique:brands,slug,'.$brand->id,
            'rating' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'rating.required' => trans('Rating is required'),
            'logo.required' => trans('Logo is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        if($request->logo){
            $old_logo = $brand->logo;
            $extention = $request->logo->getClientOriginalExtension();
            $logo_name = Str::slug($request->name).date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $logo_name = 'uploads/custom-images/'.$logo_name;
            Image::make($request->logo)
                ->save(public_path().'/'.$logo_name);
            $brand->logo = $logo_name;
            $brand->save();
            if($old_logo){
                if(File::exists(public_path().'/'.$old_logo))unlink(public_path().'/'.$old_logo);
            }
        }

        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status;
        $brand->rating = $request->rating;
        $brand->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification],200);
    }


    public function destroy($id)
    {
        $brand = Brand::find($id);
        $old_logo = $brand->logo;
        $brand->delete();
        if($old_logo){
            if(File::exists(public_path().'/'.$old_logo))unlink(public_path().'/'.$old_logo);
        }

        $notification = trans('Delete Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function changeStatus($id){
        $brand = Brand::find($id);
        if($brand->status == 1){
            $brand->status = 0;
            $brand->save();
            $message = trans('InActive Successfully');
        }else{
            $brand->status = 1;
            $brand->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }
}
