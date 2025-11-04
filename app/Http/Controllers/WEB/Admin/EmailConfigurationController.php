<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailConfiguration;
class EmailConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $email = EmailConfiguration::first();
        return view('admin.email_configuration',compact('email'));
    }

    public function update(Request $request){
        $rules = [
            'mail_host' => 'required',
            'email' => 'required',
            'smtp_username' => 'required',
            'smtp_password' => 'required',
            'mail_port' => 'required',
            'mail_encryption' => 'required',
        ];
        $customMessages = [
            'mail_host.required' => trans('admin_validation.Mail host is required'),
            'email.required' => trans('admin_validation.Email is required'),
            'smtp_username.required' => trans('admin_validation.Smtp username is required'),
            'smtp_password.unique' => trans('admin_validation.Smtp password is required'),
            'mail_port.required' => trans('admin_validation.Mail port is required'),
            'mail_encryption.required' => trans('admin_validation.Mail encryption is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $email = EmailConfiguration::first();
        $email->mail_host = $request->mail_host;
        $email->email = $request->email;
        $email->smtp_username = $request->smtp_username;
        $email->smtp_password = $request->smtp_password;
        $email->mail_port = $request->mail_port;
        $email->mail_encryption = $request->mail_encryption;
        $email->save();

        $notification=  trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
