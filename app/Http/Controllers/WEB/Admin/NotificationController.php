<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TwilioSms;
use App\Models\BiztechSms;
use App\Models\SmsTemplate;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    public function twilio_sms(){
        $twilio = TwilioSms::first();
        $biztech = BiztechSms::first();

        return view('admin.sms_configuration', compact('twilio','biztech'));
    }

    public function update_twilio_sms(Request $request){
        $rules = [
            'account_sid' => 'required',
            'auth_token' => 'required',
            'twilio_phone_number' => 'required',
        ];
        $customMessages = [
            'account_sid.required' => trans('Auth SID is required'),
            'auth_token.required' => trans('Auth token is required'),
            'twilio_phone_number.required' => trans('Twilio phone is required')
        ];
        $this->validate($request, $rules,$customMessages);

        $twilio = TwilioSms::first();
        $twilio->account_sid = $request->account_sid;
        $twilio->auth_token = $request->auth_token;
        $twilio->twilio_phone_number = $request->twilio_phone_number;
        $twilio->enable_register_sms = $request->register_otp ? 1 : 0;
        $twilio->enable_reset_pass_sms = $request->reset_pass_otp ? 1 : 0;
        $twilio->enable_order_confirmation_sms = $request->order_confirmation ? 1 : 0;
        $twilio->save();

        $notification = trans('Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function update_biztech_sms(Request $request){
        $rules = [
            'api_key' => 'required',
            'client_id' => 'required',
            'sender_id' => 'required',
        ];
        $customMessages = [
            'api_key.required' => trans('Api key is required'),
            'client_id.required' => trans('Client id is required'),
            'sender_id.required' => trans('Sender id is required')
        ];
        $this->validate($request, $rules,$customMessages);

        $biztech = BiztechSms::first();
        $biztech->api_key = $request->api_key;
        $biztech->client_id = $request->client_id;
        $biztech->sender_id = $request->sender_id;
        $biztech->enable_register_sms = $request->register_otp ? 1 : 0;
        $biztech->enable_reset_pass_sms = $request->reset_pass_otp ? 1 : 0;
        $biztech->enable_order_confirmation_sms = $request->order_confirmation ? 1 : 0;
        $biztech->save();

        $notification = trans('Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }



    public function sms_template(){


        $templates = SmsTemplate::all();
        return view('admin.sms_template',compact('templates'));
    }

    public function edit_sms_template($id){
        $template = SmsTemplate::find($id);

        return view('admin.edit_sms_template',compact('template'));
    }

    public function update_sms_template(Request $request,$id){
        $rules = [
            'description'=>'required',
        ];
        $customMessages = [
            'description.required' => trans('admin_validation.Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $template = SmsTemplate::find($id);
        if($template){
            $template->subject = $request->subject;
            $template->description = $request->description;
            $template->save();
            $notification= trans('admin_validation.Updated Successfully');
            $notification = array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);
        }else{
            $notification= trans('admin_validation.Something went wrong');
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }
    }
}
