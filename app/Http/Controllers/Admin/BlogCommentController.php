<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $blogComments = BlogComment::with('blog')->get();
        return response()->json(['blogComments' => $blogComments]);
    }

    public function destroy($id)
    {
        $blogComment = BlogComment::find($id);
        $blogComment->delete();

        $notification= trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function changeStatus($id){
        $blogComment = BlogComment::find($id);
        if($blogComment->status == 1){
            $blogComment->status = 0;
            $blogComment->save();
            $message = trans('Inactive Successfully');
        }else{
            $blogComment->status = 1;
            $blogComment->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }
}
