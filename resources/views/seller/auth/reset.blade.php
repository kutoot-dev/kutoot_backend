@include('admin.header')
<div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
            <div class="col-md-4"></div>
          <div class="col-md-4">
            <div class="login-brand">
              <img src="{{ asset($setting->logo) }}" alt="logo" width="150" class="shadow-light">
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>{{__('admin.Reset Password')}}</h4></div>

              <div class="card-body">
                 @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
           <form method="POST" action="{{ route('seller.store.reset.password', $token) }}">
                            @csrf

                            <div class="form-group">
                                <label for="password">{{ __('admin.New Password') }}</label>
                                <input id="password" type="password" class="form-control" 
                                       name="password" required autofocus>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">{{ __('admin.Confirm Password') }}</label>
                                <input id="password_confirmation" type="password" class="form-control" 
                                       name="password_confirmation" required>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    {{ __('admin.Reset Password') }}
                                </button>
                            </div>
                        </form>
              </div>
            </div>

            <div class="mt-5 text-muted text-center">
                {{__('admin.Back To Login Page')}}, <a href="{{ route('seller.login') }}">{{__('admin.Click Here')}}</a>
            </div>

            <div class="simple-footer">
                {{ $setting->copyright }}
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
@include('admin.footer')


