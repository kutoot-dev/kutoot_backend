<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerImage;
use App\Models\ShopPage;
use Image;
use File;
class AdvertisementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(){

        $sliderSidebarBannerOne = BannerImage::whereId('16')->select('link','image','id','banner_location','title_one','title_two')->first();

        $sliderSidebarBannerTwo = BannerImage::whereId('17')->select('link','image','id','banner_location')->first();

        $popularCategorySidebarBanner = BannerImage::whereId('18')->select('link','image','id','banner_location')->first();

        $homepageTwoColumnBannerOne = BannerImage::whereId('19')->select('link','image','id','banner_location')->first();

        $homepageTwoColumnBannerTwo = BannerImage::whereId('20')->select('link','image','id','banner_location')->first();

        $homepageSingleBannerOne = BannerImage::whereId('21')->select('link','image','id','banner_location')->first();

        $homepageSingleBannerTwo = BannerImage::whereId('22')->select('link','image','id','banner_location')->first();

        $megaMenuBanner = BannerImage::whereId('23')->select('link','image','id','banner_location')->first();

        $homepageFlashSaleSidebarBanner = BannerImage::whereId('24')->select('product_slug','image','id','banner_location','status','title','link')->first();
        
        $homepageFlashSaleSidebarBanner = BannerImage::whereId('24')->select('link','image','id','banner_location')->first();

        $shopPageCenterBanner = BannerImage::whereId('25')->select('link','image','id','banner_location','after_product_qty')->first();

        $shopPageSidebarBanner = BannerImage::whereId('26')->select('link','image','id','banner_location')->first();

        return response()->json([
            'sliderSidebarBannerOne' => $sliderSidebarBannerOne,
            'sliderSidebarBannerTwo' => $sliderSidebarBannerTwo,
            'popularCategorySidebarBanner' => $popularCategorySidebarBanner,
            'homepageTwoColumnBannerOne' => $homepageTwoColumnBannerOne,
            'homepageTwoColumnBannerTwo' => $homepageTwoColumnBannerTwo,
            'homepageSingleBannerOne' => $homepageSingleBannerOne,
            'homepageSingleBannerTwo' => $homepageSingleBannerTwo,
            'megaMenuBanner' => $megaMenuBanner,
            'homepageFlashSaleSidebarBanner' => $homepageFlashSaleSidebarBanner,
            'shopPageCenterBanner' => $shopPageCenterBanner,
            'shopPageSidebarBanner' => $shopPageSidebarBanner,
        ], 200);

    }



    public function megaMenuBannerUpdate(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $megaMenuBanner = BannerImage::whereId('23')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $megaMenuBanner->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $megaMenuBanner->image = $banner_name;
            $megaMenuBanner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $megaMenuBanner->link = $request->link;
        $megaMenuBanner->status = $request->status;
        $megaMenuBanner->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }


    public function updateSliderBannerOne(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $sliderSidebarBannerOne = BannerImage::whereId('16')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $sliderSidebarBannerOne->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $sliderSidebarBannerOne->image = $banner_name;
            $sliderSidebarBannerOne->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $sliderSidebarBannerOne->link = $request->link;
        $sliderSidebarBannerOne->status = $request->status;
        $sliderSidebarBannerOne->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updateSliderBannerTwo(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $sliderSidebarBannerTwo = BannerImage::whereId('17')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $sliderSidebarBannerTwo->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $sliderSidebarBannerTwo->image = $banner_name;
            $sliderSidebarBannerTwo->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $sliderSidebarBannerTwo->link = $request->link;
        $sliderSidebarBannerTwo->status = $request->status;
        $sliderSidebarBannerTwo->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function updatePopularCategorySidebar(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $popularCategorySidebarBanner = BannerImage::whereId('18')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $popularCategorySidebarBanner->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $popularCategorySidebarBanner->image = $banner_name;
            $popularCategorySidebarBanner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $popularCategorySidebarBanner->link = $request->link;
        $popularCategorySidebarBanner->status = $request->status;
        $popularCategorySidebarBanner->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function homepageTwoColFirstBanner(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $homepageTwoColumnBannerOne = BannerImage::whereId('19')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $homepageTwoColumnBannerOne->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $homepageTwoColumnBannerOne->image = $banner_name;
            $homepageTwoColumnBannerOne->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $homepageTwoColumnBannerOne->link = $request->link;
        $homepageTwoColumnBannerOne->status = $request->status;
        $homepageTwoColumnBannerOne->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function homepageTwoColSecondBanner(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $homepageTwoColumnBannerTwo = BannerImage::whereId('20')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $homepageTwoColumnBannerTwo->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $homepageTwoColumnBannerTwo->image = $banner_name;
            $homepageTwoColumnBannerTwo->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $homepageTwoColumnBannerTwo->link = $request->link;
        $homepageTwoColumnBannerTwo->status = $request->status;
        $homepageTwoColumnBannerTwo->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function homepageSinleFirstBanner(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $homepageSingleBannerOne = BannerImage::whereId('21')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $homepageSingleBannerOne->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $homepageSingleBannerOne->image = $banner_name;
            $homepageSingleBannerOne->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $homepageSingleBannerOne->link = $request->link;
        $homepageSingleBannerOne->status = $request->status;
        $homepageSingleBannerOne->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function homepageSinleSecondBanner(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $homepageSingleBannerTwo = BannerImage::whereId('22')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $homepageSingleBannerTwo->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $homepageSingleBannerTwo->image = $banner_name;
            $homepageSingleBannerTwo->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $homepageSingleBannerTwo->link = $request->link;
        $homepageSingleBannerTwo->status = $request->status;
        $homepageSingleBannerTwo->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function homepageFlashSaleSidebarBanner(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $homepageFlashSaleSidebarBanner = BannerImage::whereId('24')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $homepageFlashSaleSidebarBanner->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $homepageFlashSaleSidebarBanner->image = $banner_name;
            $homepageFlashSaleSidebarBanner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $homepageFlashSaleSidebarBanner->link = $request->link;
        $homepageFlashSaleSidebarBanner->status = $request->status;
        $homepageFlashSaleSidebarBanner->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function shopPageCenterBanner(Request $request){
        $rules = [
            'link' => 'required',
            'after_product_qty' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'after_product_qty.required' => trans('After product quantity is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

         $shopPageCenterBanner = BannerImage::whereId('25')->select('link','image','id','banner_location','after_product_qty')->first();

        if($request->banner_image){
            $existing_banner = $shopPageCenterBanner->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $shopPageCenterBanner->image = $banner_name;
            $shopPageCenterBanner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }
        $shopPageCenterBanner->after_product_qty = $request->after_product_qty;
        $shopPageCenterBanner->link = $request->link;
        $shopPageCenterBanner->status = $request->status;
        $shopPageCenterBanner->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function shopPageSidebarBanner(Request $request){
        $rules = [
            'link' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('Link is required'),
            'status.required' => trans('Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $shopPageSidebarBanner = BannerImage::whereId('26')->select('link','image','id','banner_location')->first();

        if($request->banner_image){
            $existing_banner = $shopPageSidebarBanner->image;
            $extention = $request->banner_image->getClientOriginalExtension();
            $banner_name = 'Mega-menu'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $banner_name = 'uploads/website-images/'.$banner_name;
            Image::make($request->banner_image)
                ->save(public_path().'/'.$banner_name);
            $shopPageSidebarBanner->image = $banner_name;
            $shopPageSidebarBanner->save();
            if($existing_banner){
                if(File::exists(public_path().'/'.$existing_banner))unlink(public_path().'/'.$existing_banner);
            }
        }

        $shopPageSidebarBanner->link = $request->link;
        $shopPageSidebarBanner->status = $request->status;
        $shopPageSidebarBanner->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

}
