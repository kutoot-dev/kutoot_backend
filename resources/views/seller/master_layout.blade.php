@php
    $setting = App\Models\Setting::first();
@endphp

@include('seller.header')
<body>
  <div>
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg custom_click"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
            <li class="dropdown dropdown-list-toggle"><a target="_blank" href="{{ $setting->frontend_url }}" class="nav-link nav-link-lg"><i class="fas fa-home"></i> {{__('admin.Visit Website')}}</i></a>
            </li>

          @php
              $header_seller=Auth::guard('web')->user();
              $defaultProfile = App\Models\BannerImage::whereId('15')->first();
          @endphp
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              @if ($header_seller->image)
              <img alt="image" src="{{ asset($header_seller->image) }}" class="rounded-circle mr-1">
              @else
              <img alt="image" src="{{ asset($defaultProfile->image) }}" class="rounded-circle mr-1">
              @endif
            <div class="d-sm-none d-lg-inline-block">{{ $header_seller->name }}</div></a>
            <div class="dropdown-menu dropdown-menu-right">

              <a href="{{ route('seller.my-profile') }}" class="dropdown-item has-icon">
                <i class="far fa-user"></i>{{__('admin.My Profile')}}
              </a>

              <a href="{{ route('seller.shop-profile') }}" class="dropdown-item has-icon">
                <i class="fas fa-store"></i>{{__('admin.Shop Profile')}}
              </a>

              <a href="{{ route('seller.forgot-password') }}" class="dropdown-item has-icon">
                <i class="fas fa-lock"></i>{{__('admin.Change Password')}}
              </a>



              <div class="dropdown-divider"></div>
              <a href="{{ route('seller.logout') }}" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i>{{__('admin.Logout')}}
              </a>


            </div>
          </li>
        </ul>
      </nav>




      @include('seller.sidebar')

      @yield('seller-content')



      <footer class="main-footer">
        <div class="footer-left">
          {{ $setting->copyright }}
        </div>
      </footer>
    </div>
  </div>

  @include('seller.footer')
