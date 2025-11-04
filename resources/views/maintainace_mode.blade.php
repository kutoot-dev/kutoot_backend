@php
    $setting = App\Models\Setting::first();
    $maintainance = App\Models\MaintainanceText::first();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densityDpi=device-dpi" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
        <title>{{__("Maintainance")}}</title>
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

        <link rel="stylesheet" href="{{ asset('user/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('user/css/responsive.css') }}">

        <link rel="stylesheet" href="{{ asset('user/css/dev.css') }}">

        <!--jquery library js-->
        <script src="{{ asset('user/js/jquery-3.6.0.min.js') }}"></script>
</head>

<body>



    <!--============================
        404 PAGE START
    ==============================-->
    <section id="wsus__404">
        <div class="container">
            <div class="row">
                <div class="col-xl-7 col-md-10 col-lg-8 col-xxl-6 m-auto">
                    <div class="wsus__404_text">
                        <img width="200px" src="{{ asset($maintainance->image) }}" alt="">
                        <p>{!! clean(nl2br($maintainance->description)) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        404 PAGE END
    ==============================-->



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

    <script src="{{ asset('toastr/toastr.min.js') }}"></script>
</body>

</html>
