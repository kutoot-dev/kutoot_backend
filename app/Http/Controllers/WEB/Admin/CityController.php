<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\CountryState;
use App\Models\Country;
use Str;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\User;

use App\Exports\CityExport;
use App\Imports\CityImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $cities = City::with('countryState','addressCities')->get();

        return view('admin.city', compact('cities'));
    }

    public function create()
    {
        $countries = Country::all();
        return view('admin.create_city', compact('countries'));
    }

    public function store(Request $request)
    {
        $rules = [
            'country'=>'required',
            'state'=>'required',
            'name'=>'required|unique:cities',
            'status'=>'required',
        ];

        $customMessages = [
            'country.required' => trans('admin_validation.Country is required'),
            'state.required' => trans('admin_validation.State is required'),
            'name.required' => trans('admin_validation.Name is required'),
            'name.unique' => trans('admin_validation.Name already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $city=new City();
        $city->country_state_id=$request->state;
        $city->name=$request->name;
        $city->slug=Str::slug($request->name);
        $city->status=$request->status;
        $city->save();

        $notification=trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function show($id)
    {
        $states = CountryState::with('cities','country')->get();
        $city = City::with('countryState')->find($id);
        $countries = Country::with('countryStates')->get();

        return response()->json(['states' => $states, 'city' => $city, 'countries' => $countries], 200);
    }


    public function update(Request $request, $id)
    {
        $city = City::find($id);
        $rules = [
            'country'=>'required',
            'state'=>'required',
            'name'=>'required|unique:cities,name,'.$city->id,
            'status'=>'required',
        ];
        $customMessages = [
            'country.required' => trans('admin_validation.Country is required'),
            'state.required' => trans('admin_validation.State is required'),
            'name.required' => trans('admin_validation.Name is required'),
            'name.unique' => trans('admin_validation.Name already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $city->country_state_id=$request->state;
        $city->name=$request->name;
        $city->slug=Str::slug($request->name);
        $city->status=$request->status;
        $city->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.city.index')->with($notification);
    }

    public function edit($id)
    {
        $states = CountryState::all();
        $city = City::find($id);
        $countries = Country::all();
        return view('admin.edit_city', compact('states','city','countries'));
    }


    public function destroy($id)
    {
        $city = City::find($id);
        $city->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.city.index')->with($notification);
    }

    public function changeStatus($id){
        $city = City::find($id);
        if($city->status==1){
            $city->status=0;
            $city->save();
            $message=trans('admin_validation.Inactive Successfully');
        }else{
            $city->status=1;
            $city->save();
            $message=trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }


    public function city_import_page()
    {
        return view('admin.city_import_page');
    }

    public function city_export()
    {
        $is_dummy = false;
        return Excel::download(new CityExport($is_dummy), 'cities.xlsx');
    }


    public function demo_city_export()
    {
        $is_dummy = true;
        return Excel::download(new CityExport($is_dummy), 'cities.xlsx');
    }



    public function city_import(Request $request)
    {

        try{
            Excel::import(new CityImport, $request->file('import_file'));

            $notification=trans('Uploaded Successfully');
            $notification=array('messege'=>$notification,'alert-type'=>'success');
            return redirect()->back()->with($notification);

        }catch(Exception $ex){
            $notification=trans('Please follow the instruction and input the value carefully');
            $notification=array('messege'=>$notification,'alert-type'=>'error');
            return redirect()->back()->with($notification);
        }


    }
}
