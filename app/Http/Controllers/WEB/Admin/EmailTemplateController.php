<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $templates = EmailTemplate::all();
        return view('admin.email_template',compact('templates'));
    }

    public function edit($id){
        $template = EmailTemplate::find($id);
        if($template){
            return view('admin.edit_email_template',compact('template'));
        }else{
            $notification='Something went wrong';
            $notification = array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }

    }

    public function update(Request $request,$id){
        $rules = [
            'subject'=>'required',
            'description'=>'required',
        ];
        $customMessages = [
            'subject.required' => trans('admin_validation.Subject is required'),
            'description.required' => trans('admin_validation.Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $template = EmailTemplate::find($id);
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
