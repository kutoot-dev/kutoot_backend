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
              <div class="card-header"><h4>{{__('admin.Login')}}</h4></div>

              <div class="card-body">
                <form class="needs-validation" novalidate="" action="{{ route('admin.login') }}" method="POST">
                    @csrf

                  <div class="form-group">
                    <label for="email">{{__('admin.Email')}}</label>
                    <input id="email exampleInputEmail" type="email" class="form-control" name="email" tabindex="1" autofocus value="{{ old('email') }}">
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                    	<label for="password" class="control-label">{{__('admin.Password')}}</label>

                    </div>
                    <input id="password exampleInputPassword" type="password" class="form-control" name="password" tabindex="2">
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember" {{ old('remember') ? 'checked' : '' }}>
                      <label class="custom-control-label" for="remember">{{__('admin.Remember Me')}}</label>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      {{__('admin.Login')}}
                    </button>
                  </div>


        
                </form>

              </div>
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


