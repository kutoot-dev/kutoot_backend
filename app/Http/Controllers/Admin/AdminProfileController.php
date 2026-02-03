<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\BannerImage;
use Hash;
use Auth;
use Image;
use Str;
use File;
/**
 * @group Admin Profile
 */
class AdminProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $admin=Auth::guard('admin')->user();
        $defaultProfile = BannerImage::whereId('15')->first();
        return view('admin.admin_profile',compact('admin','defaultProfile'));
    }

    public function update(Request $request){
        $admin=Auth::guard('admin')->user();
        $rules = [
            'name'=>'required',
            'email'=>'required|unique:admins,email,'.$admin->id,
            'password'=>'confirmed',
        ];
        $customMessages = [
            'name.required' => trans('Name is required'),
            'email.required' => trans('Email is required'),
            'email.unique' => trans('Email already exist'),
            'password.confirmed' => trans('Confirm password does not match'),
        ];
        $this->validate($request, $rules,$customMessages);

        $this->validate($request, $rules);
        $admin=Auth::guard('admin')->user();

        // insert user profile image: ensure directory exists and is writable
        if ($request->file('image')) {
            $old_image = $admin->image;
            $user_image = $request->image;
            $extension = $user_image->getClientOriginalExtension();
            $fileName = Str::slug($request->name) . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.' . $extension;
            $relativePath = 'uploads/website-images/' . $fileName;
            $destination = public_path('uploads/website-images');

            // create directory if it doesn't exist
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // try to fix permissions if not writable (may be ineffective on some OS)
            if (!is_writable($destination)) {
                @chmod($destination, 0755);
            }

            // final writable check
            if (!is_writable($destination)) {
                $notification = trans('Upload directory is not writable');
                $notification = array('messege' => $notification, 'alert-type' => 'error');
                return redirect()->back()->with($notification);
            }

            Image::make($user_image)->save(public_path($relativePath));

            $admin->image = $relativePath;
            $admin->save();

            if ($old_image) {
                if (File::exists(public_path($old_image))) unlink(public_path($old_image));
            }
        }

        if($request->password){
            $admin->password=Hash::make($request->password);
        }
        $admin->name=$request->name;
        $admin->email=$request->email;
        $admin->save();

        $notification= trans('Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.profile')->with($notification);


    }
}
