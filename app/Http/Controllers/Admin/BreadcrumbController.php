<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BreadcrumbImage;
use Image;
use File;
class BreadcrumbController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){
        $images = BreadcrumbImage::orderBy('id','asc')->get();

        return response()->json(['images' => $images], 200);
    }

    public function show($id){
        $image = BreadcrumbImage::find($id);
        return response()->json(['image' => $image], 200);
    }

    public function update(Request $request, $id){
        $rules = [
            'image' => 'required',
        ];
        $this->validate($request, $rules);
        $image = BreadcrumbImage::find($id);
        if($request->image){
            $exist_banner = $image->image;
            $extention = $request->image->getClientOriginalExtension();
            $banner_name = 'banner-us'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->image)
                ->save(public_path().'/'.$banner_name);
            $image->image = $banner_name;
            $image->save();
            if($exist_banner){
                if(File::exists(public_path().'/'.$exist_banner))unlink(public_path().'/'.$exist_banner);
            }
        }

        $notification = 'Updated Successfully';
        return response()->json(['notification' => $notification], 200);
    }
}
