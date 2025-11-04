<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use Illuminate\Http\Request;
use Image;
use File;
class CustomPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $customPages = CustomPage::all();
        return view('admin.custom_page',compact('customPages'));
    }

    public function create()
    {
        return view('admin.create_custom_page');
    }


    public function store(Request $request)
    {
        $rules = [
            'description' => 'required',
            'page_name' => 'required|unique:custom_pages',
            'slug' => 'required|unique:custom_pages',
            'status' => 'required'
        ];
        $customMessages = [
            'page_name.required' => trans('admin_validation.Page name is required'),
            'page_name.unique' => trans('admin_validation.Page name already exist'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
            'description.required' => trans('admin_validation.Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $customPage = new CustomPage();
        $customPage->page_name = $request->page_name;
        $customPage->slug = $request->slug;
        $customPage->description = $request->description;
        $customPage->status = $request->status;
        $customPage->save();

        $notification = trans('admin_validation.Created Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function show($id)
    {
        $customPage = CustomPage::find($id);
        return response()->json(['customPage' => $customPage]);
    }

    public function edit($id)
    {
        $customPage = CustomPage::find($id);
        return view('admin.edit_custom_page',compact('customPage'));
    }



    public function update(Request $request, $id)
    {
        $customPage = CustomPage::find($id);
        $rules = [
            'description' => 'required',
            'page_name' => 'required|unique:custom_pages,page_name,'.$customPage->id,
            'slug' => 'required|unique:custom_pages,page_name,'.$customPage->id,
            'status' => 'required'
        ];
        $customMessages = [
            'page_name.required' => trans('admin_validation.Page name is required'),
            'page_name.unique' => trans('admin_validation.Page name already exist'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
            'description.required' => trans('admin_validation.Description is required'),

        ];
        $this->validate($request, $rules,$customMessages);

        $customPage->page_name = $request->page_name;
        $customPage->slug = $request->slug;
        $customPage->description = $request->description;
        $customPage->status = $request->status;
        $customPage->save();

        $notification = trans('admin_validation.Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.custom-page.index')->with($notification);
    }

    public function destroy($id)
    {
        $customPage = CustomPage::find($id);
        $customPage->delete();

        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function changeStatus($id){
        $customPage = CustomPage::find($id);
        if($customPage->status == 1){
            $customPage->status = 0;
            $customPage->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $customPage->status = 1;
            $customPage->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

}
