@extends('layout')
@section('title')
    <title>{{ $page->page_name }}</title>
@endsection
@section('meta')
    <meta name="description" content="{{ $page->page_name }}">
@endsection

@section('public-content')
    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{ asset($page->banner_image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{ $page->page_name }}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="javascript:;">{{ $page->page_name }}</a></li>
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
                    {!! clean($page->description) !!}
                </div>
            </div>
        </div>
    </section>
    <!--============================
        CUSTOM PAGES PAGE END
    ==============================-->

@endsection
