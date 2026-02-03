<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Helpers\ImageHelper;

/**
 * @group Slider
 */
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
        if($request->hasFile('slider_image')){
            $slider->image = ImageHelper::upload(
                $request->file('slider_image'),
                'custom-images',
                'slider',
                'slider',
                80,
                null,
                true
            );
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
        if($request->hasFile('slider_image')){
            $slider->image = ImageHelper::upload(
                $request->file('slider_image'),
                'custom-images',
                'slider',
                'slider',
                80,
                $slider->image,
                true
            );
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
        ImageHelper::delete($existing_slider);

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
