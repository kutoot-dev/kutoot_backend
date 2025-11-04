<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CountryState;
use Str;
use App\Models\Country;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\User;
class CountryStateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $states = CountryState::with('cities','country')->get();
        $billingAddress = BillingAddress::with('country','countryState','city')->get();
        $shippingAddress = ShippingAddress::with('country','countryState','city')->get();
        $users = User::with('seller','city','state','country')->get();

        return response()->json(['states' => $states, 'billingAddress' => $billingAddress, 'shippingAddress' => $shippingAddress, 'users' => $users], 200);
    }


    public function store(Request $request)
    {
        $rules = [
            'country'=>'required',
            'name'=>'required|unique:country_states',
            'status' => 'required',
        ];
        $customMessages = [
            'country.required' => trans('Country is required'),
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $state=new CountryState();
        $state->country_id=$request->country;
        $state->name=$request->name;
        $state->slug=Str::slug($request->name);
        $state->status=$request->status;
        $state->save();

        $notification=trans('Created Successfully');
        return response()->json(['notification' => $notification], 200);
    }


    public function show($id)
    {
        $state = CountryState::with('cities','country')->find($id);
        $countries = Country::with('countryStates')->get();
        return response()->json(['countries' => $countries, 'state' => $state], 200);

    }

    public function update(Request $request, $id)
    {
        $state = CountryState::find($id);
        $rules = [
            'country'=>'required',
            'name'=>'required|unique:country_states,name,'.$state->id,
            'status' => 'required'
        ];
        $customMessages = [
            'country.required' => trans('Country is required'),
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $state->country_id=$request->country;
        $state->name=$request->name;
        $state->slug=Str::slug($request->name);
        $state->status=$request->status;
        $state->save();

        $notification=trans('Updated Successfully');
        return response()->json(['notification' => $notification], 200);
    }


    public function destroy($id)
    {
        $state = CountryState::find($id);
        $state->delete();
        $notification=trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function changeStatus($id){
        $state = CountryState::find($id);
        if($state->status==1){
            $state->status=0;
            $state->save();
            $message= trans('Inactive Successfully');
        }else{
            $state->status=1;
            $state->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }
}
