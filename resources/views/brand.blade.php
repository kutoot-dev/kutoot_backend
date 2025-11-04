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
                        <h4>{{__('Brand')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('brand') }}">{{__('Brand')}}</a></li>
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
        BRANDS PAGE START
    ==============================-->
    <section id="wsus__brands">
        <div class="container">
            <div class="row">
                @foreach ($brands as $brand)
                    <div class="col-xl-3 col-sm-6 col-lg-4">
                        <a href="{{ route('product',['brand' => $brand->slug]) }}" class="wsus__single_brand">
                            <img src="{{ asset($brand->logo) }}" alt="brand"
                            class="img-fluid w-100">
                            <span class="new">{{ $brand->name }}</span>
                            <span class="rating">{{ $brand->rating }} <i class="fas fa-star"></i></span>
                        </a>
                    </div>
                @endforeach

                <div class="col-xl-12">
                    {{ $brands->links('custom_paginator') }}
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BRANDS PAGE END
    ==============================-->

@endsection
