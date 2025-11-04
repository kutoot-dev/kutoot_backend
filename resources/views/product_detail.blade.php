@extends('layout')
@section('title')
    <title>{{ $product->seo_title }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $product->seo_description }} {{ $tags }}">
@endsection

@section('public-content')


    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{  asset($product->banner_image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('Product')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                            <li><a href="{{ route('product') }}">{{__('Product')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BREADCRUMB END
    ==============================-->
<!--============================
        PRODUCT DETAILS START
    ==============================-->
    <section id="wsus__product_details">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-md-5 col-lg-5">
                    <div id="sticky_pro_zoom">
                        <div class="exzoom hidden" id="exzoom">
                            <div class="exzoom_img_box">
                                @if ($product->video_link)
                                    @php
                                        $video_id=explode("=",$product->video_link);
                                    @endphp
                                    <a class="venobox wsus__pro_det_video" data-autoplay="true" data-vbtype="video"
                                        href="https://youtu.be/{{ $video_id[1] }}">
                                        <i class="fas fa-play"></i>
                                    </a>
                                @endif
                                <ul class='exzoom_img_ul'>
                                    @foreach ($product->gallery as $image)
                                    <li><img class="zoom ing-fluid w-100" src="{{ asset($image->image) }}" alt="product"></li>
                                    @endforeach


                                </ul>
                            </div>
                            <div class="exzoom_nav"></div>
                            <p class="exzoom_btn">
                                <a href="javascript:void(0);" class="exzoom_prev_btn"> <i class="far fa-chevron-left"></i> </a>
                                <a href="javascript:void(0);" class="exzoom_next_btn"> <i class="far fa-chevron-right"></i> </a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-md-7 col-lg-7">
                    <div class="wsus__pro_details_text">
                        <a class="title" href="javascript:;">{{ $product->name }}</a>
                            <input type="hidden" id="stock_qty" value="{{ $product->qty }}">
                            @if ($product->qty == 0)
                            <p class="wsus__stock_area"><span class="in_stock">{{__('Out of Stock')}}</span></p>
                            @else
                                <p class="wsus__stock_area"><span class="in_stock">{{__('In stock')}}</span>
                                    @if ($setting->show_product_qty == 1)
                                    ({{ $product->qty }} {{__('item')}})
                                    @endif
                                </p>
                            @endif


                        @php
                            $reviewQty = $product->reviews->where('status',1)->count();
                            $totalReview = $product->reviews->where('status',1)->sum('rating');

                            if ($reviewQty > 0) {
                                $average = $totalReview / $reviewQty;

                                $intAverage = intval($average);

                                $nextValue = $intAverage + 1;
                                $reviewPoint = $intAverage;
                                $halfReview=false;
                                if($intAverage < $average && $average < $nextValue){
                                    $reviewPoint= $intAverage + 0.5;
                                    $halfReview=true;
                                }
                            }
                        @endphp

                        @php
                            $variantPrice = 0;
                            $variants = $product->variants->where('status', 1);
                            if($variants->count() != 0){
                                foreach ($variants as $variants_key => $variant) {
                                    if($variant->variantItems->where('status',1)->count() != 0){
                                        $item = $variant->variantItems->where('is_default',1)->first();
                                        if($item){
                                            $variantPrice += $item->price;
                                        }
                                    }
                                }
                            }
                            $isCampaign = false;
                            $today = date('Y-m-d H:i:s');
                            $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $product->id])->first();
                            if($campaign){
                                $campaign = $campaign->campaign;
                                if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                    $isCampaign = true;
                                }
                                $campaignOffer = $campaign->offer;
                                $productPrice = $product->price;
                                $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                $totalPrice = $product->price;
                                $campaignOfferPrice = $totalPrice - $campaignOfferPrice;
                            }

                            $totalPrice = $product->price;
                            if($product->offer_price != null){
                                $offerPrice = $product->offer_price;
                                $offer = $totalPrice - $offerPrice;
                                $percentage = ($offer * 100) / $totalPrice;
                                $percentage = round($percentage);
                            }


                        @endphp

                        @if ($isCampaign)
                            <h4>{{ $currencySetting->currency_icon }} <span id="mainProductPrice">{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }}</span>  <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></h4>
                        @else
                            @if ($product->offer_price == null)
                                <h4>{{ $currencySetting->currency_icon }} <span id="mainProductPrice">{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</span> </h4>
                            @else
                                <h4>{{ $currencySetting->currency_icon }} <span id="mainProductPrice">{{ sprintf("%.2f", $product->offer_price + $variantPrice) }}</span>  <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></h4>
                            @endif
                        @endif


                        @if ($reviewQty > 0)
                            <p class="review">
                                @for ($i = 1; $i <=5; $i++)
                                    @if ($i <= $reviewPoint)
                                        <i class="fas fa-star"></i>
                                    @elseif ($i> $reviewPoint )
                                        @if ($halfReview==true)
                                        <i class="fas fa-star-half-alt"></i>
                                            @php
                                                $halfReview=false
                                            @endphp
                                        @else
                                        <i class="fal fa-star"></i>
                                        @endif
                                    @endif
                                @endfor
                                <span>({{ $reviewQty }} {{__('review')}})</span>
                            </p>
                        @endif

                        @if ($reviewQty == 0)
                            <p class="review">
                                <i class="fal fa-star"></i>
                                <i class="fal fa-star"></i>
                                <i class="fal fa-star"></i>
                                <i class="fal fa-star"></i>
                                <i class="fal fa-star"></i>
                                <span>(0 {{__('review')}})</span>
                            </p>
                        @endif

                        <p class="description">{{ $product->short_description }}</p>

                        @if ($product->is_flash_deal == 1)
                            @php
                                $end_time = $product->flash_deal_date;
                            @endphp
                            <script>
                                var end_year = {{ date('Y', strtotime($end_time)) }};
                                var end_month = {{ date('m', strtotime($end_time)) }};
                                var end_date = {{ date('d', strtotime($end_time)) }};
                            </script>
                            <div class="wsus_pro_hot_deals">
                                <h5>{{__('offer ending time')}} : </h5>
                                <div class="simply-countdown product-details"></div>
                            </div>
                        @endif

                        @php
                            $productPrice = 0;
                            if($isCampaign){
                                $productPrice = $campaignOfferPrice + $variantPrice;
                            }else{
                                if ($product->offer_price == null) {
                                    $productPrice = $totalPrice + $variantPrice;
                                }else {
                                    $productPrice = $product->offer_price + $variantPrice;
                                }
                            }
                        @endphp

                        <form id="shoppingCartForm">
                        <div class="wsus__quentity">
                            <h5>{{__('Quantity')}} :</h5>
                            <div class="modal_btn">
                                <button type="button" class="btn btn-danger btn-sm decrementProduct">-</button>
                                <input class="form-control product_qty" name="quantity" readonly type="text" min="1" max="100" value="1" />
                                <button type="button" class="btn btn-success btn-sm incrementProduct">+</button>
                            </div>
                            <h3 class="d-none">{{ $currencySetting->currency_icon }}<span id="product_price">{{ sprintf("%.2f",$productPrice) }}</span></h3>
                        </div>

                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="image" value="{{ $product->thumb_image }}">
                        <input type="hidden" name="slug" value="{{ $product->slug }}">

                        @if ($productVariants->count() != 0)
                            <div class="wsus__selectbox">
                                <div class="row">
                                    @foreach ($productVariants as $productVariant)
                                        @php
                                            $items = App\Models\ProductVariantItem::orderBy('is_default','desc')->where(['product_variant_id' => $productVariant->id, 'product_id' => $product->id])->get();
                                        @endphp
                                        @if ($items->count() != 0)
                                            <div class="col-xl-6 col-sm-6 mb-3">
                                                <h5 class="mb-2">{{ $productVariant->name }}:</h5>

                                                <input type="hidden" name="variants[]" value="{{ $productVariant->id }}">
                                                <input type="hidden" name="variantNames[]" value="{{ $productVariant->name }}">

                                                <select class="select_2 productVariant" name="items[]">
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif




                        <ul class="wsus__button_area">
                            <li><button type="submit" class="add_cart">{{__('add to cart')}}</button></li>
                            <li><a class="buy_now" href="javascript:;" id="buyNowBtn">{{__('buy now')}}</a></li>
                            <li><a href="javascript:;" onclick="addToWishlist('{{ $product->id }}')"><i class="fal fa-heart"></i></a></li>
                            <li><a href="javascript:;" onclick="addToCompare('{{ $product->id }}')"><i class="far fa-random"></i></a></li>
                        </ul>

                    </form>
                        @if ($product->sku)
                        <p class="brand_model"><span>{{__('Model')}} :</span> {{ $product->sku }}</p>
                        @endif

                        <p class="brand_model"><span>{{__('Brand')}} :</span> <a href="{{ route('product',['brand' => $product->brand->slug]) }}">{{ $product->brand->name }}</a></p>
                        <p class="brand_model"><span>{{__('Category')}} :</span> <a href="{{ route('product',['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></p>
                        <div class="wsus__pro_det_share d-none">
                            <h5>{{__('share')}} :</h5>
                            <ul class="d-flex">
                                <li><a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ route('product-detail', $product->slug) }}&t={{ $product->name }}"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a class="twitter" href="https://twitter.com/share?text={{ $product->name }}&url={{ route('product-detail', $product->slug) }}"><i class="fab fa-twitter"></i></a></li>
                                <li><a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url={{ route('product-detail', $product->slug) }}&title={{ $product->name }}"><i class="fab fa-linkedin"></i></a></li>
                                <li><a class="pinterest" href="https://www.pinterest.com/pin/create/button/?description={{ $product->name }}&media=&url={{ route('product-detail', $product->slug) }}"><i class="fab fa-pinterest-p"></i></a></li>
                            </ul>
                        </div>
                        @auth
                            @php
                                $user = Auth::guard('web')->user();
                                $isExist = false;
                                $orders = App\Models\Order::where(['user_id' => $user->id])->get();
                                foreach ($orders as $key => $order) {
                                    foreach ($order->orderProducts as $key => $orderProduct) {
                                        if($orderProduct->product_id == $product->id){
                                            $isExist = true;
                                        }
                                    }
                                }
                            @endphp
                        @if ($isExist)
                            <a class="wsus__pro_report" href="#" data-bs-toggle="modal" data-bs-target="#productReportModal"><i
                            class="fal fa-comment-alt-smile"></i> {{__('Report incorrect product information')}}</a>
                        @endif

                        @endauth

                    </div>

                    <!--==========================
                    PRODUCT  REPORT MODAL VIEW
                    ===========================-->
                    @auth
                        @if ($isExist)
                            <section class="product_popup_modal report_modal">
                                <div class="modal fade" id="productReportModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{__('Report Product')}}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                                                        class="far fa-times"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <form id="reportModalForm">
                                                            @csrf
                                                            <div class="wsus__single_input">
                                                                <label>{{__('Subject')}}</label>
                                                                <input type="text" name="subject" placeholder="{{__('Type Subject')}}">
                                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                                <input type="hidden" name="seller_id" value="{{ $product->vendor_id }}">
                                                            </div>
                                                            <div class="wsus__single_input">
                                                                <label>{{__('Description')}}</label>
                                                                <textarea name="description" cols="3" rows="3"
                                                                    placeholder="{{__('Description')}}"></textarea>
                                                            </div>

                                                            <button type="submit" class="common_btn">{{__('submit')}}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endif
                    @endauth
                    <!--==========================
                    PRODUCT REPORT MODAL VIEW
                    ===========================-->
                </div>

                <div class="col-xl-3 col-md-12 mt-md-5 mt-lg-0">
                    <div class="wsus_pro_det_sidebar" id="sticky_sidebar">
                        <div class="lg_area">
                            <div class="wsus_pro_det_sidebar_single">
                                <i class="fal fa-truck"></i>
                                <div class="wsus_pro_det_sidebar_text">

                                    @if ($product->is_return == 1)
                                    <h5>{{__('Return Available')}}</h5>
                                    <p>{{ $product->returnPolicy->details }}</p>
                                    @else
                                        <h5>{{__('Return Not Available')}}</h5>
                                    @endif

                                </div>
                            </div>


                            <div class="wsus_pro_det_sidebar_single">
                                <i class="far fa-shield-check"></i>
                                <div class="wsus_pro_det_sidebar_text">
                                    <h5>{{__('Secure Payment')}}</h5>
                                    <p>{{__('We ensure secure payment')}}</p>
                                </div>
                            </div>
                            <div class="wsus_pro_det_sidebar_single">
                                <i class="fal fa-envelope-open-dollar"></i>
                                <div class="wsus_pro_det_sidebar_text">
                                    @if ($product->is_warranty == 1)
                                    <h5>{{__('Warranty Available')}}</h5>
                                    @else
                                    <h5>{{__('Warranty Not Available')}}</h5>
                                    @endif

                                </div>
                            </div>
                        </div>

                        @if ($banner->status == 1)
                            <div class="wsus__det_sidebar_banner">
                                <img src="{{ asset($banner->image) }}" alt="banner" class="img-fluid w-100">
                                    <div class="wsus__det_sidebar_banner_text_overlay">
                                    <div class="wsus__det_sidebar_banner_text">
                                        <p>{{ $banner->title }}</p>
                                        <h4>{{ $banner->description }}</h4>
                                        <a href="{{ $banner->link }}" class="common_btn">{{__('shop now')}}</a>
                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="wsus__pro_det_description">
                        <ul class="nav nav-pills mb-3" id="pills-tab3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-home-tab7" data-bs-toggle="pill"
                                    data-bs-target="#pills-home22" type="button" role="tab" aria-controls="pills-home"
                                    aria-selected="true">{{__('Description')}}</button>
                            </li>
                            @if ($product->is_specification == 1)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-profile-tab7" data-bs-toggle="pill"
                                    data-bs-target="#pills-profile22" type="button" role="tab"
                                    aria-controls="pills-profile" aria-selected="false">{{__('Specification')}}</button>
                            </li>
                            @endif

                            @if ($product->vendor_id != 0)
                            @if ($setting->enable_multivendor == 1)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-contact" type="button" role="tab"
                                    aria-controls="pills-contact" aria-selected="false">{{__('Seller Information')}}</button>
                            </li>
                            @endif
                            @endif

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-contact-tab2" data-bs-toggle="pill"
                                    data-bs-target="#pills-contact2" type="button" role="tab"
                                    aria-controls="pills-contact2" aria-selected="false">{{__('Reviews')}}</button>
                            </li>

                        </ul>
                        <div class="tab-content" id="pills-tabContent4">
                            <div class="tab-pane fade  show active " id="pills-home22" role="tabpanel"
                                aria-labelledby="pills-home-tab7">
                                <div class="row">
                                    <div class="col-12">
                                        {!! $product->long_description !!}
                                    </div>


                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-profile22" role="tabpanel"
                                aria-labelledby="pills-profile-tab7">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="wsus__pro_det_info">
                                            <h4>{{__('Additional Information')}}</h4>
                                            @foreach ($product->specifications as $specification)
                                            <p><span>{{ $specification->key->key }}</span> <span>{{ $specification->specification }}</span></p>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @if ($product->vendor_id != 0)
                            @php
                                $user = $product->seller;
                                $user = $user->user;
                            @endphp
                            <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                aria-labelledby="pills-contact-tab">
                                <div class="wsus__pro_det_vendor">
                                    <div class="row">
                                        <div class="col-xl-6 col-xxl-5 col-md-6">
                                            <div class="wsus__vebdor_img">
                                                @if ($user->image)
                                                <img src="{{ asset($user->image) }}" alt="vensor" class="img-fluid w-100">
                                                @else
                                                <img src="{{ asset($defaultProfile->image) }}" alt="vensor" class="img-fluid w-100">
                                                @endif

                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-xxl-7 col-md-6 mt-4 mt-md-0">
                                            <div class="wsus__pro_det_vendor_text">
                                                <h4>{{ $user->name }}</h4>
                                                @php
                                                    $reviewQty = App\Models\ProductReview::where('status',1)->where('product_vendor_id',$product->vendor_id)->count();
                                                    $totalReview = App\Models\ProductReview::where('status',1)->where('product_vendor_id',$product->vendor_id)->sum('rating');
                                                    if ($reviewQty > 0) {
                                                        $average = $totalReview / $reviewQty;
                                                        $intAverage = intval($average);
                                                        $nextValue = $intAverage + 1;
                                                        $reviewPoint = $intAverage;
                                                        $halfReview = false;
                                                        if($intAverage < $average && $average < $nextValue){
                                                            $reviewPoint= $intAverage + 0.5;
                                                            $halfReview=true;
                                                        }
                                                    }
                                                @endphp

                                                @if ($reviewQty > 0)
                                                <p class="rating">
                                                    @for ($i = 1; $i <=5; $i++)
                                                        @if ($i <= $reviewPoint)
                                                            <i class="fas fa-star"></i>
                                                        @elseif ($i> $reviewPoint )
                                                            @if ($halfReview==true)
                                                            <i class="fas fa-star-half-alt"></i>
                                                                @php
                                                                    $halfReview=false
                                                                @endphp
                                                            @else
                                                            <i class="fal fa-star"></i>
                                                            @endif
                                                        @endif
                                                    @endfor
                                                    <span>({{ $reviewQty }} {{ __('user.review') }})</span>
                                                </p>
                                                @endif

                                                @if ($reviewQty == 0)
                                                    <p class="rating">
                                                        <i class="fal fa-star"></i>
                                                        <i class="fal fa-star"></i>
                                                        <i class="fal fa-star"></i>
                                                        <i class="fal fa-star"></i>
                                                        <i class="fal fa-star"></i>
                                                        <span>(0 {{ __('user.review') }})</span>
                                                    </p>
                                                @endif

                                                <p><span>{{__('Store Name')}}:</span> {{ $user->seller->shop_name }}</p>
                                                <p><span>{{__('Address')}}:</span> {{ $user->address }} {{ $user->city ? ','.$user->city->name : '' }} {{ $user->city ? ','.$user->city->countryState->name : '' }} {{ $user->city ? ','.$user->city->countryState->country->name : '' }}</p>
                                                <p><span>{{__('Phone')}}:</span> {{ $user->phone }}</p>
                                                <p><span>{{__('mail')}}:</span> {{ $user->email }}</p>
                                                <a href="{{ route('seller-detail',['shop_name' => $user->seller->slug]) }}" class="see_btn">{{__('visit store')}}</a>
                                                <a href="{{ route('user.chat-with-seller', $user->seller->slug) }}" class="see_btn">{{__('Chat with Seller')}}</a>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="wsus__vendor_details">
                                                {!! clean($user->seller->description) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="tab-pane fade" id="pills-contact2" role="tabpanel"
                                aria-labelledby="pills-contact-tab2">
                                <div class="wsus__pro_det_review">
                                    <div class="wsus__pro_det_review_single">
                                        <div class="row">
                                            <div class="col-xl-8 col-lg-7">
                                                <div class="wsus__comment_area">
                                                    <h4>{{__('Reviews')}} <span>{{ $totalProductReviewQty }}</span></h4>
                                                    @foreach ($productReviews as $review)
                                                    <div class="wsus__main_comment">
                                                        <div class="wsus__comment_img">
                                                            <img src="{{ $review->user->image ? asset($review->user->image) : asset($defaultProfile->image) }}" alt="user"
                                                                class="img-fluid w-100">
                                                        </div>
                                                        <div class="wsus__comment_text replay">
                                                            <h6>{{ $review->user->name }} <span>{{ $review->rating }} <i
                                                                        class="fas fa-star"></i></span></h6>
                                                            <span>{{ $review->created_at->format('d M, Y') }}</span>
                                                            <p>
                                                                {{ $review->review }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    {{ $productReviews->links('custom_paginator') }}
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-5 mt-4 mt-lg-0">
                                                <div class="wsus__post_comment rev_mar" id="sticky_sidebar3">
                                                    <h4>{{__('write a Review')}}</h4>
                                                    <form id="reviewFormId">
                                                        @csrf
                                                        <p class="rating">
                                                            <span>{{__('select your rating')}} : </span>
                                                            <i class="fas fa-star product_rat" data-rating="1" onclick="productReview(1)"></i>
                                                            <i class="fas fa-star product_rat" data-rating="2" onclick="productReview(2)"></i>
                                                            <i class="fas fa-star product_rat" data-rating="3" onclick="productReview(3)"></i>
                                                            <i class="fas fa-star product_rat" data-rating="4" onclick="productReview(4)"></i>
                                                            <i class="fas fa-star product_rat" data-rating="5" onclick="productReview(5)"></i>
                                                        </p>
                                                        <div class="row">
                                                            <div class="col-xl-12">
                                                                <div class="col-xl-12">
                                                                    <div class="wsus__single_com">
                                                                        <textarea name="review" cols="3" rows="3"
                                                                            placeholder="{{__('Write your review')}}"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" id="product_id" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="rating" value="5" id="product_rating">
                                                        <input type="hidden" name="seller_id" value="{{ $product->vendor_id }}">

                                                        @if($recaptchaSetting->status==1)
                                                            <div class="col-xl-12">
                                                                <div class="wsus__single_com mb-3">
                                                                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSetting->site_key }}"></div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @auth
                                                        <button class="common_btn" type="submit">{{__('submit review')}}</button>
                                                        @else
                                                        <a class="login_link" href="{{ route('login') }}">{{__('Before submit review, please login first')}}</a>
                                                        @endauth

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        PRODUCT DETAILS END
    ==============================-->


        <!--============================
        RELATED PRODUCT START
    ==============================-->
    @if ($relatedProducts->count() > 0)
    <section id="wsus__flash_sell">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="wsus__section_header">
                        <h3>{{__('Related Products')}}</h3>
                        <a class="see_btn" href="{{ route('product',['category' => $product->category->slug]) }}">{{__('see more')}} <i class="fas fa-caret-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row flash_sell_slider ">
                @foreach ($relatedProducts as $relatedProduct)
                    @php
                        $reviewQty = $relatedProduct->reviews->where('status',1)->count();
                        $totalReview = $relatedProduct->reviews->where('status',1)->sum('rating');

                        if ($reviewQty > 0) {
                            $average = $totalReview / $reviewQty;

                            $intAverage = intval($average);

                            $nextValue = $intAverage + 1;
                            $reviewPoint = $intAverage;
                            $halfReview=false;
                            if($intAverage < $average && $average < $nextValue){
                                $reviewPoint= $intAverage + 0.5;
                                $halfReview=true;
                            }
                        }
                    @endphp

                    @php
                        $variantPrice = 0;
                        $variants = $relatedProduct->variants->where('status', 1);
                        if($variants->count() != 0){
                            foreach ($variants as $variants_key => $variant) {
                                if($variant->variantItems->where('status',1)->count() != 0){
                                    $item = $variant->variantItems->where('is_default',1)->first();
                                    if($item){
                                        $variantPrice += $item->price;
                                    }
                                }
                            }
                        }

                        $isCampaign = false;
                        $today = date('Y-m-d H:i:s');

                        $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $relatedProduct->id])->first();
                        if($campaign){

                            $campaign = $campaign->campaign;
                            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                $isCampaign = true;
                            }

                            $campaignOffer = $campaign->offer;
                            $productPrice = $relatedProduct->price;
                            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                            $totalPrice = $productPrice;
                            $campaignOfferPrice = $totalPrice - $campaignOfferPrice;
                        }

                        $totalPrice = $relatedProduct->price;
                        if($relatedProduct->offer_price != null){
                            $offerPrice = $relatedProduct->offer_price;
                            $offer = $totalPrice - $offerPrice;
                            $percentage = ($offer * 100) / $totalPrice;
                            $percentage = round($percentage);
                        }

                    @endphp
                    <div class="col-xl-3">
                        <div class="wsus__product_item wsus__after">
                            @if ($relatedProduct->new_product == 1)
                                <span class="wsus__new">{{__('New')}}</span>
                            @elseif ($relatedProduct->is_featured == 1)
                                <span class="wsus__new">{{__('Featured')}}</span>
                            @elseif ($relatedProduct->is_top == 1)
                                <span class="wsus__new">{{__('Top')}}</span>
                            @elseif ($relatedProduct->is_best == 1)
                                <span class="wsus__new">{{__('Best')}}</span>
                            @endif

                            @if ($isCampaign)
                                <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                            @else
                                @if ($relatedProduct->offer_price != null)
                                    <span class="wsus__minus">-{{ $percentage }}%</span>
                                @endif
                            @endif
                            <a class="wsus__pro_link" href="{{ route('product-detail', $relatedProduct->slug) }}">
                                <img src="{{ asset($relatedProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                <img src="{{ asset($relatedProduct->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                            </a>

                            <ul class="wsus__single_pro_icon">
                                <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $relatedProduct->id }}"><i class="fal fa-eye"></i></a></li>
                                <li><a href="javascript:;" onclick="addToWishlist('{{ $relatedProduct->id }}')"><i class="far fa-heart"></i></a></li>
                                <li><a href="javascript:;" onclick="addToCompare('{{ $relatedProduct->id }}')"><i class="far fa-random"></i></a>
                                </li>
                            </ul>
                            <div class="wsus__product_details">
                                <a class="wsus__category" href="{{ route('product',['category' => $relatedProduct->category->slug]) }}">{{ $relatedProduct->category->name }} </a>

                                @if ($reviewQty > 0)
                                    <p class="wsus__pro_rating">
                                        @for ($i = 1; $i <=5; $i++)
                                            @if ($i <= $reviewPoint)
                                                <i class="fas fa-star"></i>
                                            @elseif ($i> $reviewPoint )
                                                @if ($halfReview==true)
                                                <i class="fas fa-star-half-alt"></i>
                                                    @php
                                                        $halfReview=false
                                                    @endphp
                                                @else
                                                <i class="fal fa-star"></i>
                                                @endif
                                            @endif
                                        @endfor
                                        <span>({{ $reviewQty }} {{__('review')}})</span>
                                    </p>
                                @endif

                                @if ($reviewQty == 0)
                                    <p class="wsus__pro_rating">
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <i class="fal fa-star"></i>
                                        <span>(0 {{__('review')}})</span>
                                    </p>
                                @endif

                                <a class="wsus__pro_name" href="{{ route('product-detail', $relatedProduct->slug) }}">{{ $relatedProduct->short_name }}</a>
                                @if ($isCampaign)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f",$totalPrice) }}</del></p>
                                @else
                                    @if ($relatedProduct->offer_price == null)
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                    @else
                                    <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $relatedProduct->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                    @endif
                                @endif
                                <a class="add_cart" onclick="addToCartMainProduct('{{ $relatedProduct->id }}')" href="javascript:;">{{__('add to cart')}}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @foreach ($relatedProducts as $relatedProduct)
                <section class="product_popup_modal">
                    <div class="modal fade" id="productModalView-{{ $relatedProduct->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                                            class="far fa-times"></i></button>
                                    <div class="row">
                                        <div class="col-xl-6 col-12 col-sm-10 col-md-8 col-lg-6 m-auto display">
                                            <div class="wsus__quick_view_img">
                                                @if ($relatedProduct->video_link)
                                                    @php
                                                        $video_id=explode("=",$relatedProduct->video_link);
                                                    @endphp
                                                    <a class="venobox wsus__pro_det_video" data-autoplay="true" data-vbtype="video"
                                                    href="https://youtu.be/{{ $video_id[1] }}">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                                @endif

                                                <div class="row modal_slider">
                                                    @foreach ($relatedProduct->gallery as $image)
                                                    <div class="col-xl-12">
                                                        <div class="modal_slider_img">
                                                            <img src="{{ asset($image->image) }}" alt="product" class="img-fluid w-100">
                                                        </div>
                                                    </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-12 col-sm-12 col-md-12 col-lg-6">
                                            <div class="wsus__pro_details_text">
                                                <a class="title" href="{{ route('product-detail', $relatedProduct->slug) }}">{{ $relatedProduct->name }}</a>

                                                    @if ($relatedProduct->qty == 0)
                                                    <p class="wsus__stock_area"><span class="in_stock">{{__('Out of Stock')}}</span></p>
                                                    @else
                                                        <p class="wsus__stock_area"><span class="in_stock">{{__('In stock')}}
                                                            @if ($setting->show_product_qty == 1)
                                                                </span> ({{ $relatedProduct->qty }} {{__('item')}})
                                                            @endif
                                                        </p>
                                                    @endif


                                                @php
                                                    $reviewQty = $relatedProduct->reviews->where('status',1)->count();
                                                    $totalReview = $relatedProduct->reviews->where('status',1)->sum('rating');

                                                    if ($reviewQty > 0) {
                                                        $average = $totalReview / $reviewQty;

                                                        $intAverage = intval($average);

                                                        $nextValue = $intAverage + 1;
                                                        $reviewPoint = $intAverage;
                                                        $halfReview=false;
                                                        if($intAverage < $average && $average < $nextValue){
                                                            $reviewPoint= $intAverage + 0.5;
                                                            $halfReview=true;
                                                        }
                                                    }
                                                @endphp

                                                @php
                                                    $variantPrice = 0;
                                                    $variants = $relatedProduct->variants->where('status', 1);
                                                    if($variants->count() != 0){
                                                        foreach ($variants as $variants_key => $variant) {
                                                            if($variant->variantItems->where('status',1)->count() != 0){
                                                                $item = $variant->variantItems->where('is_default',1)->first();
                                                                if($item){
                                                                    $variantPrice += $item->price;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $isCampaign = false;
                                                    $today = date('Y-m-d H:i:s');
                                                    $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $relatedProduct->id])->first();
                                                    if($campaign){
                                                        $campaign = $campaign->campaign;
                                                        if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                            $isCampaign = true;
                                                        }
                                                        $campaignOffer = $campaign->offer;
                                                        $productPrice = $relatedProduct->price;
                                                        $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                        $totalPrice = $relatedProduct->price;
                                                        $campaignOfferPrice = $totalPrice - $campaignOfferPrice;
                                                    }

                                                    $totalPrice = $relatedProduct->price;
                                                    if($relatedProduct->offer_price != null){
                                                        $offerPrice = $relatedProduct->offer_price;
                                                        $offer = $totalPrice - $offerPrice;
                                                        $percentage = ($offer * 100) / $totalPrice;
                                                        $percentage = round($percentage);
                                                    }


                                                @endphp

                                                @if ($isCampaign)
                                                    <h4>{{ $currencySetting->currency_icon }} <span id="mainProductModalPrice-{{ $relatedProduct->id }}">{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }}</span>  <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></h4>
                                                @else
                                                    @if ($relatedProduct->offer_price == null)
                                                        <h4>{{ $currencySetting->currency_icon }}<span id="mainProductModalPrice-{{ $relatedProduct->id }}">{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</span></h4>
                                                    @else
                                                        <h4>{{ $currencySetting->currency_icon }}<span id="mainProductModalPrice-{{ $relatedProduct->id }}">{{ sprintf("%.2f", $relatedProduct->offer_price + $variantPrice) }}</span> <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></h4>
                                                    @endif
                                                @endif

                                                @if ($reviewQty > 0)
                                                    <p class="review">
                                                        @for ($i = 1; $i <=5; $i++)
                                                            @if ($i <= $reviewPoint)
                                                                <i class="fas fa-star"></i>
                                                            @elseif ($i> $reviewPoint )
                                                                @if ($halfReview==true)
                                                                <i class="fas fa-star-half-alt"></i>
                                                                    @php
                                                                        $halfReview=false
                                                                    @endphp
                                                                @else
                                                                <i class="fal fa-star"></i>
                                                                @endif
                                                            @endif
                                                        @endfor
                                                        <span>({{ $reviewQty }} {{__('review')}})</span>
                                                    </p>
                                                @endif

                                                @if ($reviewQty == 0)
                                                    <p class="review">
                                                        <i class="fal fa-star"></i>
                                                        <i class="fal fa-star"></i>
                                                        <i class="fal fa-star"></i>
                                                        <i class="fal fa-star"></i>
                                                        <i class="fal fa-star"></i>
                                                        <span>(0 {{__('review')}})</span>
                                                    </p>
                                                @endif

                                                @php
                                                    $productPrice = 0;
                                                    if($isCampaign){
                                                        $productPrice = $campaignOfferPrice + $variantPrice;
                                                    }else{
                                                        if ($relatedProduct->offer_price == null) {
                                                            $productPrice = $totalPrice + $variantPrice;
                                                        }else {
                                                            $productPrice = $relatedProduct->offer_price + $variantPrice;
                                                        }
                                                    }
                                                @endphp
                                                <form id="productModalFormId-{{ $relatedProduct->id }}">
                                                <div class="wsus__quentity">
                                                    <h5>{{__('quantity') }} :</h5>
                                                    <div class="modal_btn">
                                                        <button onclick="productModalDecrement('{{ $relatedProduct->id }}')" type="button" class="btn btn-danger btn-sm">-</button>
                                                        <input id="productModalQty-{{ $relatedProduct->id }}" name="quantity"  readonly class="form-control" type="text" min="1" max="100" value="1" />
                                                        <button onclick="productModalIncrement('{{ $relatedProduct->id }}')" type="button" class="btn btn-success btn-sm">+</button>
                                                    </div>
                                                    <h3 class="d-none">{{ $currencySetting->currency_icon }}<span id="productModalPrice-{{ $relatedProduct->id }}">{{ sprintf("%.2f",$productPrice) }}</span></h3>

                                                    <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                                                    <input type="hidden" name="image" value="{{ $relatedProduct->thumb_image }}">
                                                    <input type="hidden" name="slug" value="{{ $relatedProduct->slug }}">

                                                </div>
                                                @php
                                                    $productVariants = App\Models\ProductVariant::where(['status' => 1, 'product_id'=> $relatedProduct->id])->get();
                                                @endphp
                                                @if ($productVariants->count() != 0)
                                                    <div class="wsus__selectbox">
                                                        <div class="row">
                                                            @foreach ($productVariants as $productVariant)
                                                                @php
                                                                    $items = App\Models\ProductVariantItem::orderBy('is_default','desc')->where(['product_variant_id' => $productVariant->id, 'product_id' => $relatedProduct->id])->get();
                                                                @endphp
                                                                @if ($items->count() != 0)
                                                                    <div class="col-xl-6 col-sm-6 mb-3">
                                                                        <h5 class="mb-2">{{ $productVariant->name }}:</h5>

                                                                        <input type="hidden" name="variants[]" value="{{ $productVariant->id }}">
                                                                        <input type="hidden" name="variantNames[]" value="{{ $productVariant->name }}">

                                                                        <select class="select_2 productModalVariant" name="items[]" data-product="{{ $relatedProduct->id }}">
                                                                            @foreach ($items as $item)
                                                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                                            @endforeach
                                                                        </select>

                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                <ul class="wsus__button_area">
                                                    <li><button type="button" onclick="addToCartInProductModal('{{ $relatedProduct->id }}')" class="add_cart">{{__('add to cart')}}</button></li>
                                                    <li><a class="buy_now" href="javascript:;" onclick="addToBuyNow('{{ $relatedProduct->id }}')">{{__('buy now')}}</a></li>
                                                    <li><a href="javascript:;" onclick="addToWishlist('{{ $relatedProduct->id }}')"><i class="fal fa-heart"></i></a></li>
                                                    <li><a href="javascript:;" onclick="addToCompare('{{ $relatedProduct->id }}')"><i class="far fa-random"></i></a></li>
                                                </ul>
                                            </form>
                                            @if ($relatedProduct->sku)
                                            <p class="brand_model"><span>{{__('Model')}} :</span> {{ $relatedProduct->sku }}</p>
                                            @endif

                                            <p class="brand_model"><span>{{__('Brand')}} :</span> <a href="{{ route('product',['brand' => $relatedProduct->brand->slug]) }}">{{ $relatedProduct->brand->name }}</a></p>
                                            <p class="brand_model"><span>{{__('Category')}} :</span> <a href="{{ route('product',['category' => $relatedProduct->category->slug]) }}">{{ $relatedProduct->category->name }}</a></p>
                                            <div class="wsus__pro_det_share d-none">
                                                <h5>{{__('share')}} :</h5>
                                                <ul class="d-flex">
                                                    <li><a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ route('product-detail', $relatedProduct->slug) }}&t={{ $relatedProduct->name }}"><i class="fab fa-facebook-f"></i></a></li>
                                                    <li><a class="twitter" href="https://twitter.com/share?text={{ $relatedProduct->name }}&url={{ route('product-detail', $relatedProduct->slug) }}"><i class="fab fa-twitter"></i></a></li>
                                                    <li><a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url={{ route('product-detail', $relatedProduct->slug) }}&title={{ $relatedProduct->name }}"><i class="fab fa-linkedin"></i></a></li>
                                                    <li><a class="pinterest" href="https://www.pinterest.com/pin/create/button/?description={{ $relatedProduct->name }}&media=&url={{ route('product-detail', $relatedProduct->slug) }}"><i class="fab fa-pinterest-p"></i></a></li>
                                                </ul>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    </section>
    @endif
    <!--============================
        RELATED PRODUCT END
    ==============================-->


<script>
    (function($) {
        "use strict";
        $(document).ready(function () {
            $(".productVariant").on("change",function(){
                calculateProductPrice();
            })

            $(".decrementProduct").on("click", function(){
                let qty = $(".product_qty").val();
                if(qty > 1){
                    qty = qty - 1;
                    $(".product_qty").val(qty);
                    calculateProductPrice();
                }
            })

            $(".incrementProduct").on("click", function(){
                let qty = $(".product_qty").val();
                qty = qty*1 + 1*1;
                $(".product_qty").val(qty);
                calculateProductPrice();
            })

            $("#reportModalForm").on('submit', function(e){
                e.preventDefault();
                var isDemo = "{{ env('APP_VERSION') }}"
                if(isDemo == 0){
                    toastr.error('This Is Demo Version. You Can Not Change Anything');
                    return;
                }
                $.ajax({
                    type: 'post',
                    data: $('#reportModalForm').serialize(),
                    url: "{{ route('user.product-report') }}",
                    success: function (response) {
                        if(response.status == 1){
                            toastr.success(response.message)
                            $("#productReportModal").trigger("reset");
                            $("#productReportModal").modal('hide')
                        }
                        if(response.status == 0){
                            toastr.error(response.message)
                        }
                    },
                    error: function(err) {
                        alert('error')
                    }
                });
            })

            //start insert new cart item
            $("#shoppingCartForm").on("submit", function(e){
                e.preventDefault();
                $.ajax({
                    type: 'get',
                    data: $('#shoppingCartForm').serialize(),
                    url: "{{ route('add-to-cart') }}",
                    success: function (response) {
                        if(response.status == 0){
                            toastr.error(response.message)
                        }
                        if(response.status == 1){
                            toastr.success(response.message)
                            $.ajax({
                                type: 'get',
                                url: "{{ route('load-sidebar-cart') }}",
                                success: function (response) {
                                   $("#load_sidebar_cart").html(response)
                                   $.ajax({
                                        type: 'get',
                                        url: "{{ route('get-cart-qty') }}",
                                        success: function (response) {
                                            $("#cartQty").text(response.qty);
                                        },
                                    });
                                },
                            });
                        }
                    },
                    error: function(response) {

                    }
                });
            })
            //start insert new cart item

            // buy now item
            $("#buyNowBtn").on("click", function(){
                $.ajax({
                    type: 'get',
                    data: $('#shoppingCartForm').serialize(),
                    url: "{{ route('add-to-cart') }}",
                    success: function (response) {
                        if(response.status == 0){
                            toastr.error(response.message)
                        }
                        if(response.status == 1){
                            window.location.href = "{{ route('cart') }}";
                            toastr.success(response.message)
                            $.ajax({
                                type: 'get',
                                url: "{{ route('load-sidebar-cart') }}",
                                success: function (response) {
                                   $("#load_sidebar_cart").html(response)
                                   $.ajax({
                                        type: 'get',
                                        url: "{{ route('get-cart-qty') }}",
                                        success: function (response) {
                                            $("#cartQty").text(response.qty);
                                        },
                                    });
                                },
                            });
                        }
                    },
                    error: function(response) {

                    }
                });
            })

            $("#reviewFormId").on('submit', function(e){
                e.preventDefault();

                var isDemo = "{{ env('APP_VERSION') }}"
                if(isDemo == 0){
                    toastr.error('This Is Demo Version. You Can Not Change Anything');
                    return;
                }
                $.ajax({
                    type: 'post',
                    data: $('#reviewFormId').serialize(),
                    url: "{{ route('user.store-product-review') }}",
                    success: function (response) {
                        if(response.status == 1){
                            toastr.success(response.message)
                            $("#reviewFormId").trigger("reset");
                        }
                        if(response.status == 0){
                            toastr.error(response.message)
                            $("#reviewFormId").trigger("reset");
                        }
                    },
                    error: function(response) {
                        if(response.responseJSON.errors.rating)toastr.error(response.responseJSON.errors.rating[0])
                        if(response.responseJSON.errors.review)toastr.error(response.responseJSON.errors.review[0])
                        if(!response.responseJSON.errors.rating || !response.responseJSON.errors.review){
                            toastr.error("{{__('Please complete the recaptcha to submit the form')}}")
                        }
                    }
                });
            })

        });
    })(jQuery);

    function calculateProductPrice(){
        $.ajax({
            type: 'get',
            data: $('#shoppingCartForm').serialize(),
            url: "{{ route('calculate-product-price') }}",
            success: function (response) {
                let qty = $(".product_qty").val();
                let price = response.productPrice * qty;
                price = price.toFixed(2);
                $("#product_price").text(price);
                $("#mainProductPrice").text(price);
            },
            error: function(err) {
                alert('error')
            }
        });
    }

    function productReview(rating){
        $(".product_rat").each(function(){
            var product_rat = $(this).data('rating')
            if(product_rat > rating){
                $(this).removeClass('fas fa-star').addClass('fal fa-star');
            }else{
                $(this).removeClass('fal fa-star').addClass('fas fa-star');
            }
        })
        $("#product_rating").val(rating);
    }

</script>
@endsection
