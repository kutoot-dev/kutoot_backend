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
                        <h4>{{__('Blog')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('blog') }}">{{__('Blog')}}</a></li>
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
        BLOGS PAGE START
    ==============================-->
    <section id="wsus__blogs">
        <div class="container">
            <div class="row">
                @php
                    $colorId=1;
                @endphp
                @foreach ($blogs as $index => $blog)
                @php
                    if($index %4 ==0){
                        $colorId=1;
                    }

                    $color="";
                    if($colorId==1){
                        $color="blue";
                    }else if($colorId==2){
                        $color="red";
                    }else if($colorId==3){
                        $color="orange";
                    }else if($colorId==4){
                        $color="green";
                    }
                @endphp
                    <div class="col-xl-4 col-sm-6 col-lg-4 col-xxl-3">
                        <div class="wsus__single_blog">
                            <a class="wsus__blog_img" href="#">
                                <img src="{{ asset($blog->image) }}" alt="blog" class="img-fluid w-100">
                            </a>
                            <a class="blog_top {{ $color }}" href="{{ route('blog-by-category', $blog->category->slug) }}">{{ $blog->category->name }}</a>
                            <div class="wsus__blog_text">
                                <div class="wsus__blog_text_center">
                                    <a href="{{ route('blog-detail',$blog->slug) }}">{{ $blog->title }}</a>
                                    <p class="date"><span>{{ $blog->created_at->format('d F, Y') }}</span> {{__('Hosted by')}} {{ $blog->admin->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $colorId ++;
                    @endphp
                @endforeach
                <div class="col-xl-12">
                    {{ $blogs->links('custom_paginator') }}
                </div>
            </div>
            @if ($blogs->count() == 0)
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="text-danger text-center">{{__('Blog Not Found')}}</h2>
                    </div>
                </div>
            @endif


        </div>
    </section>
    <!--============================
        BLOGS PAGE END
    ==============================-->

@endsection
