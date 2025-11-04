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
                <form action="{{ route('deliveryman.password.reset.email') }}" method="POST">
                    @csrf
                  <div class="form-group">
                    <label for="email">{{__('admin.Email')}}</label>
                    <input id="email exampleInputEmail" type="email" class="form-control" name="email" tabindex="1" autofocus value="{{ old('email') }}">
                  </div>

                  <div class="form-group">
                    <button id="adminLoginBtn" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      {{__('Send password reset link')}}
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


