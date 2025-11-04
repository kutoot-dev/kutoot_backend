<?php

namespace App\Http\Controllers\User;
use Auth;
use Pusher;
use App\Models\User;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\Message;
use App\Models\BannerImage;
use Illuminate\Http\Request;
use App\Models\DeliveryMessage;
use App\Providers\PusherConfig;
use App\Models\PusherCredentail;
use App\Events\UserToSellerMessage;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{


    public function __construct(){
        $this->middleware('auth:api');
    }

    public function index(){

        $auth_user = Auth::guard('api')->user();

        $sellers = Message::where(['customer_id' => $auth_user->id])->select('seller_id')->groupBy('seller_id')->orderBy('id','desc')->get();
        
        $seller_list = array();
        foreach($sellers as $index => $seller_row){
            $user = User::with('seller')->find($seller_row->seller_id);
            $messages = Message::with('product')->where(['customer_id' => $auth_user->id, 'seller_id' => $seller_row->seller_id])->get();
            $unread_message = $messages->where('send_by','seller')->where('customer_read_msg', 0)->count();
            $shop_detail = (object) array(
                'shop_owner_id' => $seller_row->seller_id,
                'shop_owner' => $user->name,
                'shop_name' => $user->seller->shop_name,
                'shop_or_vendor_id' => $user->seller->id,
                'shop_slug' => $user->seller->slug,
                'shop_logo' => $user->seller->logo,
                'unread_message' => $unread_message,
                'messages' => $messages
            );
            $seller_list[] = $shop_detail;
        }

        return response()->json(['seller_list' => $seller_list]);
    }

    public function send_message_to_seller(Request $request){
        $rules = [
            'seller_id' => 'required',
            'message' => !$request->product_id ? 'required' : '',
        ];
        $customMessages = [
            'seller_id.required' => trans('Seller id is required'),
            'message.required' => trans('Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $auth_user = Auth::guard('api')->user();
        $message = new Message();
        $message->seller_id = $request->seller_id;
        $message->customer_id = $auth_user->id;
        $message->message = $request->message;
        $message->customer_read_msg = 1;
        $message->send_by = 'customer';
        $message->product_id = $request->product_id ? $request->product_id : 0;
        $message->save();

        $update_message = Message::with('product')->find($message->id);
        $user = User::find($request->seller_id);
        $messages = Message::with('product')->where(['customer_id' => $auth_user->id, 'seller_id' => $request->seller_id])->get();

        $data = $update_message;
        event(new UserToSellerMessage($data, $user));

        return response()->json(['message' => $update_message, 'messages' => $messages]);
    }

    public function laod_active_seller_message($id){
        $auth_user =  Auth::guard('api')->user();
        $user = User::find($id);
        Message::where(['customer_id' => $auth_user->id, 'seller_id' => $id])->update(['customer_read_msg' => 1]);
        $messages = Message::with('product')->where(['customer_id' => $auth_user->id, 'seller_id' => $id])->get();

        return response()->json(['messages' => $messages]);
    }

    public function delete_seller_message($seller_id){
        Message::where('seller_id', $seller_id)->delete();

        return response()->json(['status' => 'success']);
    }


    public function sendMessage(Request $request){

        $auth = Auth::guard('web')->user();
        $message = new Message();
        $message->seller_id = $request->seller_id;
        $message->customer_id = $auth->id;
        $message->message = $request->message;
        $message->send_customer = $auth->id;
        $message->save();

        $data = ['seller_id' => $request->seller_id, 'customer_id' => $auth->id];
        $user = User::find($request->seller_id);
        event(new UserToSellerMessage($user, $data));
        $id = $request->seller_id;
        $seller = User::find($id);
        $messages = Message::where(['customer_id' => $auth->id, 'seller_id'=> $request->seller_id])->get();
        $defaultProfile = BannerImage::whereId('15')->first();

        return view('user.chat_message_list', compact('seller','auth','messages','defaultProfile'));

    }

    public function loadNewMessage($id){
        $auth = Auth::guard('web')->user();
        $seller = User::find($id);
        $unRead = Message::where(['customer_id' => $auth->id, 'seller_id' => $seller->id])->update(['customer_read_msg' => 1]);

        $messages = Message::where(['customer_id' => $auth->id, 'seller_id'=>$id])->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        return view('user.chat_message_list', compact('seller','auth','messages','defaultProfile'));
    }


    public function loadChatBox($id){
        $seller = User::find($id);
        $auth = Auth::guard('web')->user();
        $unRead = Message::where(['customer_id' => $auth->id, 'seller_id' => $seller->id])->update(['customer_read_msg' => 1]);
        $messages = Message::where(['customer_id' => $auth->id, 'seller_id'=>$id])->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        return view('user.chat_box', compact('seller','auth','messages','defaultProfile'));
    }

    public function chatWithSeller($slug){
        $auth = Auth::guard('web')->user();
        $seller = Vendor::where('slug', $slug)->first();
        if($auth->id == $seller->user_id){
            $notification = trans('user_validation.Something Went Wrong');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back();
        }else{
            $message = new Message();
            $message->seller_id = $seller->user_id;
            $message->customer_id = $auth->id;
            $message->message = $seller->greeting_msg;
            $message->send_seller = $seller->user_id;
            $message->save();
            return redirect()->route('user.message');
        }
    }

    public function message_with_delivery_man($order_id){
        $deliveryMessage=DeliveryMessage::with('customer', 'deliveryman')->where('order_id', $order_id)->get();
        return response()->json(['deliveryMessage'=>$deliveryMessage, 'orderId'=>$order_id]);
    }

    public function send_message_to_delivery_man(Request $request){
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
        $message->sent_by='customer';
        $message->save();

        $update_message = DeliveryMessage::find($message->id);
        $messages = DeliveryMessage::where(['customer_id' => $customer_id, 'delivery_man_id' => $delivery_man_id])->get();
        return response()->json(['message' => $update_message, 'messages'=>$messages]);
    }
}
