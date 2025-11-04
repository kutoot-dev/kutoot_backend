<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;
use Image;
use File;
class PrivacyPolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $privacyPolicy = TermsAndCondition::first();
        $isPrivacyPolicy = false;
        if($privacyPolicy){
            $isPrivacyPolicy = true;
        }

        return response()->json(['privacyPolicy' => $privacyPolicy, 'isPrivacyPolicy' => $isPrivacyPolicy]);
    }


    public function store(Request $request)
    {
        $rules = [
            'privacy_policy' => 'required',
        ];
        $customMessages = [
            'privacy_policy.required' => trans('Privacy policy is required')
        ];
        $this->validate($request, $rules,$customMessages);

        $privacyPolicy = new TermsAndCondition();

        $privacyPolicy->privacy_policy = $request->privacy_policy;
        $privacyPolicy->save();

        $notification = trans('Created Successfully');
        return response()->json(['message' => $notification], 200);
    }


    public function update(Request $request, $id)
    {
        $privacyPolicy = TermsAndCondition::find($id);

        $rules = [
            'privacy_policy' => 'required',
        ];
        $customMessages = [
            'privacy_policy.required' => trans('Privacy policy is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $privacyPolicy->privacy_policy = $request->privacy_policy;
        $privacyPolicy->save();

        $notification = trans('Updated Successfully');
        return response()->json(['message' => $notification], 200);
    }
}
