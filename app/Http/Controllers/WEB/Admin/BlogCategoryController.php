<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $categories=BlogCategory::with('blogs')->get();
        return view('admin.blog_category',compact('categories'));

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
            'name.required' => trans('admin_validation.Name is required'),
            'name.unique' => trans('admin_validation.Name already exist'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = new BlogCategory();
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->save();

        $notification= trans('admin_validation.Created Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
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
            'name.required' => trans('admin_validation.Name is required'),
            'name.unique' => trans('admin_validation.Name already exist'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = BlogCategory::find($id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.blog-category.index')->with($notification);
    }

    public function destroy($id)
    {
        $category = BlogCategory::find($id);
        $category->delete();

        $notification= trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function changeStatus($id){
        $category = BlogCategory::find($id);
        if($category->status==1){
            $category->status=0;
            $category->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $category->status=1;
            $category->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
