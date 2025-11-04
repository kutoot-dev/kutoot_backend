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
    <section id="wsus__breadcrumb" style="background: url({{ $aboutUs ? asset($aboutUs->banner_image) : '' }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('About us')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                            <li><a href="{{ route('about-us') }}">{{__('About us')}}</a></li>
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
        CUSTOM PAGES PAGE START
    ==============================-->
    <section id="wsus__product_page" class="wsus__custom_pages">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    @if ($aboutUs)
                    {!! clean($aboutUs->description) !!}
                    @endif

                </div>
            </div>
        </div>
    </section>
    <!--============================
        CUSTOM PAGES PAGE END
    ==============================-->

@endsection
