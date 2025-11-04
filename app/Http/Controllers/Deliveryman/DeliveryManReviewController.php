<?php

namespace App\Http\Controllers\Deliveryman;

use Auth;
use App\Models\Order;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\Models\DeliveryManReview;
use App\Http\Controllers\Controller;

class DeliveryManReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function review(Request $request){
        $rules = [
            'order_id' => 'required',
            'review' => 'required',
            'rating' => 'required',
        ];
        $customMessages = [
            'order_id.required' => trans('Order id is required'),
            'review.required' => trans('Review is required'),
            'rating.required' => trans('rating is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $deliveryManReview=DeliveryManReview::where('order_id', $request->order_id)->count();
        $order=Order::whereId($request->order_id)->first();
        $delivery_man_id=$order->delivery_man_id;
        $user_id=$order->user_id;
        
        if($delivery_man_id==0){
            $notification = 'Delivery men not assign this order, You can not send review';
            return response()->json(['error'=>$notification],403);
        }
        
        if($deliveryManReview == 0){
           $review = new DeliveryManReview();
           $review->user_id = $user_id;
           $review->delivery_man_id = $delivery_man_id;
           $review->order_id = $request->order_id;
           $review->review = $request->review;
           $review->rating = $request->rating;
           $review->status = 0;
           $review->save();
           $notification = trans('Review Send Successfully');
           return response()->json(['message' => $notification],200);
        }else{
            $notification = 'Review already submit';
            return response()->json(['error'=>$notification],403);
        }

    }
}
