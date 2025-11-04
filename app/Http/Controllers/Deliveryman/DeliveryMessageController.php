<?php

namespace App\Http\Controllers\Deliveryman;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\DeliveryMessage;
use App\Http\Controllers\Controller;
use Auth;

class DeliveryMessageController extends Controller
{
    
    public function message_with_customer($order_id){
        return response()->json(['orderId'=>$order_id]);
    }

    public function get_message_with_customer($order_id){
        $deliveryMessage=DeliveryMessage::with('customer', 'deliveryman')->where('order_id', $order_id)->get();
        return response()->json(['deliveryMessage'=>$deliveryMessage]);
    }

    public function sent_message_to_customer(Request $request){
        $rules = [
            'order_id'=>'required',
            'message'=>'required',
        ];
        $customMessages = [
            'order_id.required' => trans('Order id is required'),
            'message.required' => trans('Text message is required'),
        ];
        $this->validate($request, $rules,$customMessages);
        $order=Order::whereId($request->order_id)->first();
        $customer_id=$order->user_id;
        $delivery_man_id=$order->delivery_man_id;

        $message = new DeliveryMessage();
        $message->delivery_man_id=$delivery_man_id;
        $message->customer_id=$customer_id;
        $message->order_id=$request->order_id;
        $message->message=$request->message;
        $message->sent_by='deliveryman';
        $message->save();
        $update_message = DeliveryMessage::find($message->id);
        $messages = DeliveryMessage::where(['customer_id' => $customer_id, 'delivery_man_id' => $delivery_man_id])->get();
        
        return response()->json(['message' => $update_message, 'messages'=>$messages]);
    }
}
