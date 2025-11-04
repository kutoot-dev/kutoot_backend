<?php

namespace App\Http\Controllers\WEB\Seller;
use App\Providers\PusherConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BannerImage;
use App\Models\Message;
use App\Models\PusherCredentail;
use Pusher;
use Auth;
use App\Events\SellerToUser;

class SellerMessageContoller extends Controller
{

    public function __construct(){
        $this->middleware('auth:web');
    }

    public function index(){
        $auth_user = Auth::guard('web')->user();

        return view('seller.chat_list')->with(['auth_user' => $auth_user]);
    }


    public function existing_customer_list(){
        $auth_user = Auth::guard('web')->user();
        $customers = Message::where(['seller_id' => $auth_user->id])->select('customer_id')->groupBy('customer_id')->orderBy('id','desc')->get();

        $customer_list = array();
        foreach($customers as $index => $customer_row){
            $user = User::find($customer_row->customer_id);

            $messages = Message::with('product')->where(['seller_id' => $auth_user->id, 'customer_id' => $customer_row->customer_id])->get();

            $unread_message = $messages->where('send_by','customer')->where('seller_read_msg', 0)->count();

            $customer_detail = (object) array(
                'customer_id' => $user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_image' => $user->image,
                'unread_message' => $unread_message,
                'messages' => $messages
            );
            $customer_list[] = $customer_detail;
        }

        $defaultProfile = BannerImage::whereId('15')->first();

        $auth_user = (object) array(
            'id' => $auth_user->id,
            'name' => $auth_user->name,
            'email' => $auth_user->email,
            'image' => $auth_user->image,
        );
        return response()->json(['customer_list' => $customer_list, 'default_avatar' => $defaultProfile->image, 'auth_user' => $auth_user]);
    }


    public function send_message_to_customer(Request $request){
        $rules = [
            'customer_id' => 'required',
            'message' => 'required',
        ];
        $customMessages = [
            'customer_id.required' => trans('Customer id is required'),
            'message.required' => trans('Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $auth_user =  Auth::guard('web')->user();
        $message = new Message();
        $message->seller_id = $auth_user->id;
        $message->customer_id = $request->customer_id;
        $message->message = $request->message;
        $message->customer_read_msg = 0;
        $message->seller_read_msg = 1;
        $message->send_by = 'seller';
        $message->product_id = 0;
        $message->save();

        $update_message = Message::find($message->id);
        $user = User::find($request->customer_id);
        $messages = Message::with('product')->where(['customer_id' => $request->customer_id, 'seller_id' => $auth_user->id])->get();

        $data = $update_message;
        event(new SellerToUser($data, $user));

        return response()->json(['message' => $update_message, 'messages' => $messages]);
    }

    public function laod_active_user_message($id){
        $auth_user =  Auth::guard('web')->user();
        $user = User::find($id);
        Message::where(['customer_id' => $id, 'seller_id' => $auth_user->id])->update(['seller_read_msg' => 1]);
        $messages = Message::with('product')->where(['customer_id' => $id, 'seller_id' => $auth_user->id])->get();

        return response()->json(['messages' => $messages]);
    }


}
