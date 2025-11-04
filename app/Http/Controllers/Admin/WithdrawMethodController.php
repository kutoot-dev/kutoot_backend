<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawMethod;
use App\Models\Setting;
class WithdrawMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $methods = WithdrawMethod::all();
        $setting = Setting::first();

        return response()->json(['methods' => $methods, 'setting' => $setting], 200);
    }

    public function create(){
        $setting = Setting::first();
        return view('admin.create_withdraw_method',compact('setting'));
    }

    public function store(Request $request){
        $rules = [
            'name' => 'required',
            'minimum_amount' => 'required',
            'maximum_amount' => 'required',
            'withdraw_charge' => 'required',
            'description' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'minimum_amount.required' => trans('Minimum ammount is required'),
            'maximum_amount.required' => trans('Maximum ammount is required'),
            'withdraw_charge.required' => trans('Withdraw charge is required'),
            'description.required' => trans('Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $method = new WithdrawMethod();
        $method->name = $request->name;
        $method->min_amount = $request->minimum_amount;
        $method->max_amount = $request->maximum_amount;
        $method->withdraw_charge = $request->withdraw_charge;
        $method->description = $request->description;
        $method->status = 1;
        $method->save();

        $notification=trans('Create Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function show($id){
        $method = WithdrawMethod::find($id);
        $setting = Setting::first();

        return response()->json(['method' => $method, 'setting' => $setting], 200);
    }

    public function edit($id){
        $method = WithdrawMethod::find($id);
        $setting = Setting::first();
        return view('admin.edit_withdraw_method', compact('method','setting'));
    }


    public function update(Request $request, $id){

        $rules = [
            'name' => 'required',
            'minimum_amount' => 'required',
            'maximum_amount' => 'required',
            'withdraw_charge' => 'required',
            'description' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'minimum_amount.required' => trans('Minimum ammount is required'),
            'maximum_amount.required' => trans('Maximum ammount is required'),
            'withdraw_charge.required' => trans('Withdraw charge is required'),
            'description.required' => trans('Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $method = WithdrawMethod::find($id);
        $method->name = $request->name;
        $method->min_amount = $request->minimum_amount;
        $method->max_amount = $request->maximum_amount;
        $method->withdraw_charge = $request->withdraw_charge;
        $method->description = $request->description;
        $method->status = 1;
        $method->save();

        $notification=trans('Update Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function destroy($id){
        $method = WithdrawMethod::find($id);
        $method->delete();
        $notification=trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function changeStatus($id){
        $method = WithdrawMethod::find($id);
        if($method->status==1){
            $method->status=0;
            $method->save();
            $message= trans('Inactive Successfully');
        }else{
            $method->status=1;
            $method->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }
}
