<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Setting;
class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $coupons = Coupon::orderBy('id','desc')->get();
        $setting = Setting::first();

        return view('admin.coupon', compact('coupons','setting'));
    }

    public function store(Request $request){
        $rules = [
            'name'=>'required',
            'code'=>'required|unique:coupons',
             'number_of_time'=>'required|numeric',
            // 'min_purchase_price'=>'required|numeric',
            // 'offer_type'=>'required',
            'discount'=>'required|numeric',
            'status'=>'required',
            // 'expired_date'=>'required',
            'status'=>'required',
        ];
        $customMessages = [
            'code.required' => trans('admin_validation.Code is required'),
            'code.unique' => trans('admin_validation.Code already exist'),
            'name.required' => trans('admin_validation.Name is required'),
            'number_of_time.required' => trans('admin_validation.Number of time is required'),
            // 'offer_type.required' => trans('admin_validation.Offer type is required'),
            'discount.required' => trans('admin_validation.Discount is required'),
            'status.required' => trans('admin_validation.Status is required'),
            // 'expired_date.required' => trans('admin_validation.Expired date is required'),
            // 'min_purchase_price.required' => trans('admin_validation.Minimum price is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $coupon = new Coupon();
        $coupon->name = $request->name;
        $coupon->code = $request->code;
        $coupon->max_quantity = $request->number_of_time;
        // $coupon->min_purchase_price = $request->min_purchase_price;
        // $coupon->expired_date = $request->expired_date;
        // $coupon->offer_type = $request->offer_type;
        $coupon->discount = $request->discount;
        $coupon->status = $request->status;
        $coupon->save();

        $notification=trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function update(Request $request, $id){
        $rules = [
            'name'=>'required',
            'code'=>'required|unique:coupons,code,'.$id,
            'number_of_time'=>'required|numeric',
            'min_purchase_price'=>'required|numeric',
            'offer_type'=>'required',
            'discount'=>'required|numeric',
            'status'=>'required',
            'expired_date'=>'required',
            'status'=>'required',
        ];
        $customMessages = [
            'code.required' => trans('admin_validation.Code is required'),
            'code.unique' => trans('admin_validation.Code already exist'),
            'name.required' => trans('admin_validation.Name is required'),
            'number_of_time.required' => trans('admin_validation.Number of time is required'),
            'offer_type.required' => trans('admin_validation.Offer type is required'),
            'discount.required' => trans('admin_validation.Discount is required'),
            'status.required' => trans('admin_validation.Status is required'),
            'expired_date.required' => trans('admin_validation.Expired date is required'),
            'min_purchase_price.required' => trans('admin_validation.Minimum price is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $coupon = Coupon::find($id);
        $coupon->name = $request->name;
        $coupon->code = $request->code;
        $coupon->max_quantity = $request->number_of_time;
        $coupon->min_purchase_price = $request->min_purchase_price;
        $coupon->offer_type = $request->offer_type;
        $coupon->discount = $request->discount;
        $coupon->expired_date = $request->expired_date;
        $coupon->status = $request->status;
        $coupon->save();

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function show($id){
        $coupon = Coupon::find($id);
        return response()->json(['coupon' => $coupon], 200);
    }

    public function destroy($id){
        $coupon = Coupon::find($id);
        $coupon->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function changeStatus($id){
        $coupon = Coupon::find($id);
        if($coupon->status == 1){
            $coupon->status = 0;
            $coupon->save();
            $message =  trans('admin_validation.Inactive Successfully');
        }else{
            $coupon->status = 1;
            $coupon->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

}
