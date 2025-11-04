<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $categories=BlogCategory::with('blogs')->get();
        return response()->json(['categories' => $categories]);

    }


    public function create()
    {
        return view('admin.create_blog_category');
    }


    public function store(Request $request)
    {
        $rules = [
            'name'=>'required|unique:blog_categories',
            'slug'=>'required|unique:blog_categories',
            'status'=>'required',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = new BlogCategory();
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->save();

        $notification= trans('Created Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function show($id)
    {
        $category = BlogCategory::find($id);
        return response()->json(['category' => $category], 200);
    }


    public function edit($id)
    {
        $category = BlogCategory::find($id);
        return view('admin.edit_blog_category',compact('category'));
    }

    public function update(Request $request,$id)
    {
        $category = BlogCategory::find($id);
        $rules = [
            'name'=>'required|unique:blog_categories,name,'.$category->id,
            'slug'=>'required|unique:blog_categories,slug,'.$category->id,
            'status'=>'required',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = BlogCategory::find($id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function destroy($id)
    {
        $category = BlogCategory::find($id);
        $category->delete();

        $notification= trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function changeStatus($id){
        $category = BlogCategory::find($id);
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
