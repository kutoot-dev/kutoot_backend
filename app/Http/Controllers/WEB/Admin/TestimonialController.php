<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use Image;
use File;
use Str;
use Cache;
class TestimonialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $testimonials = Testimonial::all();

        return view('admin.testimonial', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.create_testimonial');
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'designation' => 'required',
            'image' => 'required',
            'status' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'designation.required' => trans('admin_validation.Designation is required'),
            'image.required' => trans('admin_validation.Image is required'),
            'rating.required' => trans('admin_validation.Rating is required'),
            'comment.required' => trans('admin_validation.Comment is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $testimonial = new Testimonial();

        if($request->image){
            $extention = $request->image->getClientOriginalExtension();
            $image_name = Str::slug($request->name).date('-Ymdhis').'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;

            Image::make($request->image)
                ->save(public_path().'/'.$image_name);

        }

        $testimonial->name = $request->name;
        $testimonial->designation = $request->designation;
        $testimonial->image = $image_name;
        $testimonial->rating = $request->rating;
        $testimonial->comment = $request->comment;
        $testimonial->status = $request->status;
        $testimonial->save();

        $notification = trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.testimonial.index')->with($notification);
    }

    public function show($id)
    {
        $testimonial = Testimonial::find($id);
        return response()->json(['testimonial' => $testimonial],200);
    }


    public function edit($id)
    {
        $testimonial = Testimonial::find($id);
        return view('admin.edit_testimonial',compact('testimonial'));
    }


    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::find($id);
        $rules = [
            'name' => 'required',
            'designation' => 'required',
            'status' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'designation.required' => trans('admin_validation.Designation is required'),
            'rating.required' => trans('admin_validation.Rating is required'),
            'comment.required' => trans('admin_validation.Comment is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        if($request->image){
            $existing_image = $testimonial->image;
            $extention = $request->image->getClientOriginalExtension();
            $image_name = Str::slug($request->name).date('-Ymdhis').'.'.$extention;
            $image_name = 'uploads/custom-images/'.$image_name;
            Image::make($request->image)
                    ->save(public_path().'/'.$image_name);
                $testimonial->image= $image_name;
                $testimonial->save();
                if($existing_image){
                    if(File::exists(public_path().'/'.$existing_image))unlink(public_path().'/'.$existing_image);
                }
        }

        $testimonial->name = $request->name;
        $testimonial->designation = $request->designation;
        $testimonial->rating = $request->rating;
        $testimonial->comment = $request->comment;
        $testimonial->status = $request->status;
        $testimonial->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.testimonial.index')->with($notification);
    }


    public function destroy($id)
    {
        $testimonial = Testimonial::find($id);
        $existing_image = $testimonial->image;
        $testimonial->delete();

        if($existing_image){
            if(File::exists(public_path().'/'.$existing_image))unlink(public_path().'/'.$existing_image);
        }

        $notification = trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.testimonial.index')->with($notification);
    }

    public function changeStatus($id){
        $item = Testimonial::find($id);
        if($item->status == 1){
            $item->status = 0;
            $item->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $item->status = 1;
            $item->save();
            $message = trans('admin_validation.Active Successfully');
        }

        return response()->json($message);
    }
}
