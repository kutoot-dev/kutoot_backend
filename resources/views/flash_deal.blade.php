@extends('layout')
@section('title')
    <title>{{ $seoSetting->seo_title }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $seoSetting->seo_description }}">
@endsection

@section('public-content')


    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{  asset($banner->image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('Flash Deal')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                            <li><a href="{{ route('flash-deal') }}">{{__('Flash Deal')}}</a></li>
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
        DAILY DEALS DETAILS START
    ==============================-->
    <section id="wsus__daily_deals">
        <div class="container">
            <div class="wsus__offer_details_area">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="wsus__section_header rounded-0">
                            <h3>{{__('Flash Deal')}}</h3>
                        </div>
                    </div>
                </div>

                @php
                    $productIds = [];
                    $productYears = [];
                    $productMonths = [];
                    $productDays = [];

                    if(env('APP_VERSION') == 0){
                        $demo_end = Carbon\Carbon::now()->addDays(3);
                        foreach ($products as $key => $product) {
                            $productIds[] = $product->id;
                            $productYears[] = $demo_end->format('Y');
                            $productMonths[] = $demo_end->format('m');;
                            $productDays[] = $demo_end->format('d');
                        }
                    }else {
                        foreach ($products as $key => $product) {
                            $productIds[] = $product->id;
                            $productYears[] = date('Y', strtotime($product->flash_deal_date));
                            $productMonths[] = date('m', strtotime($product->flash_deal_date));
                            $productDays[] = date('d', strtotime($product->flash_deal_date));
                        }
                    }
                @endphp
                <script>
                    var productIds = <?= json_encode($productIds)?>;
                    var productYears = <?= json_encode($productYears)?>;
                    var productMonths = <?= json_encode($productMonths)?>;
                    var productDays = <?= json_encode($productDays)?>;
                </script>


                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-xl-4 col-sm-6 col-lg-4">
                            <div class="wsus__offer_det_single">
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
                                <div class="wsus__product_item">
                                    @if ($isCampaign)
                                        <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                                    @else
                                        @if ($product->offer_price != null)
                                            <span class="wsus__minus">-{{ $percentage }}%</span>
                                        @endif
                                    @endif


                                    <a class="wsus__pro_link" href="{{ route('product-detail', $product->slug) }}">
                                        <img src="{{ asset($product->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                        <img src="{{ asset($product->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                                    </a>
                                    <ul class="wsus__single_pro_icon">
                                        <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $product->id }}"><i class="fal fa-eye"></i></a></li>
                                        <li><a href="javascript:;" onclick="addToWishlist('{{ $product->id }}')"><i class="far fa-heart"></i></a></li>
                                        <li><a href="javascript:;" onclick="addToCompare('{{ $product->id }}')"><i class="far fa-random"></i></a></li>
                                    </ul>

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
                                    <div class="wsus__product_details">
                                        <a class="wsus__category" href="{{ route('product',['category' => $product->category->slug]) }}">{{ $product->category->name }} </a>

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


                                        <a class="wsus__pro_name" href="{{ route('product-detail', $product->slug) }}">{{ $product->short_name }}</a>

                                        @if ($isCampaign)
                                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @else
                                            @if ($product->offer_price == null)
                                                <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                                <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $product->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif
                                        <a class="add_cart" onclick="addToCartMainProduct('{{ $product->id }}')" href="javascript:;">{{__('Add to Cart')}}</a>
                                    </div>
                                </div>
                                <div class="wsus__offer_time">
                                    <div class="simply-countdown flash-deal-product-{{ $product->id }}"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-xl-12">
                        {{ $products->links('custom_paginator') }}
                    </div>

                </div>

                @foreach ($products as $product)
                    <section class="product_popup_modal">
                        <div class="modal fade" id="productModalView-{{ $product->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                                                class="far fa-times"></i></button>
                                        <div class="row">
                                            <div class="col-xl-6 col-12 col-sm-10 col-md-8 col-lg-6 m-auto display">
                                                <div class="wsus__quick_view_img">
                                                    @if ($product->video_link)
                                                        @php
                                                            $video_id=explode("=",$product->video_link);
                                                        @endphp
                                                        <a class="venobox wsus__pro_det_video" data-autoplay="true" data-vbtype="video"
                                                        href="https://youtu.be/{{ $video_id[1] }}">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                    @endif

                                                    <div class="row modal_slider">
                                                        @foreach ($product->gallery as $image)
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
                                                    <a class="title" href="{{ route('product-detail', $product->slug) }}">{{ $product->name }}</a>

                                                        @if ($product->qty == 0)
                                                        <p class="wsus__stock_area"><span class="in_stock">{{__('Out of Stock')}}</span></p>
                                                        @else
                                                            <p class="wsus__stock_area"><span class="in_stock">{{__('In stock')}}
                                                                @if ($setting->show_product_qty == 1)
                                                                    </span> ({{ $product->qty }} {{__('item')}})
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
                                                        <h4>{{ $currencySetting->currency_icon }} <span id="mainProductModalPrice-{{ $product->id }}">{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }}</span>  <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></h4>
                                                    @else
                                                        @if ($product->offer_price == null)
                                                            <h4>{{ $currencySetting->currency_icon }}<span id="mainProductModalPrice-{{ $product->id }}">{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</span></h4>
                                                        @else
                                                            <h4>{{ $currencySetting->currency_icon }}<span id="mainProductModalPrice-{{ $product->id }}">{{ sprintf("%.2f", $product->offer_price + $variantPrice) }}</span> <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></h4>
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
                                                            if ($product->offer_price == null) {
                                                                $productPrice = $totalPrice + $variantPrice;
                                                            }else {
                                                                $productPrice = $product->offer_price + $variantPrice;
                                                            }
                                                        }
                                                    @endphp
                                                    <form id="productModalFormId-{{ $product->id }}">
                                                    <div class="wsus__quentity">
                                                        <h5>{{__('quantity') }} :</h5>
                                                        <div class="modal_btn">
                                                            <button onclick="productModalDecrement('{{ $product->id }}')" type="button" class="btn btn-danger btn-sm">-</button>
                                                            <input id="productModalQty-{{ $product->id }}" name="quantity"  readonly class="form-control" type="text" min="1" max="100" value="1" />
                                                            <button onclick="productModalIncrement('{{ $product->id }}')" type="button" class="btn btn-success btn-sm">+</button>
                                                        </div>
                                                        <h3 class="d-none">{{ $currencySetting->currency_icon }}<span id="productModalPrice-{{ $product->id }}">{{ sprintf("%.2f",$productPrice) }}</span></h3>

                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="image" value="{{ $product->thumb_image }}">
                                                        <input type="hidden" name="slug" value="{{ $product->slug }}">

                                                    </div>
                                                    @php
                                                        $productVariants = App\Models\ProductVariant::where(['status' => 1, 'product_id'=> $product->id])->get();
                                                    @endphp
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

                                                                            <select class="select_2 productModalVariant" name="items[]" data-product="{{ $product->id }}">
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
                                                        <li><button type="button" onclick="addToCartInProductModal('{{ $product->id }}')" class="add_cart">{{__('add to cart')}}</button></li>
                                                        <li><a class="buy_now" href="javascript:;" onclick="addToBuyNow('{{ $product->id }}')">{{__('buy now')}}</a></li>
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
        </div>
    </section>
    <!--============================
        DAILY DEALS DETAILS END
    ==============================-->

@endsection
