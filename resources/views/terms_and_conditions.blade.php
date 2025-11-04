@extends('layout')
@section('title')
    <title>{{__('Terms And Conditions')}}</title>
@endsection
@section('meta')
    <meta name="description" content="{{__('Terms And Conditions')}}">
@endsection

@section('public-content')
    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{ $terms_conditions ? asset($terms_conditions->terms_condition_banner) : '' }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('Terms And Condition')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('terms-and-conditions') }}">{{__('Terms And Conditions')}}</a></li>
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
                    @if ($terms_conditions)
                    {!! clean($terms_conditions->terms_and_condition) !!}
                    @endif

                </div>
            </div>
        </div>
    </section>
    <!--============================
        CUSTOM PAGES PAGE END
    ==============================-->

@endsection
