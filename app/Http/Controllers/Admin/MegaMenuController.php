<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MegaMenuCategory;
use App\Models\MegaMenuSubCategory;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\BannerImage;
use Image;
use File;
class MegaMenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $categories = MegaMenuCategory::with('category')->orderBy('serial','asc')->get();
        return response()->json(['categories' => $categories], 200);
    }

    public function create(){
        $categories = Category::where('status',1)->get();

        return response()->json(['categories' => $categories], 200);

    }

    public function store(Request $request){
        $rules = [
            'category' => 'required|unique:mega_menu_categories,category_id',
            'status' => 'required',
            'serial' => 'required',
        ];
        $customMessages = [
            'category.required' => trans('Category is required'),
            'category.unique' => trans('Category already exist'),
            'status.required' => trans('Status is required'),
            'serial.required' => trans('Serial text is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = new MegaMenuCategory();
        $category->category_id = $request->category;
        $category->status = $request->status;
        $category->serial = $request->serial;
        $category->save();

        $notification= trans('Created Successfully');
        return response()->json(['notification' => $notification], 200);

    }


    public function show($id){
        $megaMenuCategory = MegaMenuCategory::with('category')->find($id);
        $categories = Category::where('status',1)->get();

        return response()->json(['megaMenuCategory' => $megaMenuCategory, 'categories' => $categories], 200);

    }

    public function edit($id){
        $megaMenuCategory = MegaMenuCategory::find($id);
        $categories = Category::where('status',1)->get();
        return view('admin.edit_mega_menu_category', compact('categories','megaMenuCategory'));
    }



    public function update(Request $request, $id){
        $category = MegaMenuCategory::find($id);
        $rules = [
            'category' => 'required|unique:mega_menu_categories,category_id,'.$category->id,
            'status' => 'required',
            'serial' => 'required',
        ];
        $customMessages = [
            'category.required' => trans('Category is required'),
            'category.unique' => trans('Category already exist'),
            'status.required' => trans('Status is required'),
            'serial.required' => trans('Serial text is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category->category_id = $request->category;
        $category->status = $request->status;
        $category->serial = $request->serial;
        $category->save();

        $notification= trans('Update Successfully');
        return response()->json(['notification' => $notification], 200);

    }


    public function destroy($id){
        $category = MegaMenuCategory::find($id);
        $category_id = $category->id;
        $category->delete();
        MegaMenuSubCategory::where('mega_menu_category_id',$category_id)->delete();

        $notification= trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function changeStatus($id){
        $category = MegaMenuCategory::find($id);
        if($category->status==1){
            $category->status=0;
            $category->save();
            $message= trans('Inactive Successfully');
        }else{
            $category->status=1;
            $category->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }

}
