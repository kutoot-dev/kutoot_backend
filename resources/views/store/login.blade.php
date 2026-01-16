@php
    $setting = App\Models\Setting::first();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="shortcut icon" href="{{ asset($setting->favicon) }}" type="image/x-icon">
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Store Login | {{ $setting->app_name ?? 'Kutoot' }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Inter Font --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/store-shadcn.css') }}">

  {{-- Theme preference script --}}
  <script>
    (function() {
      const savedTheme = localStorage.getItem('store-theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = savedTheme || (prefersDark ? 'dark' : 'light');
      if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.documentElement.classList.add('dark');
      }
    })();
  </script>
</head>
<body>
  <div class="sh-login-wrapper">
    <div class="sh-login-card">
      <div class="sh-login-header">
        @if($setting->logo && file_exists(public_path($setting->logo)))
          <img src="{{ asset($setting->logo) }}" alt="{{ $setting->app_name ?? 'Store' }}" style="height: 48px; margin-bottom: 1rem;">
        @endif
        <h2>Store Login</h2>
        <p>Welcome back! Please sign in to continue.</p>
      </div>

      <div class="sh-login-body">
        @if ($errors->any())
          <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
            @foreach ($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('store.login.submit') }}">
          @csrf

          <div class="form-group">
            <label for="username">Username</label>
            <div class="sh-input-icon-wrapper">
              <i class="fas fa-user sh-input-icon"></i>
              <input 
                type="text" 
                id="username"
                class="form-control" 
                name="username" 
                value="{{ old('username') }}" 
                placeholder="Enter your username"
                required 
                autofocus
              >
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="sh-input-icon-wrapper">
              <i class="fas fa-lock sh-input-icon"></i>
              <input 
                type="password" 
                id="password"
                class="form-control" 
                name="password" 
                placeholder="Enter your password"
                required
              >
            </div>
          </div>

          <button type="submit" class="btn btn-primary sh-login-btn">
            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
          </button>
        </form>
      </div>
    </div>

    {{-- Theme Toggle for Login Page --}}
    <button 
      type="button" 
      class="sh-theme-toggle" 
      style="position: fixed; bottom: 1.5rem; right: 1.5rem;"
      onclick="toggleTheme()"
      title="Toggle theme"
    >
      <i class="fas fa-moon"></i>
      <i class="fas fa-sun"></i>
    </button>
  </div>

  <script src="{{ asset('backend/js/jquery-3.6.0.min.js') }}"></script>
  <script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('toastr/toastr.min.js') }}"></script>

  <script>
    function toggleTheme() {
      const html = document.documentElement;
      const isDark = html.classList.contains('dark');
      
      if (isDark) {
        html.classList.remove('dark');
        html.removeAttribute('data-theme');
        localStorage.setItem('store-theme', 'light');
      } else {
        html.classList.add('dark');
        html.setAttribute('data-theme', 'dark');
        localStorage.setItem('store-theme', 'dark');
      }
    }
  </script>

  @if(Session::has('success'))
    <script>toastr.success("{{ Session::get('success') }}");</script>
  @endif

  @if(Session::has('error'))
    <script>toastr.error("{{ Session::get('error') }}");</script>
  @endif
</body>
</html>
