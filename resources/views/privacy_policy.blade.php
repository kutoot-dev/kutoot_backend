@extends('layout')
@section('title')
    <title>{{__('Privacy Policy')}}</title>
@endsection
@section('meta')
    <meta name="description" content="{{__('Privacy Policy')}}">
@endsection

@section('public-content')
    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{ $privacyPolicy ? asset($privacyPolicy->privacy_banner) : '' }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('Privacy Policy')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('privacy-policy') }}">{{__('Privacy Policy')}}</a></li>
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
                    @if ($privacyPolicy)
                    {!! clean($privacyPolicy->privacy_policy) !!}
                    @endif

                </div>
            </div>
        </div>
    </section>
    <!--============================
        CUSTOM PAGES PAGE END
    ==============================-->

@endsection
