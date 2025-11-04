<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerImage;
use App\Models\PopularCategory;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\ThreeColumnCategory;
use App\Models\FeaturedCategory;
use App\Models\Setting;
use Image;
use File;
class HomePageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    public function homepage_section_content(){

        $setting = Setting::first();
        $sections = json_decode($setting->homepage_section_title);
        return view('admin.homepage_section_title', compact('sections'));
    }

    public function update_homepage_section_content(Request $request){

        $sections = array();
        foreach($request->customs as $index => $custom){
            $item = (object) array(
                'key' => $request->keys[$index],
                'default' => $request->defaults[$index],
                'custom' => $request->customs[$index],
            );

            $sections[] = $item;
        }

        $sections = json_encode($sections);

        $setting = Setting::first();
        $setting->homepage_section_title = $sections;
        $setting->save();


        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }
    public function popularCategory(){
        $popularCategories = PopularCategory::with('category')->get();

        $categories = Category::where('status', 1)->get();

        $banner = Setting::select('popular_category_banner')->first();

        return view('admin.home_page_banner', compact('popularCategories', 'categories','banner'));
    }

    public function bannerPopularCategory(Request $request){
        $setting = Setting::first();
        if($request->image){
            $old_logo=$setting->popular_category_banner;
            $image=$request->image;
            $ext=$image->getClientOriginalExtension();
            $logo_name= 'popular-cat-banner-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$ext;
            $logo_name='uploads/website-images/'.$logo_name;
            $logo=Image::make($image)
                    ->save(public_path().'/'.$logo_name);
            $setting->popular_category_banner=$logo_name;
            $setting->save();

            if($old_logo){
                if(File::exists(public_path().'/'.$old_logo))unlink(public_path().'/'.$old_logo);
            }
        }


        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function storePopularCategory(Request $request){

        $isExist = 0;
        if($request->category_id){
            $isExist = PopularCategory::where('category_id', $request->category_id)->count();
        }

        $rules = [
            'category_id' => $isExist == 0 ? 'required' : 'required|unique:popular_categories',
        ];
        $customMessages = [
            'category_id.required' => trans('admin_validation.Category is required'),
            'category_id.unique' => trans('admin_validation.Category already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = new PopularCategory();
        $category->category_id = $request->category_id;
        $category->save();

        $notification= trans('admin_validation.Create Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function destroyPopularCategory($id){

        $category = PopularCategory::where('id', $id)->first();
        $category->delete();

        $notification= trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }


    public function featuredCategory(){
        $featuredCategories = FeaturedCategory::with('category')->get();
        $categories = Category::where('status', 1)->get();

        $banner = Setting::select('featured_category_banner')->first();
        return view('admin.featured_category', compact('featuredCategories', 'categories','banner'));
    }


    public function bannerFeaturedCategory(Request $request){
        $setting = Setting::first();
        if($request->image){
            $old_logo=$setting->featured_category_banner;
            $image=$request->image;
            $ext=$image->getClientOriginalExtension();
            $logo_name= 'featured-cat-banner-'.date('Y-m-d-h-i-s-').rand(999,9999).'.'.$ext;
            $logo_name='uploads/website-images/'.$logo_name;
            $logo=Image::make($image)
                    ->save(public_path().'/'.$logo_name);
            $setting->featured_category_banner=$logo_name;
            $setting->save();

            if($old_logo){
                if(File::exists(public_path().'/'.$old_logo))unlink(public_path().'/'.$old_logo);
            }
        }


        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function storeFeaturedCategory(Request $request){

        $isExist = 0;
        if($request->category_id){
            $isExist = FeaturedCategory::where('category_id', $request->category_id)->count();
        }

        $rules = [
            'category_id' => $isExist == 0 ? 'required' : 'required|unique:featured_categories',
        ];
        $customMessages = [
            'category_id.required' => trans('admin_validation.Category is required'),
            'category_id.unique' => trans('admin_validation.Category already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = new FeaturedCategory();
        $category->category_id = $request->category_id;
        $category->save();

        $notification= trans('admin_validation.Create Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function destroyFeaturedCategory($id){

        $category = FeaturedCategory::where('id', $id)->first();
        $category->delete();

        $notification= trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

}
