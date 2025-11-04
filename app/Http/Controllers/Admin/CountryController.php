<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\User;
use Str;
class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $countries = Country::with('countryStates')->get();
        $billingAddress = BillingAddress::with('country','countryState','city')->get();
        $shippingAddress = ShippingAddress::with('country','countryState','city')->get();
        $users = User::with('seller','city','state','country')->get();

        return response()->json(['countries' => $countries, 'billingAddress' => $billingAddress, 'shippingAddress' => $shippingAddress, 'users' => $users], 200);
    }


    public function store(Request $request)
    {
        $rules = [
            'name'=>'required|unique:countries',
            'status'=>'required'
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $country=new Country();
        $country->name = $request->name;
        $country->slug = Str::slug($request->name);
        $country->status = $request->status;
        $country->save();

        $notification=trans('Created Successfully');
        return response()->json(['notification' => $notification], 200);
    }


    public function show($id)
    {
        $country = Country::find($id);
        return response()->json(['country' => $country], 200);
    }

    public function update(Request $request, $id)
    {
        $country = Country::find($id);
        $rules = [
            'name'=>'required|unique:countries,name,'.$country->id,
            'status'=>'required'
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'name.unique' => trans('Name already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $country->name = $request->name;
        $country->slug = Str::slug($request->name);
        $country->status = $request->status;
        $country->save();

        $notification=trans('Updated Successfully');
        return response()->json(['notification' => $notification], 200);
    }


    public function destroy($id)
    {
        $country = Country::find($id);
        $country->delete();
        $notification=trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function changeStatus($id){
        $country = Country::find($id);
        if($country->status==1){
            $country->status=0;
            $country->save();
            $message= trans('Inactive Successfully');
        }else{
            $country->status=1;
            $country->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }
}
