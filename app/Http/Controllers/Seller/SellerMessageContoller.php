<?php

namespace App\Http\Controllers\Seller;
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
        $auth = Auth::guard('web')->user();
        $defaultProfile = BannerImage::whereId('15')->first();
        $customers = Message::with('customer')->where(['seller_id' => $auth->id])->select('customer_id')->groupBy('customer_id')->orderBy('id','desc')->get();

        //return view('seller.chat_list', compact('customers','defaultProfile','auth'));
        return response()->json(['customers' => $customers, 'defaultProfile' => $defaultProfile, 'auth' => $auth]);
    }

    public function sendMessage(Request $request){

        $auth = Auth::guard('web')->user();
        $message = new Message();
        $message->customer_id = $request->customer_id;
        $message->seller_id = $auth->id;
        $message->message = $request->message;
        $message->send_seller = $auth->id;
        $message->save();

        $data = ['seller_id' => $auth->id, 'customer_id' => $request->customer_id];
        $user = User::find($request->customer_id);
        event(new SellerToUser($user, $data));
        $customer = User::find($request->customer_id);
        $id = $request->customer_id;
        $messages = Message::where(['seller_id' => $auth->id, 'customer_id'=>$id])->get();
        $defaultProfile = BannerImage::whereId('15')->first();

        //return view('seller.chat__message_list', compact('customer','auth','messages','defaultProfile'));
        return response()->json(['customer' => $customer, 'auth' => $auth, 'messages' => $messages]);
    }

    public function loadNewMessage($id){
        $auth = Auth::guard('web')->user();
        $customer = User::find($id);
        $unRead = Message::where(['seller_id' => $auth->id, 'customer_id' => $customer->id])->update(['seller_read_msg' => 1]);
        $messages = Message::where(['seller_id' => $auth->id, 'customer_id'=>$id])->get();
        $defaultProfile = BannerImage::whereId('15')->first();

        //return view('seller.chat__message_list', compact('customer','auth','messages','defaultProfile'));
        return response()->json(['customer' => $customer, 'auth' => $auth, 'messages' => $messages]);
    }



    public function loadChatBox($id){
        $customer = User::find($id);
        $auth = Auth::guard('web')->user();
        $unRead = Message::where(['seller_id' => $auth->id, 'customer_id' => $customer->id])->update(['seller_read_msg' => 1]);
        $messages = Message::where(['seller_id' => $auth->id, 'customer_id'=>$id])->get();
        $defaultProfile = BannerImage::whereId('15')->first();

        //return view('seller.chat_box', compact('customer','auth','messages','defaultProfile'));

        return response()->json(['customer' => $customer, 'auth' => $auth, 'messages' => $messages, 'defaultProfile' => $defaultProfile]);
    }
}
