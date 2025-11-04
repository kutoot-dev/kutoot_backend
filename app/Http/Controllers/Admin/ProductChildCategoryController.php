<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildCategory;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\PopularCategory;
use App\Models\ThreeColumnCategory;
class ProductChildCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $childCategories=ChildCategory::with('subCategory','category','products')->get();
        $pupoularCategory = PopularCategory::first();
        $threeColCategory = ThreeColumnCategory::first();

        return response()->json(['childCategories' => $childCategories, 'pupoularCategory' => $pupoularCategory, 'threeColCategory' => $threeColCategory], 200);
    }


    public function create()
    {
        $categories=Category::all();
        $SubCategories=SubCategory::all();
        return view('admin.create_product_child_category',compact('categories','SubCategories'));
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
            'name'=>'required',
            'category'=>'required',
            'sub_category'=>'required',
            'slug'=>'required|unique:child_categories',
            'status'=>'required'
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'category.required' => trans('Category is required'),
            'sub_category.required' => trans('Sub category is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $childCategory = new ChildCategory();
        $childCategory->category_id = $request->category;
        $childCategory->sub_category_id = $request->sub_category;
        $childCategory->name = $request->name;
        $childCategory->slug = $request->slug;
        $childCategory->status = $request->status;
        $childCategory->save();

        $notification = trans('Created Successfully');
        return response()->json(['message' => $notification],200);
    }


    public function show($id){
        $childCategory = ChildCategory::find($id);
        return response()->json(['childCategory' => $childCategory],200);
    }

    public function edit($id)
    {
        $childCategory = ChildCategory::find($id);
        $categories = Category::all();
        $subCategories = SubCategory::where('category_id',$childCategory->category_id)->get();
        return view('admin.edit_product_child_category',compact('childCategory','categories','subCategories'));
    }


    public function update(Request $request, $id)
    {
        $childCategory = ChildCategory::find($id);
        $rules = [
            'name' => 'required',
            'category' => 'required',
            'sub_category' => 'required',
            'slug' => 'required|unique:child_categories,slug,'.$childCategory->id,
            'status' => 'required'
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'category.required' => trans('Category is required'),
            'sub_category.required' => trans('Sub category is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $childCategory->category_id = $request->category;
        $childCategory->sub_category_id = $request->sub_category;
        $childCategory->name = $request->name;
        $childCategory->slug = $request->slug;
        $childCategory->status = $request->status;
        $childCategory->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification],200);
    }


    public function destroy($id)
    {
        $childCategory = ChildCategory::find($id);
        $childCategory->delete();
        $notification = trans('Delete Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function changeStatus($id){
        $childCategory = ChildCategory::find($id);
        if($childCategory->status==1){
            $childCategory->status=0;
            $childCategory->save();
            $message = trans('InActive Successfully');
        }else{
            $childCategory->status=1;
            $childCategory->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }
}
