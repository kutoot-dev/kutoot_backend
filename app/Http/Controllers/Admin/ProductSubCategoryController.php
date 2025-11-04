<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\PopularCategory;
use App\Models\ThreeColumnCategory;
use App\Models\MegaMenuSubCategory;
class ProductSubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $subCategories=SubCategory::with('category','childCategories','products')->get();
        $pupoularCategory = PopularCategory::first();
        $threeColCategory = ThreeColumnCategory::first();

        return response()->json(['subCategories' => $subCategories, 'pupoularCategory' => $pupoularCategory, 'threeColCategory' => $threeColCategory],200);
    }


    public function create()
    {
        $categories=Category::all();
        return view('admin.create_product_sub_category',compact('categories'));
    }


    public function store(Request $request)
    {
        $rules = [
            'name'=>'required',
            'slug'=>'required|unique:sub_categories',
            'category'=>'required',
            'status'=>'required'
        ];

        $customMessages = [
            'name.required' => trans('Name is required'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'category.required' => trans('Category is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $subCategory = new SubCategory();
        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->slug = $request->slug;
        $subCategory->status = $request->status;
        $subCategory->save();

        $notification = trans('Created Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function show($id){
        $subCategory = SubCategory::find($id);
        return response()->json(['subCategory' => $subCategory],200);
    }

    public function edit($id)
    {
        $subCategory = SubCategory::find($id);
        $categories=Category::all();
        return view('admin.edit_product_sub_category',compact('subCategory','categories'));
    }


    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);
        $rules = [
            'name'=>'required',
            'slug'=>'required|unique:sub_categories,slug,'.$subCategory->id,
            'category'=>'required',
            'status'=>'required'
        ];

        $customMessages = [
            'name.required' => trans('Name is required'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'category.required' => trans('Category is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->slug = $request->slug;
        $subCategory->status = $request->status;
        $subCategory->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification],200);
    }


    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);
        $subCategory->delete();
        MegaMenuSubCategory::where('sub_category_id',$id)->delete();

        $notification = trans('Delete Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function changeStatus($id){
        $subCategory = SubCategory::find($id);
        if($subCategory->status==1){
            $subCategory->status=0;
            $subCategory->save();
            $message = trans('InActive Successfully');
        }else{
            $subCategory->status=1;
            $subCategory->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }

}
