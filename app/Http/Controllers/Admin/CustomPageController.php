<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use Illuminate\Http\Request;
use Image;
use File;
class CustomPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $customPages = CustomPage::all();
        return response()->json(['customPages' => $customPages]);
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
            'page_name.required' => trans('Page name is required'),
            'page_name.unique' => trans('Page name already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'description.required' => trans('Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $customPage = new CustomPage();
        $customPage->page_name = $request->page_name;
        $customPage->slug = $request->slug;
        $customPage->description = $request->description;
        $customPage->status = $request->status;
        $customPage->save();

        $notification = trans('Created Successfully');
        return response()->json(['message' => $notification], 200);
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
            'page_name.required' => trans('Page name is required'),
            'page_name.unique' => trans('Page name already exist'),
            'slug.required' => trans('Slug is required'),
            'slug.unique' => trans('Slug already exist'),
            'description.required' => trans('Description is required'),

        ];
        $this->validate($request, $rules,$customMessages);

        $customPage->page_name = $request->page_name;
        $customPage->slug = $request->slug;
        $customPage->description = $request->description;
        $customPage->status = $request->status;
        $customPage->save();

        $notification = trans('Updated Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function destroy($id)
    {
        $customPage = CustomPage::find($id);
        $customPage->delete();

        $notification = trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }


    public function changeStatus($id){
        $customPage = CustomPage::find($id);
        if($customPage->status == 1){
            $customPage->status = 0;
            $customPage->save();
            $message = trans('Inactive Successfully');
        }else{
            $customPage->status = 1;
            $customPage->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }

}
