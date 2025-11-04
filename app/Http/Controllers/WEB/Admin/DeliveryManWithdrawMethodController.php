<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeliveryManWithdrawMethod;

class DeliveryManWithdrawMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $methods = DeliveryManWithdrawMethod::all();
        $setting = Setting::first();
        return view('admin.delivery_man_withdrow_method', compact('methods', 'setting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create_withdraw_method_delivery_man');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'minimum_amount' => 'required',
            'maximum_amount' => 'required',
            'withdraw_charge' => 'required',
            'description' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'minimum_amount.required' => trans('admin_validation.Minimum ammount is required'),
            'maximum_amount.required' => trans('admin_validation.Maximum ammount is required'),
            'withdraw_charge.required' => trans('admin_validation.Withdraw charge is required'),
            'description.required' => trans('admin_validation.Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $method = new DeliveryManWithdrawMethod();
        $method->name = $request->name;
        $method->min_amount = $request->minimum_amount;
        $method->max_amount = $request->maximum_amount;
        $method->withdraw_charge = $request->withdraw_charge;
        $method->description = $request->description;
        $method->status = 1;
        $method->save();

        $notification=trans('admin_validation.Create Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.delivery-man-withdraw-method.index')->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $method = DeliveryManWithdrawMethod::find($id);
        $setting = Setting::first();
        return view('admin.edit_withdraw_method_delivery_man', compact('method','setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'minimum_amount' => 'required',
            'maximum_amount' => 'required',
            'withdraw_charge' => 'required',
            'description' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'minimum_amount.required' => trans('admin_validation.Minimum ammount is required'),
            'maximum_amount.required' => trans('admin_validation.Maximum ammount is required'),
            'withdraw_charge.required' => trans('admin_validation.Withdraw charge is required'),
            'description.required' => trans('admin_validation.Description is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $method = DeliveryManWithdrawMethod::find($id);
        $method->name = $request->name;
        $method->min_amount = $request->minimum_amount;
        $method->max_amount = $request->maximum_amount;
        $method->withdraw_charge = $request->withdraw_charge;
        $method->description = $request->description;
        $method->status = 1;
        $method->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.delivery-man-withdraw-method.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $method = DeliveryManWithdrawMethod::find($id);
        $method->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.delivery-man-withdraw-method.index')->with($notification);
    }

    public function changeStatus($id){
        $method = DeliveryManWithdrawMethod::find($id);
        if($method->status==1){
            $method->status=0;
            $method->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $method->status=1;
            $method->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
