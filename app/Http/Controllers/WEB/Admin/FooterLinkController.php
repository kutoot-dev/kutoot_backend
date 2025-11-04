<?php

namespace App\Http\Controllers\WEB\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FooterLink;
use App\Models\Footer;
class FooterLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $links = FooterLink::where('column',1)->get();
        $column = 1;
        $title = trans('admin_validation.First Column Link');
        $footer = Footer::first();
        $columnTitle = $footer->first_column;

        return view('admin.footer_link', compact('links','column','title','columnTitle'));

    }

    public function secondColFooterLink(){
        $links = FooterLink::where('column',2)->get();
        $column = 2;
        $title = trans('admin_validation.Second Column Link');
        $footer = Footer::first();
        $columnTitle = $footer->second_column;

        return view('admin.footer_link', compact('links','column','title','columnTitle'));
    }

    public function thirdColFooterLink(){
        $links = FooterLink::where('column',3)->get();
        $column = 3;
        $title = trans('admin_validation.Third Column Link');
        $footer = Footer::first();
        $columnTitle = $footer->third_column;

        return view('admin.footer_link', compact('links','column','title','columnTitle'));
    }



    public function store(Request $request){
        $rules = [
            'link' =>'required',
            'name' =>'required',
            'column' =>'required',
        ];
        $customMessages = [
            'link.required' => trans('admin_validation.Link is required'),
            'name.required' => trans('admin_validation.Name is required'),
            'column.required' => trans('admin_validation.Column is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $link = new FooterLink();
        $link->link = $request->link;
        $link->title = $request->name;
        $link->column = $request->column;
        $link->save();

        $notification=trans('admin_validation.Create Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function show($id){
        $link = FooterLink::find($id);
        return response()->json(['link' => $link], 200);
    }

    public function update(Request $request, $id){
        $rules = [
            'name' =>'required',
            'link' =>'required',
        ];
        $customMessages = [
            'link.required' => trans('admin_validation.Link is required'),
            'name.required' => trans('admin_validation.Name is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $link = FooterLink::find($id);
        $link->link = $request->link;
        $link->title = $request->name;
        $link->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function destroy($id){
        $link = FooterLink::find($id);
        $link->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateColTitle(Request $request, $id){
        $rules = [
            'title' =>'required'
        ];
        $customMessages = [
            'title.required' => trans('admin_validation.Title is required'),
        ];
        $this->validate($request, $rules,$customMessages);
        $footer = Footer::first();
        if($id == 1){
            $footer->first_column = $request->title;
            $footer->save();
        }else if($id == 2){
            $footer->second_column = $request->title;
            $footer->save();
        }else if($id == 3){
            $footer->third_column = $request->title;
            $footer->save();
        }
        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }

}
