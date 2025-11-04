<?php

namespace App\Http\Controllers\WEB\Admin;

use File;
use Image;
use App\Models\Order;
use App\Models\Setting;
use App\Models\DeliveryMan;
use App\Models\OrderAmount;
use Illuminate\Http\Request;
use App\Models\DeliveryManReview;
use App\Models\DeliveryManWithdraw;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class DeliveryManController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveryMans=DeliveryMan::latest()->get();
        return view('admin.delivery_man', compact('deliveryMans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create_delivery_men');
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
            'man_image'=>'required',
            'fname'=>'required',
            'lname'=>'required',
            'email'=>'required|email|unique:delivery_men,email',
            'man_type'=>'required',
            'idn_type'=>'required',
            'idn_num'=>'required',
            'idn_image'=>'required',
            'phone'=>'required',
            'password'=>'required|min:4',
            'c_password' => 'required|same:password',
        ];
        $customMessages = [
            'man_image.required' => trans('Delivery man image is required'),
            'fname.required' => trans('First name is required'),
            'lname.required' => trans('Last name is required'),
            'email.required' => trans('Email is required'),
            'email.email' => trans('Must be email'),
            'email.unique' => trans('Email already exist'),
            'man_type.required' => trans('Delivery man type is required'),
            'idn_type.required' => trans('Identity type is required'),
            'idn_num.required' => trans('Identity number is required'),
            'idn_image.required' => trans('Identity image is required'),
            'phone.required' => trans('Phone is required'),
            'password.required' => trans('Password is required'),
            'password.min' => trans('Password minimum 4 character'),
            'c_password.required' => trans('Confirm password is required'),
            'c_password.same' => trans('Confirm password do not match'),
        ];
        $this->validate($request, $rules,$customMessages);
        $man = new DeliveryMan();
        if($request->man_image){
            $man_extention=$request->man_image->getClientOriginalExtension();
            $man_image_name = 'man-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$man_extention;
            $man_image_name ='uploads/custom-images/'.$man_image_name;
            Image::make($request->man_image)->save(public_path().'/'.$man_image_name);
            $man->man_image = $man_image_name;
        }
        $man->fname=$request->fname;
        $man->lname=$request->lname;
        $man->email=$request->email;
        $man->man_type=$request->man_type;
        $man->idn_type=$request->idn_type;
        $man->idn_num=$request->idn_num;
        if($request->idn_image){
            $idn_extention=$request->idn_image->getClientOriginalExtension();
            $idn_image_name = 'identity-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$idn_extention;
            $idn_image_name ='uploads/custom-images/'.$idn_image_name;
            Image::make($request->idn_image)->save(public_path().'/'.$idn_image_name);
            $man->idn_image = $idn_image_name;
        }
        $man->phone=$request->phone;
        $man->password=Hash::make($request->password);
        $man->status=1;
        $man->save();
        $notification= trans('admin_validation.Created Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $deliveryman=DeliveryMan::findOrFail($id);
        $completeOrder=Order::where('delivery_man_id', $id)->where('order_status', '=', 3)->get();
        $runingOrder=Order::where('delivery_man_id', $id)->where('order_status', '!=', 3)->where('order_status', '!=', 4)->get();
        
        $tota_earn=Order::where('delivery_man_id', $id)->where('order_status', '=', 3)->sum('shipping_cost');
        $setting = Setting::first();

        $deliveryManWithdraw=DeliveryManWithdraw::where('delivery_man_id', $id)->sum('total_amount');

        $order_total_amount=Order::where('delivery_man_id', $id)->where('order_status', '=', 3)->where('cash_on_delivery', '=', 1)->sum('total_amount');
        
        $given_amount=OrderAmount::where('delivery_man_id', $id)->sum('total_amount');
        
        $current_product_amount= $order_total_amount-$given_amount;
        
        return view('admin.show_delivery_man', compact('deliveryman', 'completeOrder', 'runingOrder', 'setting', 'tota_earn', 'current_product_amount', 'deliveryManWithdraw'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $deliveryman=DeliveryMan::findOrFail($id);
        return view('admin.edit_delivery_man', compact('deliveryman'));
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
            'fname'=>'required',
            'lname'=>'required',
            'email'=>'required|email|unique:delivery_men,email,'.$id,
            'man_type'=>'required',
            'idn_type'=>'required',
            'idn_num'=>'required',
            'phone'=>'required',
        ];
        $customMessages = [
            'man_image.required' => trans('Delivery man image is required'),
            'fname.required' => trans('First name is required'),
            'lname.required' => trans('Last name is required'),
            'email.required' => trans('Email is required'),
            'email.email' => trans('Must be email'),
            'email.unique' => trans('Email already exist'),
            'man_type.required' => trans('Delivery man type is required'),
            'idn_type.required' => trans('Identity type is required'),
            'idn_num.required' => trans('Identity number is required'),
            'idn_image.required' => trans('Identity image is required'),
            'phone.required' => trans('Phone is required'),
        ];
        $this->validate($request, $rules,$customMessages);
        $man = DeliveryMan::findOrFail($id);
        $man_old_image=$man->man_image;
        $idn_old_image=$man->idn_image;
        if($request->man_image){
            $man_extention=$request->man_image->getClientOriginalExtension();
            $man_image_name = 'man-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$man_extention;
            $man_image_name ='uploads/custom-images/'.$man_image_name;
            Image::make($request->man_image)->save(public_path().'/'.$man_image_name);
            $man->man_image = $man_image_name;
            if($man_old_image){
                if(File::exists(public_path().'/'.$man_old_image))unlink(public_path().'/'.$man_old_image);
            }
        }
        $man->fname=$request->fname;
        $man->lname=$request->lname;
        $man->email=$request->email;
        $man->man_type=$request->man_type;
        $man->idn_type=$request->idn_type;
        $man->idn_num=$request->idn_num;
        if($request->idn_image){
            $idn_extention=$request->idn_image->getClientOriginalExtension();
            $idn_image_name = 'identity-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$idn_extention;
            $idn_image_name ='uploads/custom-images/'.$idn_image_name;
            Image::make($request->idn_image)->save(public_path().'/'.$idn_image_name);
            $man->idn_image = $idn_image_name;
            if($idn_old_image){
                if(File::exists(public_path().'/'.$idn_old_image))unlink(public_path().'/'.$idn_old_image);
            }
        }
        $man->phone=$request->phone;
        $man->save();
        $notification= trans('admin_validation.Updated Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.delivery-man.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deliveryman = DeliveryMan::find($id);
        $man_old_image = $deliveryman->man_image;
        $idn_old_image = $deliveryman->idn_image;
        $deliveryman->delete();
        if($man_old_image){
            if(File::exists(public_path().'/'.$man_old_image))unlink(public_path().'/'.$man_old_image);
        }
        if($idn_old_image){
            if(File::exists(public_path().'/'.$idn_old_image))unlink(public_path().'/'.$idn_old_image);
        }
        $notification=  trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function changeStatus($id){
        $deliveryman = DeliveryMan::find($id);
        if($deliveryman->status == 1){
            $deliveryman->status = 0;
            $deliveryman->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $deliveryman->status = 1;
            $deliveryman->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    public function review(){
        $deliveryManReviews=DeliveryManReview::with('deliveryman', 'user', 'order')->latest()->get();
        return view('admin.delivery_man_review', compact('deliveryManReviews'));
    }

    public function deleteReview($id){
        $review = DeliveryManReview::find($id);
        $review->delete();
        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return back()->with($notification);
    }

    public function changeReviewStatus($id){
        $review = DeliveryManReview::find($id);
        if($review->status==1){
            $review->status=0;
            $review->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $review->status=1;
            $review->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
