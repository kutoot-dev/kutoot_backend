<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ErrorPage;
class ErrorPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $errorPages = ErrorPage::all();

        return response()->json(['errorPages' => $errorPages], 200);
    }

    public function show($id){
        $errorPage = ErrorPage::find($id);
        return response()->json(['errorPage' => $errorPage], 200);
    }

    public function update(Request $request, $id)
    {
        $errorPage = ErrorPage::find($id);

        $rules = [
            'page_name'=>'required',
            'header'=>'required',
            'button_text'=>'required',
        ];
        $customMessages = [
            'page_name.required' => trans('Page name is required'),
            'header.required' => trans('Header is required'),
            'button_text.required' => trans('Button text is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $errorPage->page_name=$request->page_name;
        $errorPage->header=$request->header;
        $errorPage->button_text=$request->button_text;
        $errorPage->save();

        $notification= trans('Updated Successfully');
        return response()->json(['notification' => $notification], 200);
    }
}
