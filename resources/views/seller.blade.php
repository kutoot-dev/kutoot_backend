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
                        <h4>{{__('Seller')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('sellers') }}">{{__('Seller')}}</a></li>
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
      VENDORS DETAILA START
    ==============================-->
    <section id="wsus__product_page" class="wsus__vendors">
        <div class="container">
            <div class="row">
                @if ($sellers->count() > 0)
                <div class="col-12">
                    <div class="row">
                        @foreach ($sellers as $seller)
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__vendor_single">
                                <img src="{{ asset($seller->banner_image) }}" alt="vendor" class="img-fluid w-100">
                                <div class="wsus__vendor_text">
                                    <div class="wsus__vendor_text_center">
                                        <h4>{{ $seller->shop_name }}</h4>

                                        @php
                                            $reviewQty = App\Models\ProductReview::where('status',1)->where('product_vendor_id',$seller->id)->count();
                                            $totalReview = App\Models\ProductReview::where('status',1)->where('product_vendor_id',$seller->id)->sum('rating');
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
                                        <p class="wsus__vendor_rating">
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
                                            <p class="wsus__vendor_rating">
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <i class="fal fa-star"></i>
                                                <span>(0 {{__('review')}})</span>
                                            </p>
                                        @endif

                                        <a href="callto:{{ $seller->phone }}"><i class="far fa-phone-alt"></i> {{ $seller->phone }}</a>
                                        <a href="mailto:{{ $seller->email }}"><i class="fal fa-envelope"></i> {{ $seller->email }}</a>
                                        <a href="{{ route('seller-detail',['shop_name' => $seller->slug]) }}" class="common_btn">{{__('visit store')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="col-12 text-center">
                    <h3 class="text-danger">{{__('Seller not found')}}</h3>
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection
