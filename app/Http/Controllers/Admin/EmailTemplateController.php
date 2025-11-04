<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $templates = EmailTemplate::all();
        return response()->json(['templates' => $templates]);
    }

    public function edit($id){
        $template = EmailTemplate::find($id);
        if($template){
            return response()->json(['template' => $template]);
        }else{
            $notification='Something went wrong';
            return response()->json(['message' => $notification], 500);
        }

    }

    public function update(Request $request,$id){
        $rules = [
            'subject'=>'required',
            'description'=>'required',
        ];
        $customMessages = [
            'subject.required' => trans('Subject is required'),
            'description.required' => trans('Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $template = EmailTemplate::find($id);
        if($template){
            $template->subject = $request->subject;
            $template->description = $request->description;
            $template->save();
            $notification= trans('Updated Successfully');
            return response()->json(['message' => $notification], 200);
        }else{
            $notification= trans('Something went wrong');
            return response()->json(['message' => $notification], 500);
        }
    }
}
