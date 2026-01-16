@php
    $setting = App\Models\Setting::first();
@endphp

@include('store.header')
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
            {{-- Theme Toggle Button --}}
            <li class="nav-item mr-2">
              <button 
                type="button" 
                class="sh-theme-toggle" 
                onclick="toggleTheme()"
                title="Toggle light/dark mode"
              >
                <i class="fas fa-moon"></i>
                <i class="fas fa-sun"></i>
              </button>
            </li>

            <li class="dropdown dropdown-list-toggle"><a target="_blank" href="{{ $setting->frontend_url }}" class="nav-link nav-link-lg"><i class="fas fa-home"></i> {{__('admin.Visit Website')}}</i></a>
            </li>

          @php
              $header_store=Auth::guard('store')->user();
              $defaultProfile = App\Models\BannerImage::whereId('15')->first();
              $avatarRaw = $defaultProfile?->image;
              $avatarIsUrl = is_string($avatarRaw) && preg_match('/^https?:\\/\\//i', $avatarRaw);
              $avatarExists = is_string($avatarRaw) && $avatarRaw !== '' && !$avatarIsUrl && file_exists(public_path($avatarRaw));
          @endphp
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              @if($avatarIsUrl)
                <img alt="image" src="{{ $avatarRaw }}" class="rounded-circle mr-1">
              @elseif($avatarExists)
                <img alt="image" src="{{ asset($avatarRaw) }}" class="rounded-circle mr-1">
              @else
                <span class="mr-1" style="font-size: 28px; line-height: 1;">
                  <i class="fas fa-user-circle"></i>
                </span>
              @endif
            <div class="d-sm-none d-lg-inline-block">{{ $header_store?->owner_name ?? $header_store?->username }}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-divider"></div>
              <a href="{{ route('store.logout') }}" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i>{{__('admin.Logout')}}
              </a>
            </div>
          </li>
        </ul>
      </nav>

      @include('store.sidebar')

      <div class="main-content">
        @yield('store-content')
      </div>

      @include('store.footer')
    </div>
  </div>
</body>
</html>


