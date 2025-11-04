<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerImage;
use App\Models\ShopPage;
use App\Models\Product;
use App\Models\Category;
use Image;
use File;
class AdvertisementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){

        $sliderSidebarBannerOne = BannerImage::whereId('16')->select('product_slug','image','id','banner_location','title_one','title_two','badge','status')->first();

        $sliderSidebarBannerTwo = BannerImage::whereId('17')->select('product_slug','image','id','banner_location','status','title_one','title_two','badge')->first();

        $popularCategorySidebarBanner = BannerImage::whereId('18')->select('product_slug','image','id','banner_location','status')->first();

        $homepageTwoColumnBannerOne = BannerImage::whereId('19')->select('product_slug','image','id','banner_location','status','title_one','title_two','badge')->first();

        $homepageTwoColumnBannerTwo = BannerImage::whereId('20')->select('product_slug','image','id','banner_location','status','title_one','title_two','badge')->first();

        $homepageSingleBannerOne = BannerImage::whereId('21')->select('product_slug','image','id','banner_location','status','title_one','title_two')->first();

        $homepageSingleBannerTwo = BannerImage::whereId('22')->select('product_slug','image','id','banner_location','status','title_one')->first();

        $megaMenuBanner = BannerImage::whereId('23')->select('product_slug','image','id','banner_location','status','title_one','title_two')->first();

        $homepageFlashSaleSidebarBanner = BannerImage::whereId('24')->select('product_slug','image','id','banner_location','status','title')->first();

        $shopPageCenterBanner = BannerImage::whereId('25')->select('product_slug','image','id','banner_location','after_product_qty','status','title_one')->first();

        $shopPageSidebarBanner = BannerImage::whereId('26')->select('product_slug','image','id','banner_location','status','title_one','title_two')->first();

        $products = Category::where(['status' => 1])->select('id','name','slug')->get();

        return view('admin.advertisement')->with([
            'products' => $products,
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
        ]);
    }



    public function megaMenuBannerUpdate(Request $request){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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
        $megaMenuBanner->product_slug = $request->product_slug;
        $megaMenuBanner->status = $request->status;
        $megaMenuBanner->title_one = $request->title_one;
        $megaMenuBanner->title_two = $request->title_two;
        $megaMenuBanner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateSliderBannerOne(Request $request){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
            'badge' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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

        $sliderSidebarBannerOne->product_slug = $request->product_slug;
        $sliderSidebarBannerOne->status = $request->status;
        $sliderSidebarBannerOne->title_one = $request->title_one;
        $sliderSidebarBannerOne->title_two = $request->title_two;
        $sliderSidebarBannerOne->badge = $request->badge;
        $sliderSidebarBannerOne->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updateSliderBannerTwo(Request $request){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
            'badge' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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
        $sliderSidebarBannerTwo->product_slug = $request->product_slug;
        $sliderSidebarBannerTwo->status = $request->status;
        $sliderSidebarBannerTwo->title_one = $request->title_one;
        $sliderSidebarBannerTwo->title_two = $request->title_two;
        $sliderSidebarBannerTwo->badge = $request->badge;
        $sliderSidebarBannerTwo->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function updatePopularCategorySidebar(Request $request){
        $rules = [
            'product_slug' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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
        $popularCategorySidebarBanner->product_slug = $request->product_slug;
        $popularCategorySidebarBanner->status = 1;
        $popularCategorySidebarBanner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function homepageTwoColFirstBanner(Request $request){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
            'badge' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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
        $homepageTwoColumnBannerOne->product_slug = $request->product_slug;
        $homepageTwoColumnBannerOne->status = $request->status;
        $homepageTwoColumnBannerOne->title_one = $request->title_one;
        $homepageTwoColumnBannerOne->title_two = $request->title_two;
        $homepageTwoColumnBannerOne->badge = $request->badge;
        $homepageTwoColumnBannerOne->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function homepageTwoColSecondBanner(Request $request){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
            'badge' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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
        $homepageTwoColumnBannerTwo->product_slug = $request->product_slug;
        $homepageTwoColumnBannerTwo->status = $request->status;
        $homepageTwoColumnBannerTwo->title_one = $request->title_one;
        $homepageTwoColumnBannerTwo->title_two = $request->title_two;
        $homepageTwoColumnBannerTwo->badge = $request->badge;
        $homepageTwoColumnBannerTwo->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function homepageSinleFirstBanner(Request $request){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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
        $homepageSingleBannerOne->product_slug = $request->product_slug;
        $homepageSingleBannerOne->status = $request->status;
        $homepageSingleBannerOne->title_one = $request->title_one;
        $homepageSingleBannerOne->title_two = $request->title_two;
        $homepageSingleBannerOne->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function homepageSinleSecondBanner(Request $request){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'title' => 'required',

        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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
        $homepageSingleBannerTwo->product_slug = $request->product_slug;
        $homepageSingleBannerTwo->status = $request->status;
        $homepageSingleBannerTwo->title_one = $request->title;
        $homepageSingleBannerTwo->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function homepageFlashSaleSidebarBanner(Request $request){
        $rules = [
            'link' => 'required',
            'link_two' => 'required',
            'status' => 'required'
        ];
        $customMessages = [
            'link.required' => trans('admin_validation.Play store link is required'),
            'link_two.required' => trans('admin_validation.App store link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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
        $homepageFlashSaleSidebarBanner->title = $request->link_two;
        $homepageFlashSaleSidebarBanner->status = $request->status;
        $homepageFlashSaleSidebarBanner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function shopPageCenterBanner(Request $request){
        $rules = [
            'product_slug' => 'required',
            'after_product_qty' => 'required',
            'status' => 'required',
            'title' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'after_product_qty.required' => trans('admin_validation.After product quantity is required'),
            'status.required' => trans('admin_validation.Status is required'),
        ];
        $this->validate($request, $rules,$customMessages);

         $shopPageCenterBanner = BannerImage::whereId('25')->first();

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
        $shopPageCenterBanner->product_slug = $request->product_slug;
        $shopPageCenterBanner->status = $request->status;
        $shopPageCenterBanner->title_one = $request->title;
        $shopPageCenterBanner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function shopPageSidebarBanner(Request $request){
        $rules = [
            'product_slug' => 'required',
            'status' => 'required',
            'title_one' => 'required',
            'title_two' => 'required',
        ];
        $customMessages = [
            'product_slug.required' => trans('admin_validation.Link is required'),
            'status.required' => trans('admin_validation.Status is required'),
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

        $shopPageSidebarBanner->product_slug = $request->product_slug;
        $shopPageSidebarBanner->status = $request->status;
        $shopPageSidebarBanner->title_one = $request->title_one;
        $shopPageSidebarBanner->title_two = $request->title_two;
        $shopPageSidebarBanner->save();

        $notification= trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

}
