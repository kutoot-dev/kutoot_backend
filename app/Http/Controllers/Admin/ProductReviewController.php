<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use Auth;
class ProductReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }


    public function index(){
        $reviews = ProductReview::with('user','product')->orderBy('id','desc')->get();
        return response()->json(['reviews' => $reviews], 200);
    }

    public function show($id){
        $review = ProductReview::with('user','product')->find($id);
        if($review){
            return response()->json(['review' => $review], 200);
        }else{
            $notification=trans('Something went wrong');
            return response()->json(['notification' => $notification], 500);
        }

    }

    public function destroy($id)
    {
        $review = ProductReview::find($id);
        $review->delete();
        $notification = trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);

    }

    public function changeStatus($id){
        $review = ProductReview::find($id);
        if($review->status == 1){
            $review->status = 0;
            $review->save();
            $message = trans('Inactive Successfully');
        }else{
            $review->status = 1;
            $review->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }
}
