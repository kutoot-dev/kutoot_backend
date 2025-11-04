@extends('layout')
@section('title')
    <title>{{__('Login')}}</title>
@endsection
@section('meta')
    <meta name="description" content="{{__('Login')}}">
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
                        <h4>{{__('Login')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                            <li><a href="{{ route('login') }}">{{__('Login')}}</a></li>

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
       LOGIN/REGISTER PAGE START
    ==============================-->
    <section id="wsus__login_register">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-md-6">
                    <div class="wsus__login_reg_area">
                        <ul class="nav nav-pills mb-3" id="pills-tab2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-home-tab2" data-bs-toggle="pill" data-bs-target="#pills-homes" type="button" role="tab" aria-controls="pills-homes" aria-selected="true">{{__('SignIn')}}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-profile-tab2" data-bs-toggle="pill" data-bs-target="#pills-profiles" type="button" role="tab" aria-controls="pills-profiles" aria-selected="true">{{__('signup')}}</button>
                            </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent2">
                            <div class="tab-pane fade show active" id="pills-homes" role="tabpanel" aria-labelledby="pills-home-tab2">
                                <div class="wsus__login">
                                    <form action="{{ route('store-login') }}" method="POST">
                                        @csrf
                                        <div class="wsus__login_input">
                                            <i class="fas fa-user-tie"></i>
                                            <input type="email" placeholder="{{__('Email')}}" name="email">
                                        </div>
                                        <div class="wsus__login_input">
                                            <i class="fas fa-key"></i>
                                            <input type="password" placeholder="{{__('Password')}}" name="password">
                                        </div>
                                        <div class="wsus__login_save">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="remember">
                                                <label class="form-check-label" for="flexSwitchCheckDefault">{{__('Remember me')}}</label>
                                                </div>
                                                <a class="forget_p" href="{{ route('forget-password') }}">{{__('forget password ?')}}</a>
                                        </div>

                                        @if($recaptchaSetting->status==1)
                                            <div class="col-xl-12">
                                                <div class="wsus__single_com mb-3">
                                                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSetting->site_key }}"></div>
                                                </div>
                                            </div>
                                        @endif

                                        <button class="common_btn" type="submit">{{__('login')}}</button>
                                        @if ($socialLogin->is_gmail == 1 || $socialLogin->is_facebook == 1)
                                        <p class="social_text">{{__('Sign in with social account')}}</p>
                                        <ul class="wsus__login_link">
                                            @if ($socialLogin->is_gmail == 1)
                                            <li><a href="{{ route('login-google') }}"><i class="fab fa-google"></i></a></li>
                                            @endif
                                            @if ($socialLogin->is_facebook == 1)
                                            <li><a href="{{ route('login-facebook') }}"><i class="fab fa-facebook-f"></i></a></li>
                                            @endif
                                        </ul>
                                        @endif
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-profiles" role="tabpanel" aria-labelledby="pills-profile-tab2">
                                <div class="wsus__login">
                                    <form action="{{ route('store-register') }}" method="POST">
                                        @csrf
                                        <div class="wsus__login_input">
                                            <i class="fas fa-user-tie"></i>
                                            <input type="text" placeholder="{{__('Name')}}" name="name">
                                        </div>
                                        <div class="wsus__login_input">
                                            <i class="far fa-envelope"></i>
                                            <input type="text" placeholder="{{__('Email')}}" name="email">
                                        </div>
                                        <div class="wsus__login_input">
                                            <i class="fas fa-key"></i>
                                            <input type="password" placeholder="{{__('Password')}}" name="password">
                                        </div>
                                        <div class="wsus__login_input">
                                            <i class="fas fa-key"></i>
                                            <input type="password" placeholder="{{__('Confirm Password')}}" name="password_confirmation">
                                        </div>
                                        <div class="wsus__login_save">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault03" name="agree">
                                                <label class="form-check-label" for="flexSwitchCheckDefault03">{{__('I consent to the privacy policy')}}</label>
                                                </div>
                                        </div>

                                        @if($recaptchaSetting->status==1)
                                            <div class="col-xl-12">
                                                <div class="wsus__single_com mb-3">
                                                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSetting->site_key }}"></div>
                                                </div>
                                            </div>
                                        @endif

                                        <button class="common_btn" type="submit">{{__('signup')}}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-md-6">
                    <div class="wsus__login_reg_text">
                        <img src="{{ asset($background->image) }}" alt="login" class="img-fluid w-100">
                        <div class="wsus__login_reg_text_center">
                            <div class="wsus__login_text_position">
                                <h6>{{ $background->title }} <span>{{ $background->header }}</span></h6>
                                <p>{{ $background->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
       LOGIN/REGISTER PAGE END
    ==============================-->

@endsection
