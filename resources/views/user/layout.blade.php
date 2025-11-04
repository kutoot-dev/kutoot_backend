@php
    $setting = App\Models\Setting::first();
    $user = Auth::guard('web')->user();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densityDpi=device-dpi" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @yield('title')
    <link rel="icon" type="image/png" href="{{ asset($setting->favicon) }}">
    <link rel="stylesheet" href="{{ asset('user/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/jquery.nice-number.min.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/jquery.calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/add_row_custon.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/mobile_menu.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/jquery.exzoom.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/multiple-image-video.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/ranger_style.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/jquery.classycountdown.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/venobox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/clockpicker/dist/bootstrap-clockpicker.css') }}">

    @if ($setting->text_direction == 'rtl')
    <link rel="stylesheet" href="{{ asset('user/css/rtl.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('user/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/responsive.css') }}">
    @include('theme_style_css')
    <!--jquery library js-->
    <script src="{{ asset('user/js/jquery-3.6.0.min.js') }}"></script>

    <script>
        var capmaign_time = 0;
        var filter_max_val = 1000;
        var currency_icon = $;
        var themeColor = "{{ $setting->theme_one }}";
    </script>

    <script>
        var end_year = '';
        var end_month = '';
        var end_date = '';
        var capmaign_time = '';
        var campaign_end_year = ''
        var campaign_end_month = ''
        var campaign_end_date = ''
        var campaign_hour = ''
        var campaign_min = ''
        var campaign_sec = ''
        var productIds = [];
        var productYears = [];
        var productMonths = [];
        var productDays = [];
    </script>
</head>

<body>

<div id="app">
  <!--=============================
        DASHBOARD MENU START
  ==============================-->
  <div class="wsus__dashboard_menu">
    <div class="wsusd__dashboard_user">
        @if ($user->image)
            <img src="{{ asset($user->image) }}" alt="img" class="img-fluid">
        @else
            @php
                $defaultProfile = App\Models\BannerImage::whereId('15')->first();
            @endphp
            <img src="{{ asset($defaultProfile->image) }}" alt="img" class="img-fluid">
        @endif
      <p>{{ $user->name }}</p>
    </div>
  </div>
  <!--=============================
        DASHBOARD MENU END
  ==============================-->


  <!--=============================
        DASHBOARD START
  ==============================-->
  <section id="wsus__dashboard">
    <div class="container-fluid">
      <div class="dashboard_sidebar">
        <span class="close_icon">
          <i class="far fa-bars dash_bar"></i>
          <i class="far fa-times dash_close"></i>
        </span>
        <a href="{{ route('user.dashboard') }}" class="dash_logo"><img src="{{ asset($setting->logo) }}" alt="logo" class="img-fluid"></a>
        <ul class="dashboard_link">
          <li><a class="{{ Route::is('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><i class="fas fa-tachometer"></i>{{__('Dashboard')}}</a></li>
          <li><a class="{{ Route::is('user.message') ? 'active' : '' }}" href="{{ route('user.message') }}"><i class="fas fa-envelope"></i>{{__('Message')}}</a></li>

          <li><a href="{{ route('home') }}"><i class="fal fa fa-globe"></i> {{__('Go to Homepage')}}</a></li>
          <li><a class="{{ Route::is('user.order') || Route::is('user.order-show') ? 'active' : '' }}" href="{{ route('user.order') }}"><i class="fas fa-list-ul"></i> {{__('Orders')}}</a></li>
          <li><a class="{{ Route::is('user.review') ? 'active' : '' }}" href="{{ route('user.review') }}"><i class="far fa-star"></i> {{__('Reviews')}}</a></li>
          <li><a class="{{ Route::is('user.wishlist') ? 'active' : '' }}" href="{{ route('user.wishlist') }}"><i class="far fa-heart"></i> {{__('Wishlist')}}</a></li>
          <li><a class="{{ Route::is('user.my-profile') ? 'active' : '' }}" href="{{ route('user.my-profile') }}"><i class="far fa-user"></i> {{__('My Profile')}}</a></li>
          <li><a class="{{ Route::is('user.address') ? 'active' : '' }}" href="{{ route('user.address') }}"><i class="fal fa-gift-card"></i> {{__('Address')}}</a></li>
        @if ($setting->enable_multivendor == 1)
            @php
                $authUser = Auth::guard('web')->user();
                $isSeller = App\Models\Vendor::where('user_id', $authUser->id)->first();
            @endphp
            @if ($isSeller)
                <li><a class="" href="{{ route('seller.dashboard') }}"><i class="fal fa-gift-card"></i> {{__('Visit Seller Dashboard')}}</a></li>
            @else
                <li><a class="{{ Route::is('user.seller-registration') ? 'active' : '' }}" href="{{ route('user.seller-registration') }}"><i class="fal fa-gift-card"></i> {{__('Become a Seller')}}</a></li>
            @endif

        @endif


          <li><a class="{{ Route::is('user.change-password') ? 'active' : '' }}" href="{{ route('user.change-password') }}"><i class="fal fa-gift-card"></i> {{__('Change Password')}}</a></li>

          <li><a href="{{ route('user.logout') }}"><i class="far fa-sign-out-alt"></i> {{__('Log out')}}</a></li>
        </ul>
      </div>

      @yield('user-content')
    </div>
  </section>
  <!--=============================
        DASHBOARD START
  ==============================-->


  <!--============================
        SCROLL BUTTON START
  ==============================-->
  <div class="wsus__scroll_btn">
    <i class="fas fa-chevron-up"></i>
  </div>
  <!--============================
      SCROLL BUTTON  END
  ==============================-->


   <!--============================
        SCROLL BUTTON START
    ==============================-->
    <div class="wsus__scroll_btn">
      <i class="fas fa-chevron-up"></i>
  </div>
  <!--============================
      SCROLL BUTTON  END
  ==============================-->

</div>

    <!--bootstrap js-->
    <script src="{{ asset('user/js/bootstrap.bundle.min.js') }}"></script>
    <!--font-awesome js-->
    <script src="{{ asset('user/js/Font-Awesome.js') }}"></script>
    <!--select2 js-->
    <script src="{{ asset('user/js/select2.min.js') }}"></script>
    <!--slick slider js-->
    <script src="{{ asset('user/js/slick.min.js') }}"></script>
    <!--simplyCountdown js-->
    <script src="{{ asset('user/js/simplyCountdown.js') }}"></script>
    <!--product zoomer js-->
    <script src="{{ asset('user/js/jquery.exzoom.js') }}"></script>
    <!--nice-number js-->
    <script src="{{ asset('user/js/jquery.nice-number.min.js') }}"></script>
    <!--counter js-->
    <script src="{{ asset('user/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('user/js/jquery.countup.min.js') }}"></script>
    <!--calender js-->
    <script src="{{ asset('user/js/jquery.calendar.js') }}"></script>
    <!--add row js-->
    <script src="{{ asset('user/js/add_row_custon.js') }}"></script>
    <!--multiple-image-video js-->
    <script src="{{ asset('user/js/multiple-image-video.js') }}"></script>
    <!--sticky sidebar js-->
    <script src="{{ asset('user/js/sticky_sidebar.js') }}"></script>
    <!--price ranger js-->
    <script src="{{ asset('user/js/ranger_jquery-ui.min.js') }}"></script>
    <script src="{{ asset('user/js/ranger_slider.js') }}"></script>
    <!--isotope js-->
    <script src="{{ asset('user/js/isotope.pkgd.min.js') }}"></script>
    <!--venobox js-->
    <script src="{{ asset('user/js/venobox.min.js') }}"></script>


    <!--classycountdown js-->
    <script src="{{ asset('user/js/jquery.classycountdown.js') }}"></script>
    <!--main/custom js-->
    <script src="{{ asset('user/js/main.js') }}"></script>
    <script src="{{ asset('backend/clockpicker/dist/bootstrap-clockpicker.js') }}"></script>



    <script src="{{ asset('toastr/toastr.min.js') }}"></script>

    <script>
        @if(Session::has('messege'))
        var type="{{Session::get('alert-type','info')}}"
        switch(type){
            case 'info':
                toastr.info("{{ Session::get('messege') }}");
                break;
            case 'success':
                toastr.success("{{ Session::get('messege') }}");
                break;
            case 'warning':
                toastr.warning("{{ Session::get('messege') }}");
                break;
            case 'error':
                toastr.error("{{ Session::get('messege') }}");
                break;
        }
        @endif
    </script>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
        <script>
            toastr.error('{{ $error }}');
        </script>
    @endforeach
    @endif


<script>
    (function($) {
    "use strict";
    $(document).ready(function () {

    });

    })(jQuery);
</script>
<script src="{{ asset('js/app.js') }}"></script>

<script>
    let activeSellerId= '';
    let myId = {{ Auth::guard('web')->user()->id; }};
    function loadChatBox(id){
        $(".seller").removeClass('active');
        $("#seller-list-"+id).addClass('active')
        $("#pending-"+ id).addClass('d-none')
        $("#pending-"+ id).html(0)
        activeSellerId = id
        $.ajax({
            type:"get",
            url: "{{ url('user/load-chat-box/') }}" + "/" + id,
            success:function(response){
                $("#chat_box").html(response)
                scrollToBottomFunc()
            },
            error:function(err){
            }
        })
    }


  (function($) {
      "use strict";
      $(document).ready(function () {
            $('.clockpicker').clockpicker();

            Echo.private("App.Models.User.{{$user->id}}")
            .listen('SellerToUser', (e) => {
                if(e.seller_id == activeSellerId){
                    $.ajax({
                        type:"get",
                        url: "{{ url('user/load-new-message/') }}" + "/" + e.seller_id,
                        success:function(response){
                            $(".wsus__chat_area_body").html(response)
                            scrollToBottomFunc()
                        },
                        error:function(err){
                        }
                    })
                }else{
                    var pending = parseInt($("#pending-"+ e.seller_id).html());
                    if (pending) {
                        $("#pending-"+ e.seller_id).html(pending + 1)
                        $("#pending-"+ e.seller_id).removeClass('d-none')
                    } else {
                        $("#pending-"+ e.seller_id).html(pending + 1)
                        $("#pending-"+ e.seller_id).removeClass('d-none')
                    }
                }

            });
      });
  })(jQuery);

    function scrollToBottomFunc() {
        $('.wsus__chat_area_body').animate({
            scrollTop: $('.wsus__chat_area_body').get(0).scrollHeight
        }, 100);
    }
</script>

</body>

</html>
