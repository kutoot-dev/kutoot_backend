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
    <section id="wsus__breadcrumb" style="background: url({{ $contact ? asset($contact->banner) : '' }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('contact us')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                            <li><a href="{{ route('contact-us') }}">{{__('Contact us')}}</a></li>
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
        CONTACT PAGE START
    ==============================-->
    <section id="wsus__contact">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="wsus__contact_header">
                        <h4>{{ $contact ? $contact->title : ''}}</h4>
                        <p>{{ $contact ? $contact->description : ''}}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-sm-12 col-lg-4">
                    <div class="wsus__contact_single">
                        <i class="fal fa-envelope"></i>
                        <h5>{{__('mail address')}}</h5>
                        @if ($contact)
                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                        @endif

                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-lg-4">
                    <div class="wsus__contact_single">
                        <i class="far fa-phone-alt"></i>
                        <h5>{{__('phone number')}}</h5>
                        @if ($contact)
                        <a href="macallto:{{ $contact->phone }}">{{ $contact->phone }}</a>
                        @endif
                    </div>
                </div>
                <div class="col-xl-4 col-sm-12 col-lg-4">
                    <div class="wsus__contact_single">
                        <i class="fal fa-map-marker-alt"></i>
                        <h5>{{__('contact address')}}</h5>
                        @if ($contact)
                        <a href="javascript::void()">{{ $contact->address }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="wsus__contact_question">
                        <h5>{{__('Send Us a Message')}}</h5>
                        <form method="POST" action="{{ route('send-contact-message') }}">
                            @csrf
                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <div class="wsus__con_form_single">
                                        <input type="text" name="name" placeholder="{{__('Name')}}">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <div class="wsus__con_form_single">
                                        <input type="email" name="email"  placeholder="{{__('Email')}}">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <div class="wsus__con_form_single">
                                        <input type="text" name="phone"  placeholder="{{__('Phone')}}">
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6">
                                    <div class="wsus__con_form_single">
                                        <input type="text" placeholder="{{__('Subject')}}" name="subject">
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="wsus__con_form_single">
                                        <textarea cols="3" rows="5" placeholder="{{__('Message')}}" name="message"></textarea>
                                    </div>
                                </div>
                            </div>

                            @if($recaptchaSetting->status==1)
                                <div class="col-xl-12">
                                    <div class="wsus__single_com mb-3">
                                        <div class="g-recaptcha" data-sitekey="{{ $recaptchaSetting->site_key }}"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-12">
                                <button type="submit" class="common_btn">{{__('send now')}}</button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="col-12">
                    @if ($contact)
                    <div class="wsus__con_map">
                        {!! $contact->map !!}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
    <!--============================
        CONTACT PAGE END
    ==============================-->

@endsection
