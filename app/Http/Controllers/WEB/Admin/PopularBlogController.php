<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\PopularPost;
use App\Models\Blog;
use Illuminate\Http\Request;

class PopularBlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $blogs = Blog::with('category','comments')->where('status',1)->get();
        $popularBlogs = PopularPost::with('blog')->get();

        return view('admin.popular_blog',compact('blogs','popularBlogs'));
    }

    public function store(Request $request)
    {
        $rules = [
            'blog_id'=>'required|unique:popular_posts',
        ];
        $customMessages = [
            'blog_id.required' => trans('admin_validation.Blog is required'),
            'blog_id.unique' => trans('admin_validation.Blog already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $popularBlog = new PopularPost();
        $popularBlog->blog_id = $request->blog_id;
        $popularBlog->status = 1;
        $popularBlog->save();


        $notification = trans('admin_validation.Added Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function destroy($id)
    {
        $blog = PopularPost::find($id);
        $blog->delete();

        $notification= trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function changeStatus($id){
        $blog = PopularPost::find($id);
        if($blog->status==1){
            $blog->status=0;
            $blog->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $blog->status=1;
            $blog->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
