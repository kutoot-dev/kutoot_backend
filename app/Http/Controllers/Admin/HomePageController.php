<?php

namespace App\Http\Controllers\Admin;

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
/**
 * @group Home Page
 */
class HomePageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }


    public function popularCategory(){
        $popularCategories = PopularCategory::with('category')->get();
        return response()->json(['popularCategories' => $popularCategories], 200);
    }


    public function storePopularCategory(Request $request){

        $rules = [
            'title' => 'required',
            'first_category_id' => 'required',
            'second_category_id' => 'nullable',
            'third_category_id' => 'nullable',
        ];
        $customMessages = [
            'title.required' => trans('Title is required'),
            'first_category_id.required' => trans('First category is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $popularCategory = new PopularCategory();
        $popularCategory->title = $request->title;
        $popularCategory->first_category_id = $request->first_category_id;
        $popularCategory->second_category_id = $request->second_category_id;
        $popularCategory->third_category_id = $request->third_category_id;
        $popularCategory->save();

        $notification= trans('Create Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function destroyPopularCategory($id){

        $category = PopularCategory::where('id', $id)->first();
        $category->delete();

        $notification= trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }


    public function featuredCategory(){
        $featuredCategories = FeaturedCategory::with('category')->get();
        return response()->json(['featuredCategories' => $featuredCategories], 200);
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
            'category_id.required' => trans('Category is required'),
            'category_id.unique' => trans('Category already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $category = new FeaturedCategory();
        $category->category_id = $request->category_id;
        $category->save();

        $notification= trans('Create Successfully');
        return response()->json(['notification' => $notification], 200);
    }

    public function destroyFeaturedCategory($id){

        $category = FeaturedCategory::where('id', $id)->first();
        $category->delete();

        $notification= trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }

}
