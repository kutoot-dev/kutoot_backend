<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\BannerImage;
use App\Models\Role;
use Hash;
use Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

   public function index()
{
    $admins = Admin::with('role')->orderBy('id','asc')->get();
    $defaultProfile = BannerImage::whereId('15')->first();

    return view('admin.admin', compact('admins','defaultProfile'));
}


    public function create(){

       $roles = Role::orderBy('name', 'ASC')->get();
            return view('admin.create_admin', compact('roles'));
        $logedInAdmin = Auth::guard('admin')->user();

        if($logedInAdmin->admin_type == 1){
            return view('admin.create_admin');
        }else return abort(404);

    }


public function store(Request $request)
{
    $rules = [
        'name'     => 'required',
        'email'    => 'required|email|unique:admins,email',
        'password' => 'required|min:4',
        'role_id'  => 'required|exists:roles,id',
        'status'   => 'required',
    ];

    $customMessages = [
        'name.required'     => trans('admin_validation.Name is required'),
        'email.required'    => trans('admin_validation.Email is required'),
        'email.unique'      => trans('admin_validation.Email already exist'),
        'password.required' => trans('admin_validation.Password is required'),
        'password.min'      => trans('admin_validation.Password Must be 4 characters'),
        'role_id.required'  => trans('admin_validation.Role is required'),
    ];

    $this->validate($request, $rules, $customMessages);

    $admin = new Admin();
    $admin->name     = $request->name;          // ✅ added
    $admin->email    = $request->email;
    $admin->role_id  = $request->role_id;       // ✅ added
    $admin->status   = $request->status;
    $admin->password = Hash::make($request->password);
    $admin->save();

    $notification = [
        'messege'    => trans('admin_validation.Create Successfully'),
        'alert-type' => 'success'
    ];

    return redirect()->back()->with($notification);
}




        public function edit($id)
            {
                $admin = Admin::findOrFail($id);
                $roles = Role::orderBy('name')->get();

                return view('admin.form', compact('admin', 'roles'));
            }

        public function update(Request $request, $id)
            {
                $admin = Admin::findOrFail($id);

                $request->validate([
                    'name'  => 'required|string|max:255',
                    'email' => 'required|email|unique:admins,email,' . $admin->id,
                    'role_id' => 'nullable|exists:roles,id',
                    'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                ]);

                $admin->name    = $request->name;
                $admin->email   = $request->email;
                $admin->role_id = $request->role_id;

                // // Password (optional)
                // if ($request->filled('password')) {
                //     $admin->password = Hash::make($request->password);
                // }

                // Image upload
                if ($request->hasFile('image')) {
                    $image = $request->image;
                    $imageName = time().'.'.$image->getClientOriginalExtension();
                    $image->move(public_path('uploads/admin'), $imageName);
                    $admin->image = 'uploads/admin/'.$imageName;
                }

                $admin->save();

                return redirect()
                    ->route('admin.admin.index')
                    ->with('success', 'Admin updated successfully');
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
        $notification = trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function changeStatus($id){
        $admin = Admin::find($id);
        if($admin->status == 1){
            $admin->status = 0;
            $admin->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $admin->status = 1;
            $admin->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
