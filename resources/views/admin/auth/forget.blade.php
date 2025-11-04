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
              <div class="card-header"><h4>{{__('admin.Forgot Password')}}</h4></div>

              <div class="card-body">
                <form method="POST" action="{{ route('admin.send.forget.password') }}">
                    @csrf
                  <div class="form-group">
                    <label for="email">{{__('admin.Email')}}</label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="1" autofocus>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      {{__('admin.Forgot Password')}}
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="mt-5 text-muted text-center">
                {{__('admin.Back To Login Page')}}, <a href="{{ route('admin.login') }}">{{__('admin.Click Here')}}</a>
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


