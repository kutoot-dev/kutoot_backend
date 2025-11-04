<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;

use App\Mail\SubscirberSendMail;
use App\Helpers\MailHelper;
use Str;
use Mail;
use Hash;
use Auth;

class SubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $subscribers = Subscriber::where('is_verified',1)->get();
        return view('admin.subscriber',compact('subscribers'));
    }

    public function destroy($id){
        $subscriber = Subscriber::find($id);
        $subscriber->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function specificationSubscriberEmail(Request $request,$id){
        $rules = [
            'subject' => 'required',
            'message' => 'required',
        ];
        $customMessages = [
            'subject.required' => trans('admin_validation.Subject is required'),
            'message.required' => trans('admin_validation.Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $subscriber = Subscriber::find($id);
        if($subscriber){
            MailHelper::setMailConfig();

            Mail::to($subscriber->email)->send(new SubscirberSendMail($request->subject,$request->message));

            $notification = trans('admin_validation.Email Send Successfully');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }else{

            $notification = trans('admin_validation.Something Went Wrong');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }
    }

    public function eachSubscriberEmail(Request $request){
        $rules = [
            'subject' => 'required',
            'message' => 'required',
        ];
        $customMessages = [
            'subject.required' => trans('admin_validation.Subject is required'),
            'message.required' => trans('admin_validation.Message is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $subscribers = Subscriber::where('is_verified',1)->get();
        if($subscribers->count() > 0){
            MailHelper::setMailConfig();
            foreach($subscribers as $index => $subscriber){
                Mail::to($subscriber->email)->send(new SubscirberSendMail($request->subject,$request->message));
            }

            $notification = trans('admin_validation.Email Send Successfully');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }else{

            $notification = trans('admin_validation.Something Went Wrong');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }
    }
}
