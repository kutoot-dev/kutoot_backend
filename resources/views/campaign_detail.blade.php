@extends('layout')
@section('title')
    <title>{{ $campaign->name }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $campaign->name }}">
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
                        <h4>{{__('Campaign')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('campaign') }}">{{__('Campaign')}}</a></li>
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

                @if ($bannerOne->status == 1)
                <div class="row">
                    <div class="col-xl-6 col-md-6">
                        <div class="wsus__offer_details_banner">
                            <img src="{{ asset($bannerOne->image) }}" alt="offrt img" class="img-fluid w-100">
                            <div class="wsus__offer_details_banner_text">
                                <span>{{ $bannerOne->title }}</span>
                                <p><b>{{ $bannerOne->description }}</b></p>
                                <a href="{{ $bannerOne->link }}" class="shop_btn">{{__('shop now')}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="wsus__offer_details_banner">
                            <img src="{{ asset($bannerTwo->image) }}" alt="offrt img" class="img-fluid w-100">
                            <div class="wsus__offer_details_banner_text">
                                <span>{{ $bannerTwo->title }}</span>
                                <p><b>{{ $bannerTwo->description }}</b></p>
                                <a href="{{ $bannerTwo->link }}" class="shop_btn">{{__('shop now')}}</a>
                            </div>
                        </div>
                    </div>
                </div>

                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="wsus__section_header rounded-0">
                            <h3>{{ $campaign->name }}</h3>
                            <div class="wsus__offer_countdown">
                                <span class="end_text">{{__('ends time')}} :</span>
                                <div class="simply-countdown campaign-details"></div>
                                @if (env('APP_VERSION') == 0)
                                    @php
                                        $demo_end = Carbon\Carbon::now()->addDays(3);
                                    @endphp
                                    <script>
                                        var campaign_end_year = {{ $demo_end->format('Y') }}
                                        var campaign_end_month = {{ $demo_end->format('m') }}
                                        var campaign_end_date = {{ $demo_end->format('d') }}
                                        var campaign_hour = {{ $demo_end->format('H') }}
                                        var campaign_min = {{ $demo_end->format('i') }}
                                        var campaign_sec = {{ $demo_end->format('s') }}
                                    </script>
                                @else
                                    <script>
                                        var campaign_end_year = {{ date('Y', strtotime($campaign->end_date)) }}
                                        var campaign_end_month = {{ date('m', strtotime($campaign->end_date)) }}
                                        var campaign_end_date = {{ date('d', strtotime($campaign->end_date)) }}
                                        var campaign_hour = {{ date('H', strtotime($campaign->end_date)) }}
                                        var campaign_min = {{ date('i', strtotime($campaign->end_date)) }}
                                        var campaign_sec = {{ date('s', strtotime($campaign->end_date)) }}
                                    </script>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    @foreach ($campaignProducts as $campaignProduct)
                        <div class="col-xl-3 col-sm-6 col-lg-3">
                            <div class="wsus__offer_det_single">
                                <div class="wsus__product_item wsus__before">
                                    @if ($campaignProduct->product->new_product == 1)
                                        <span class="wsus__new">{{__('New')}}</span>
                                    @elseif ($campaignProduct->product->is_featured == 1)
                                        <span class="wsus__new">{{__('Featured')}}</span>
                                    @elseif ($campaignProduct->product->is_top == 1)
                                        <span class="wsus__new">{{__('Top')}}</span>
                                    @elseif ($campaignProduct->product->is_best == 1)
                                        <span class="wsus__new">{{__('Best')}}</span>
                                    @endif

                                    @php
                                        $variantPrice = 0;
                                        $variants = $campaignProduct->product->variants->where('status', 1);
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
                                        $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $campaignProduct->product->id])->first();
                                        if($campaign){
                                            $campaign = $campaign->campaign;
                                            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                $isCampaign = true;
                                            }
                                            $campaignOffer = $campaign->offer;
                                            $productPrice = $campaignProduct->product->price;
                                            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                            $totalPrice = $campaignProduct->product->price;
                                            $campaignOfferPrice = $totalPrice - $campaignOfferPrice;
                                        }

                                        $totalPrice = $campaignProduct->product->price;
                                        if($campaignProduct->product->offer_price != null){
                                            $offerPrice = $campaignProduct->product->offer_price;
                                            $offer = $totalPrice - $offerPrice;
                                            $percentage = ($offer * 100) / $totalPrice;
                                            $percentage = round($percentage);
                                        }
                                    @endphp
                                    @if ($isCampaign)
                                        <span class="wsus__minus">-{{ $campaignOffer }}%</span>
                                    @else
                                        @if ($campaignProduct->product->offer_price != null)
                                            <span class="wsus__minus">-{{ $percentage }}%</span>
                                        @endif
                                    @endif
                                    <a class="wsus__pro_link" href="{{ route('product-detail', $campaignProduct->product->slug) }}">
                                        <img src="{{ asset($campaignProduct->product->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                                        <img src="{{ asset($campaignProduct->product->thumb_image) }}" alt="product" class="img-fluid w-100 img_2" />
                                    </a>
                                    <ul class="wsus__single_pro_icon">
                                        <li><a data-bs-toggle="modal" data-bs-target="#productModalView-{{ $campaignProduct->product->id }}"><i class="fal fa-eye"></i></a></li>
                                        <li><a href="javascript:;" onclick="addToWishlist('{{ $campaignProduct->product->id }}')"><i class="far fa-heart"></i></a></li>
                                        <li><a href="javascript:;" onclick="addToCompare('{{ $campaignProduct->product->id }}')"><i class="far fa-random"></i></a></li>
                                    </ul>
                                    <div class="wsus__product_details">
                                        <a class="wsus__category" href="{{ route('product',['category' =>  $campaignProduct->product->category->slug]) }}">{{ $campaignProduct->product->category->name }} </a>

                                        @php
                                            $reviewQty = $campaignProduct->product->reviews->where('status',1)->count();
                                            $totalReview = $campaignProduct->product->reviews->where('status',1)->sum('rating');
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
                                        <a class="wsus__pro_name" href="{{ route('product-detail', $campaignProduct->product->slug) }}">{{ $campaignProduct->product->short_name }}</a>

                                        @if ($isCampaign)
                                            <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                        @else
                                            @if ($campaignProduct->product->offer_price == null)
                                                <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</p>
                                            @else
                                                <p class="wsus__price">{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $campaignProduct->product->offer_price + $variantPrice) }} <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></p>
                                            @endif
                                        @endif
                                        <a class="add_cart" onclick="addToCartMainProduct('{{ $campaignProduct->product->id }}')" href="javascript:;">{{__('add to cart')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


                @foreach ($campaignProducts as $campaignProduct)
                    <section class="product_popup_modal">
                        <div class="modal fade" id="productModalView-{{ $campaignProduct->product->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                                                class="far fa-times"></i></button>
                                        <div class="row">
                                            <div class="col-xl-6 col-12 col-sm-10 col-md-8 col-lg-6 m-auto display">
                                                <div class="wsus__quick_view_img">
                                                    @if ($campaignProduct->product->video_link)
                                                        @php
                                                            $video_id=explode("=",$campaignProduct->product->video_link);
                                                        @endphp
                                                        <a class="venobox wsus__pro_det_video" data-autoplay="true" data-vbtype="video"
                                                        href="https://youtu.be/{{ $video_id[1] }}">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                    @endif

                                                    <div class="row modal_slider">
                                                        @foreach ($campaignProduct->product->gallery as $image)
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
                                                    <a class="title" href="{{ route('product-detail', $campaignProduct->product->slug) }}">{{ $campaignProduct->product->name }}</a>

                                                        @if ($campaignProduct->product->qty == 0)
                                                        <p class="wsus__stock_area"><span class="in_stock">{{__('Out of Stock')}}</span></p>
                                                        @else
                                                            <p class="wsus__stock_area"><span class="in_stock">{{__('In stock')}}
                                                                @if ($setting->show_product_qty == 1)
                                                                    </span> ({{ $campaignProduct->product->qty }} {{__('item')}})
                                                                @endif
                                                            </p>
                                                        @endif


                                                    @php
                                                        $reviewQty = $campaignProduct->product->reviews->where('status',1)->count();
                                                        $totalReview = $campaignProduct->product->reviews->where('status',1)->sum('rating');

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
                                                        $variants = $campaignProduct->product->variants->where('status', 1);
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
                                                        $campaign = App\Models\CampaignProduct::where(['status' => 1, 'product_id' => $campaignProduct->product->id])->first();
                                                        if($campaign){
                                                            $campaign = $campaign->campaign;
                                                            if($campaign->start_date <= $today &&  $today <= $campaign->end_date){
                                                                $isCampaign = true;
                                                            }
                                                            $campaignOffer = $campaign->offer;
                                                            $productPrice = $campaignProduct->product->price;
                                                            $campaignOfferPrice = ($campaignOffer / 100) * $productPrice;
                                                            $totalPrice = $campaignProduct->product->price;
                                                            $campaignOfferPrice = $totalPrice - $campaignOfferPrice;
                                                        }

                                                        $totalPrice = $campaignProduct->product->price;
                                                        if($campaignProduct->product->offer_price != null){
                                                            $offerPrice = $campaignProduct->product->offer_price;
                                                            $offer = $totalPrice - $offerPrice;
                                                            $percentage = ($offer * 100) / $totalPrice;
                                                            $percentage = round($percentage);
                                                        }


                                                    @endphp

                                                    @if ($isCampaign)
                                                        <h4>{{ $currencySetting->currency_icon }} <span id="mainProductModalPrice-{{ $campaignProduct->product->id }}">{{ sprintf("%.2f", $campaignOfferPrice + $variantPrice) }}</span>  <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></h4>
                                                    @else
                                                        @if ($campaignProduct->product->offer_price == null)
                                                            <h4>{{ $currencySetting->currency_icon }}<span id="mainProductModalPrice-{{ $campaignProduct->product->id }}">{{ sprintf("%.2f", $totalPrice + $variantPrice) }}</span></h4>
                                                        @else
                                                            <h4>{{ $currencySetting->currency_icon }}<span id="mainProductModalPrice-{{ $campaignProduct->product->id }}">{{ sprintf("%.2f", $campaignProduct->product->offer_price + $variantPrice) }}</span> <del>{{ $currencySetting->currency_icon }}{{ sprintf("%.2f", $totalPrice) }}</del></h4>
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
                                                            if ($campaignProduct->product->offer_price == null) {
                                                                $productPrice = $totalPrice + $variantPrice;
                                                            }else {
                                                                $productPrice = $campaignProduct->product->offer_price + $variantPrice;
                                                            }
                                                        }
                                                    @endphp
                                                    <form id="productModalFormId-{{ $campaignProduct->product->id }}">
                                                    <div class="wsus__quentity">
                                                        <h5>{{__('quantity') }} :</h5>
                                                        <div class="modal_btn">
                                                            <button onclick="productModalDecrement('{{ $campaignProduct->product->id }}')" type="button" class="btn btn-danger btn-sm">-</button>
                                                            <input id="productModalQty-{{ $campaignProduct->product->id }}" name="quantity"  readonly class="form-control" type="text" min="1" max="100" value="1" />
                                                            <button onclick="productModalIncrement('{{ $campaignProduct->product->id }}')" type="button" class="btn btn-success btn-sm">+</button>
                                                        </div>
                                                        <h3 class="d-none">{{ $currencySetting->currency_icon }}<span id="productModalPrice-{{ $campaignProduct->product->id }}">{{ sprintf("%.2f",$productPrice) }}</span></h3>

                                                        <input type="hidden" name="product_id" value="{{ $campaignProduct->product->id }}">
                                                        <input type="hidden" name="image" value="{{ $campaignProduct->product->thumb_image }}">
                                                        <input type="hidden" name="slug" value="{{ $campaignProduct->product->slug }}">

                                                    </div>
                                                    @php
                                                        $productVariants = App\Models\ProductVariant::where(['status' => 1, 'product_id'=> $campaignProduct->product->id])->get();
                                                    @endphp
                                                    @if ($productVariants->count() != 0)
                                                        <div class="wsus__selectbox">
                                                            <div class="row">
                                                                @foreach ($productVariants as $productVariant)
                                                                    @php
                                                                        $items = App\Models\ProductVariantItem::orderBy('is_default','desc')->where(['product_variant_id' => $productVariant->id, 'product_id' => $campaignProduct->product->id])->get();
                                                                    @endphp
                                                                    @if ($items->count() != 0)
                                                                        <div class="col-xl-6 col-sm-6 mb-3">
                                                                            <h5 class="mb-2">{{ $productVariant->name }}:</h5>

                                                                            <input type="hidden" name="variants[]" value="{{ $productVariant->id }}">
                                                                            <input type="hidden" name="variantNames[]" value="{{ $productVariant->name }}">

                                                                            <select class="select_2 productModalVariant" name="items[]" data-product="{{ $campaignProduct->product->id }}">
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
                                                        <li><button type="button" onclick="addToCartInProductModal('{{ $campaignProduct->product->id }}')" class="add_cart">{{__('add to cart')}}</button></li>
                                                        <li><a class="buy_now" href="javascript:;" onclick="addToBuyNow('{{ $campaignProduct->product->id }}')">{{__('buy now')}}</a></li>
                                                        <li><a href="javascript:;" onclick="addToWishlist('{{ $campaignProduct->product->id }}')"><i class="fal fa-heart"></i></a></li>
                                                        <li><a href="javascript:;" onclick="addToCompare('{{ $campaignProduct->product->id }}')"><i class="far fa-random"></i></a></li>
                                                    </ul>
                                                </form>
                                                @if ($campaignProduct->product->sku)
                                                <p class="brand_model"><span>{{__('Model')}} :</span> {{ $campaignProduct->product->sku }}</p>
                                                @endif

                                                <p class="brand_model"><span>{{__('Brand')}} :</span> <a href="{{ route('product',['brand' => $campaignProduct->product->brand->slug]) }}">{{ $campaignProduct->product->brand->name }}</a></p>
                                                <p class="brand_model"><span>{{__('Category')}} :</span> <a href="{{ route('product',['category' => $campaignProduct->product->category->slug]) }}">{{ $campaignProduct->product->category->name }}</a></p>
                                                <div class="wsus__pro_det_share d-none">
                                                    <h5>{{__('share')}} :</h5>
                                                    <ul class="d-flex">
                                                        <li><a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ route('product-detail', $campaignProduct->product->slug) }}&t={{ $campaignProduct->product->name }}"><i class="fab fa-facebook-f"></i></a></li>
                                                        <li><a class="twitter" href="https://twitter.com/share?text={{ $campaignProduct->product->name }}&url={{ route('product-detail', $campaignProduct->product->slug) }}"><i class="fab fa-twitter"></i></a></li>
                                                        <li><a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url={{ route('product-detail', $campaignProduct->product->slug) }}&title={{ $campaignProduct->product->name }}"><i class="fab fa-linkedin"></i></a></li>
                                                        <li><a class="pinterest" href="https://www.pinterest.com/pin/create/button/?description={{ $campaignProduct->product->name }}&media=&url={{ route('product-detail', $campaignProduct->product->slug) }}"><i class="fab fa-pinterest-p"></i></a></li>
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
