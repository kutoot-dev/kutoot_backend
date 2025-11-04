<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;
use Image;
use File;
class PrivacyPolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $privacyPolicy = TermsAndCondition::first();
        $isPrivacyPolicy = false;
        if($privacyPolicy){
            $isPrivacyPolicy = true;
        }

        return view('admin.privacy_policy',compact('privacyPolicy','isPrivacyPolicy'));
    }


    public function store(Request $request)
    {
        $rules = [
            'privacy_policy' => 'required',
        ];
        $customMessages = [
            'privacy_policy.required' => trans('admin_validation.Privacy policy is required')
        ];
        $this->validate($request, $rules,$customMessages);

        $privacyPolicy = new TermsAndCondition();

        $privacyPolicy->privacy_policy = $request->privacy_policy;
        $privacyPolicy->save();

        $notification = trans('admin_validation.Created Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function update(Request $request, $id)
    {
        $privacyPolicy = TermsAndCondition::find($id);

        $rules = [
            'privacy_policy' => 'required',
        ];
        $customMessages = [
            'privacy_policy.required' => trans('admin_validation.Privacy policy is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $privacyPolicy->privacy_policy = $request->privacy_policy;
        $privacyPolicy->save();

        $notification = trans('admin_validation.Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
}
