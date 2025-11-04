<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\PopularPost;
use Illuminate\Http\Request;
use  Image;
use File;
use Auth;
class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $blogs = Blog::with('category','comments')->get();
        return response()->json(['blogs' => $blogs]);
    }


    public function create()
    {
        $categories = BlogCategory::where('status',1)->get();
        return view('admin.create_blog',compact('categories'));
    }


    public function store(Request $request)
    {
        $rules = [
            'title'=>'required|unique:blogs',
            'slug'=>'required|unique:blogs',
            'image'=>'required',
            'description'=>'required',
            'category'=>'required',
            'status'=>'required',
            'show_homepage'=>'required',
        ];
        $customMessages = [
            'title.required' => trans('Title is required'),
            'title.unique' => trans('Title already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'image.required' => trans('Image is required'),
            'description.required' => trans('Description is required'),
            'category.required' => trans('Category is required'),
            'show_homepage.required' => trans('Show homepage is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $admin = Auth::guard('admin-api')->user();
        $blog = new Blog();
        if($request->image){
            $extention=$request->image->getClientOriginalExtension();
            $image_name = 'blog-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name ='uploads/custom-images/'.$image_name;
            Image::make($request->image)
                ->save(public_path().'/'.$image_name);
            $blog->image = $image_name;
        }

        $blog->admin_id = $admin->id;
        $blog->title = $request->title;
        $blog->slug = $request->slug;
        $blog->description = $request->description;
        $blog->blog_category_id = $request->category;
        $blog->status = $request->status;
        $blog->show_homepage = $request->show_homepage;
        $blog->seo_title = $request->seo_title ? $request->seo_title : $request->title;
        $blog->seo_description = $request->seo_description ? $request->seo_description : $request->title;
        $blog->save();

        $notification= trans('Created Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function edit($id)
    {
        $categories = BlogCategory::where('status',1)->get();
        $blog = Blog::find($id);
        return view('admin.edit_blog',compact('categories','blog'));
    }


    public function show($id)
    {
        $blog = Blog::with('category','comments')->find($id);
        return response()->json(['blog' => $blog], 200);
    }


    public function update(Request $request,$id)
    {
        $blog = Blog::find($id);
        $rules = [
            'title'=>'required|unique:blogs,title,'.$blog->id,
            'slug'=>'required|unique:blogs,slug,'.$blog->id,
            'description'=>'required',
            'category'=>'required',
            'status'=>'required',
            'show_homepage'=>'required',
        ];
        $customMessages = [
            'title.required' => trans('Title is required'),
            'title.unique' => trans('Title already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'description.required' => trans('Description is required'),
            'category.required' => trans('Category is required'),
            'show_homepage.required' => trans('Show homepage is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        if($request->image){
            $old_image = $blog->image;
            $extention=$request->image->getClientOriginalExtension();
            $image_name = 'blog-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name ='uploads/custom-images/'.$image_name;
            Image::make($request->image)
                ->save(public_path().'/'.$image_name);
            $blog->image = $image_name;
            $blog->save();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }

        $blog->title = $request->title;
        $blog->slug = $request->slug;
        $blog->description = $request->description;
        $blog->blog_category_id = $request->category;
        $blog->status = $request->status;
        $blog->show_homepage = $request->show_homepage;
        $blog->seo_title = $request->seo_title ? $request->seo_title : $request->title;
        $blog->seo_description = $request->seo_description ? $request->seo_description : $request->title;
        $blog->save();

        $notification= trans('Updated Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);
        $old_image = $blog->image;
        $blog->delete();
        if($old_image){
            if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
        }

        BlogComment::where('blog_id',$id)->delete();
        PopularPost::where('blog_id',$id)->delete();

        $notification=  trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function changeStatus($id){
        $blog = Blog::find($id);
        if($blog->status==1){
            $blog->status=0;
            $blog->save();
            $message= trans('Inactive Successfully');
        }else{
            $blog->status=1;
            $blog->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }
}
