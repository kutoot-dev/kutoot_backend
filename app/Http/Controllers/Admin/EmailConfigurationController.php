<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailConfiguration;
class EmailConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $email = EmailConfiguration::first();
        return response()->json(['email' => $email]);
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
            'mail_host.required' => trans('Mail host is required'),
            'email.required' => trans('Email is required'),
            'smtp_username.required' => trans('Smtp username is required'),
            'smtp_password.unique' => trans('Smtp password is required'),
            'mail_port.required' => trans('Mail port is required'),
            'mail_encryption.required' => trans('Mail encryption is required'),
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

        $notification=  trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }
}
