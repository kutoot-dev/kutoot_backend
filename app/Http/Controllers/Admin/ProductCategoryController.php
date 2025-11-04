<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PopularCategory;
use App\Models\ThreeColumnCategory;
use App\Models\MegaMenuSubCategory;
use App\Models\MegaMenuCategory;
use Illuminate\Http\Request;
use  Image;
use File;
use Str;
class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $categories=Category::with('subCategories','products')->get();
        $pupoularCategory = PopularCategory::first();
        $threeColCategory = ThreeColumnCategory::first();

        return response()->json(['categories' => $categories, 'pupoularCategory' => $pupoularCategory, 'threeColCategory' => $threeColCategory], 200);

    }


    public function create()
    {
        return view('admin.create_product_category');
    }


    public function store(Request $request)
    {
        $rules = [
            'name'=>'required|unique:categories',
            'slug'=>'required|unique:categories',
            'status'=>'required',
            'icon'=>'required',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'icon.required' => trans('Icon is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = new Category();

        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->icon = $request->icon;
        $category->save();

        $notification = trans('Created Successfully');
        return response()->json(['message' => $notification],200);
    }


    public function show($id){
        $category = Category::find($id);
        return response()->json(['category' => $category],200);
    }

    public function edit($id)
    {
        $category = Category::find($id);
        return view('admin.edit_product_category',compact('category'));
    }


    public function update(Request $request,$id)
    {
        $category = Category::find($id);
        $rules = [
            'name'=>'required|unique:categories,name,'.$category->id,
            'slug'=>'required|unique:categories,name,'.$category->id,
            'status'=>'required',
            'icon'=>'required'
        ];

        $customMessages = [
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'icon.required' => trans('Icon is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category->icon = $request->icon;
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        $megaMenuCategory = MegaMenuCategory::where('category_id',$id)->first();
        if($megaMenuCategory){
            $cat_id = $megaMenuCategory->id;
            $megaMenuCategory->delete();
            MegaMenuSubCategory::where('mega_menu_category_id',$cat_id)->delete();
        }

        $notification = trans('Delete Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function changeStatus($id){
        $category = Category::find($id);
        if($category->status==1){
            $category->status=0;
            $category->save();
            $message = trans('Inactive Successfully');
        }else{
            $category->status=1;
            $category->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }
}
