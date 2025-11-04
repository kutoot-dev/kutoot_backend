<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\BannerImage;
use Hash;
use Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $admins = Admin::orderBy('id','asc')->get();
        $defaultProfile = BannerImage::whereId('15')->first();
        return response()->json(['admins' => $admins, 'defaultProfile' => $defaultProfile], 200);

    }

    public function store(Request $request){
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:admins',
            'password' => 'required|min:4',
            'status' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'email.required' => trans('Email is required'),
            'email.unique' => trans('Email already exist'),
            'password.required' => trans('Password is required'),
            'password.min' => trans('Password Must be 4 characters'),
        ];
        $this->validate($request, $rules,$customMessages);

        $admin = new Admin();
        $admin->name =$request->name;
        $admin->email =$request->email;
        $admin->status =$request->status;
        $admin->password =Hash::make($request->status);
        $admin->save();

        $notification = trans('Create Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function update(Request $request, $id){

        $admin = Admin::find($id);
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:admins,email,'. $admin->id,
            'password' => 'required|min:4',
            'status' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'email.required' => trans('Email is required'),
            'email.unique' => trans('Email already exist'),
            'password.required' => trans('Password is required'),
            'password.min' => trans('Password Must be 4 characters'),
        ];
        $this->validate($request, $rules,$customMessages);


        $admin->name =$request->name;
        $admin->email =$request->email;
        $admin->status =$request->status;
        $admin->password =Hash::make($request->status);
        $admin->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }


    public function show($id){
        $admin = Admin::find($id);
        return response()->json(['admin' => $admin], 200);
    }

    public function destroy($id){
        $admin = Admin::find($id);
        $old_image = $admin->image;
        $admin->delete();
        if($old_image){
            if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
        }
        $notification = trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function changeStatus($id){
        $admin = Admin::find($id);
        if($admin->status == 1){
            $admin->status = 0;
            $admin->save();
            $message = trans('Inactive Successfully');
        }else{
            $admin->status = 1;
            $admin->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }
}
