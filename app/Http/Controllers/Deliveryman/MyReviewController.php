<?php

namespace App\Http\Controllers\Deliveryman;

use Illuminate\Http\Request;
use App\Models\DeliveryManReview;
use App\Http\Controllers\Controller;
use Auth;

class MyReviewController extends Controller
{
    public function index(){
        $id=Auth::guard('deliveryman-api')->user()->id;
        $reviews=DeliveryManReview::with('user')->where('delivery_man_id', $id)->where('status', 1)->latest()->get();
        
        return response()->json(['reviews' => $reviews], 200);
    }
}
