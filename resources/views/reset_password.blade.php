@extends('layout')
@section('title')
    <title>{{__('Reset Password')}}</title>
@endsection
@section('meta')
    <meta name="description" content="{{__('Reset Password')}}">
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
                        <h4>{{__('Reset Password')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="javascript:;">{{__('Reset Password')}}</a></li>
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
        FORGET PASSWORD START
    ==============================-->
    <section id="wsus__login_register">
        <div class="container">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="wsus__forget_area">
                        <span class="qiestion_icon"><i class="fal fa-question-circle"></i></span>
                        <h4>{{__('Reset password')}}</h4>
                        <div class="wsus__login">
                            <form action="{{ route('store-reset-password',$token) }}" method="POST">
                                @csrf
                                <div class="wsus__login_input">
                                    <i class="fal fa-envelope"></i>
                                    <input type="email" placeholder="{{__('Email')}}" name="email" value="{{ $user->email }}">
                                </div>

                                <div class="wsus__login_input">
                                    <i class="fas fa-key"></i>
                                    <input type="password" placeholder="{{__('Password')}}" name="password">
                                </div>

                                <div class="wsus__login_input">
                                    <i class="fas fa-key"></i>
                                    <input type="password" placeholder="{{__('Confirm Password')}}" name="password_confirmation">
                                </div>

                                @if($recaptchaSetting->status==1)
                                    <div class="col-xl-12">
                                        <div class="wsus__single_com mb-3">
                                            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSetting->site_key }}"></div>
                                        </div>
                                    </div>
                                @endif

                                <button class="common_btn" type="submit">{{__('Reset Password')}}</button>
                            </form>
                        </div>
                        <a class="see_btn mt-4" href="{{ route('login') }}">{{__('go to login')}}</a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!--============================
        FORGET PASSWORD END
    ==============================-->

@endsection
