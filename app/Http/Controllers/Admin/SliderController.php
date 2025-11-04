<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use Image;
use File;
class SliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $sliders = Slider::all();
        return response()->json(['sliders' => $sliders], 200);
    }

    public function store(Request $request){
        $rules = [
            'slider_image' => 'required',
            'title' => 'required',
            'description' => 'required',
            'button_link' => 'required',
            'status' => 'required',
            'serial' => 'required',
            'label' => 'required',

        ];
        $customMessages = [
            'slider_image.required' => trans('Slider image is required'),
            'title.required' => trans('Title is required'),
            'description.required' => trans('Description is required'),
            'button_link.required' => trans('Button link is required'),
            'status.required' => trans('Status is required'),
            'serial.required' => trans('Serial is required'),
            'label.required' => trans('Label is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $slider = new Slider();
        if($request->slider_image){
            $extention = $request->slider_image->getClientOriginalExtension();
            $slider_image = 'slider'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $slider_image = 'uploads/custom-images/'.$slider_image;
            Image::make($request->slider_image)
                ->save(public_path().'/'.$slider_image);
            $slider->image = $slider_image;
        }

        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->link = $request->button_link;
        $slider->serial = $request->serial;
        $slider->status = $request->status;
        $slider->label = $request->label;
        $slider->save();

        $notification= trans('Created Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function show($id){
        $slider = Slider::find($id);
        return response()->json(['slider' => $slider], 200);
    }


    public function update(Request $request, $id){
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'button_link' => 'required',
            'status' => 'required',
            'serial' => 'required',
            'label' => 'required',
        ];
        $customMessages = [
            'title.required' => trans('Title is required'),
            'description.required' => trans('Description is required'),
            'button_link.required' => trans('Button link is required'),
            'status.required' => trans('Status is required'),
            'serial.required' => trans('Serial is required'),
            'label.required' => trans('Label is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $slider = Slider::find($id);
        if($request->slider_image){
            $existing_slider = $slider->image;
            $extention = $request->slider_image->getClientOriginalExtension();
            $slider_image = 'slider'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $slider_image = 'uploads/custom-images/'.$slider_image;
            Image::make($request->slider_image)
                ->save(public_path().'/'.$slider_image);
            $slider->image = $slider_image;
            $slider->save();
            if($existing_slider){
                if(File::exists(public_path().'/'.$existing_slider))unlink(public_path().'/'.$existing_slider);
            }
        }

        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->link = $request->button_link;
        $slider->serial = $request->serial;
        $slider->status = $request->status;
        $slider->label = $request->label;
        $slider->save();

        $notification= trans('Update Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function destroy($id){
        $slider = Slider::find($id);
        $existing_slider = $slider->image;
        $slider->delete();
        if($existing_slider){
            if(File::exists(public_path().'/'.$existing_slider))unlink(public_path().'/'.$existing_slider);
        }

        $notification= trans('Delete Successfully');
        return response()->json(['notification' => $notification], 200);
    }


    public function changeStatus($id){
        $slider = Slider::find($id);
        if($slider->status==1){
            $slider->status=0;
            $slider->save();
            $message= trans('Inactive Successfully');
        }else{
            $slider->status=1;
            $slider->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }


}
