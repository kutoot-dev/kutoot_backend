<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use Auth;
class SellerProductReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index(){
        $seller = Auth::guard('api')->user()->seller;
        $reviews = ProductReview::with('user','product')->orderBy('id','desc')->where(['product_vendor_id' => $seller->id])->get();
        return response()->json(['reviews' => $reviews], 200);
    }

    public function show($id){
        $review = ProductReview::with('user','product')->find($id);
        if($review){
            $seller = Auth::guard('api')->user()->seller;
            if($review->product_vendor_id == $seller->id){
                return response()->json(['review' => $review], 200);
            }else{
                $notification= trans('Something went wrong');
                return response()->json(['notification' => $notification], 500);
            }

        }else{
            $notification= trans('Something went wrong');
            return response()->json(['notification' => $notification], 500);
        }

    }
}
