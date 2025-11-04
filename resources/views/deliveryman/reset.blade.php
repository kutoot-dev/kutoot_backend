@include('admin.header')
<div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
        <div class="col-md-4"></div>
          <div class="col-md-4">

            <div class="card card-primary">
              <div class="card-header"><h4>{{__('Reset password')}}</h4></div>

              <div class="card-body">
                <form action="{{ route('deliveryman.pasword.update') }}" method="POST">
                    @csrf
                  <div class="form-group">
                    <label for="email">{{__('Passwrod')}}</label>
                    <input id="password exampleInputEmail" type="password" class="form-control" name="password" tabindex="1" autofocus value="{{ old('password') }}">
                    <input type="hidden" name="token" value="{{ $token }}">
                  </div>
                  <div class="form-group">
                    <label for="email">{{__('Confirm passwrod')}}</label>
                    <input id="c_password exampleInputEmail" type="password" class="form-control" name="c_password" tabindex="1" autofocus value="{{ old('c_password') }}">
                  </div>

                  <div class="form-group">
                    <button id="adminLoginBtn" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      {{__('Reset passwrod')}}
                    </button>
                  </div>
                </form>

              </div>
            </div>
            <div class="simple-footer">
             
            </div>
          </div>
        </div>


      </div>
    </section>
  </div>
@include('admin.footer')


