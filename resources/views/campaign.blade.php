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
                        <h4>{{__('Campaign')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('campaign') }}">{{__('campaign')}}</a></li>
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
        DAILY DEALS START
    ==============================-->
    <section id="wsus__daily_deals">
        <div class="container">
            <div class="row">
                @if ($campaigns->count() > 0)
                    @foreach ($campaigns as $campaign)
                    <div class="col-xl-3 col-sm-6 col-lg-4">
                        <div class="wsus__daily_deals_single">
                            <div class="wsus__daily_deals_single_img">
                            <a class="link_img" href="{{ route('campaign-detail',$campaign->slug) }}"> <img src="{{ asset($campaign->image) }}" alt="offer" class="img-fluid w-100"></a>
                                <p>{{ $campaign->title }}</p>
                                @php
                                    $today = date('Y-m-d H:i:s');
                                @endphp
                                @if ($campaign->start_date <= $today &&  $today <= $campaign->end_date)
                                    <a class="blue" href="{{ route('campaign-detail',$campaign->slug) }}">{{__('live now')}}</a>
                                @elseif ($campaign->start_date > $today &&  $today < $campaign->end_date)
                                    <a class="live" href="{{ route('campaign-detail',$campaign->slug) }}">{{__('Upcoming')}}</a>
                                @else
                                <a class="live" href="{{ route('campaign-detail',$campaign->slug) }}">{{__('Expired')}}</a>
                                @endif

                            </div>
                            <div class="wsus__daily_deals_text">
                                <a class="deals_title" href="{{ route('campaign-detail',$campaign->slug) }}">{{ $campaign->name }}</a>
                                <p>{{__('sale start date')}}: {{ date('d F Y', strtotime($campaign->start_date)) }}</p>
                                <p>{{__('sale end date')}}: {{ date('d F Y', strtotime($campaign->end_date)) }}</p>
                                <a class="common_btn" href="{{ route('campaign-detail',$campaign->slug) }}">{{__('view deals')}}</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <h3 class="text-danger">{{__('Campaign not found')}}</h3>
                    </div>
                @endif

            </div>
        </div>
    </section>
    <!--============================
        DAILY DEALS END
    ==============================-->
@endsection
