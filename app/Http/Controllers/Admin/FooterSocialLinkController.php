<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FooterSocialLink;
class FooterSocialLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $links = FooterSocialLink::all();
        return response()->json(['links' => $links], 200);
    }

    public function store(Request $request){
        $rules = [
            'link' =>'required',
            'icon' =>'required',
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'icon.required' => trans('Icon is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $link = new FooterSocialLink();
        $link->link = $request->link;
        $link->icon = $request->icon;
        $link->save();

        $notification=trans('Create Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function show($id){
        $link = FooterSocialLink::find($id);
        return response()->json(['link' => $link], 200);
    }

    public function update(Request $request, $id){
        $rules = [
            'link' =>'required',
            'icon' =>'required',
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'icon.required' => trans('Icon is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $link = FooterSocialLink::find($id);
        $link->link = $request->link;
        $link->icon = $request->icon;
        $link->save();

        $notification=trans('Update Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function destroy($id){
        $link = FooterSocialLink::find($id);
        $link->delete();
        $notification=trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }

}
