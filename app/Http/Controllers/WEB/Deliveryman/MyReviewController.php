<?php

namespace App\Http\Controllers\WEB\Deliveryman;

use Illuminate\Http\Request;
use App\Models\DeliveryManReview;
use App\Http\Controllers\Controller;
use Auth;

class MyReviewController extends Controller
{
    public function index(){
        $id=Auth::guard('deliveryman')->user()->id;
        $reviews=DeliveryManReview::with('user', 'order')->where('delivery_man_id', $id)->where('status', 1)->latest()->get();
        
        return view('deliveryman.review', compact('reviews'));
    }
}
