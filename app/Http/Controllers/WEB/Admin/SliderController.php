<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Product;
use App\Helpers\ImageHelper;

/**
 * @group Slider
 */
class SliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        $sliders = Slider::all();
        return view('admin.slider', compact('sliders'));
    }

    public function create(){
        $products = Product::where(['status' => 1])->select('id','name','slug')->get();
        return view('admin.create_slider', compact('products'));
    }

    public function store(Request $request){
        $rules = [
            'slider_image' => 'required',
            'product_slug' => 'required',
            'status' => 'required',
            'serial' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
            'badge' => 'required',
        ];
        $customMessages = [
            'slider_image.required' => trans('admin_validation.Slider image is required'),
            'title.required' => trans('admin_validation.Title is required'),
            'description.required' => trans('admin_validation.Description is required'),
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
            'serial.required' => trans('admin_validation.Serial is required'),
            'label.required' => trans('admin_validation.Label is required'),
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


        $slider->product_slug = $request->product_slug;
        $slider->serial = $request->serial;
        $slider->status = $request->status;
        $slider->title_one = $request->title_one;
        $slider->title_two = $request->title_two;
        $slider->badge = $request->badge;
        $slider->save();

        $notification= trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function show($id){
        $slider = Slider::find($id);
        return response()->json(['slider' => $slider], 200);
    }

    public function edit($id){
        $slider = Slider::find($id);
        $products = Product::where(['status' => 1])->select('id','name','slug')->get();
        return view('admin.edit_slider', compact('slider','products'));
    }

    public function update(Request $request, $id){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'serial' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
            'badge' => 'required',

        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
            'serial.required' => trans('admin_validation.Serial is required'),
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

        $slider->product_slug = $request->product_slug;
        $slider->serial = $request->serial;
        $slider->status = $request->status;
        $slider->title_one = $request->title_one;
        $slider->title_two = $request->title_two;
        $slider->badge = $request->badge;
        $slider->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.slider.index')->with($notification);
    }

    public function destroy($id){
        $slider = Slider::find($id);
        $existing_slider = $slider->image;
        $slider->delete();
        ImageHelper::delete($existing_slider);

        $notification= trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function changeStatus($id){
        $slider = Slider::find($id);
        if($slider->status==1){
            $slider->status=0;
            $slider->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $slider->status=1;
            $slider->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }


}
