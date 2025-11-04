@extends('admin.master_layout')
@section('title')
<title>{{__('admin.My Profile')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.My Profile')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.My Profile')}}</div>
            </div>
          </div>
          <div class="section-body">
            <div class="row mt-sm-4">
              <div class="col-8">
                <div class="card profile-widget">
                  <div class="profile-widget-header">
                      @if ($admin->image)
                      <img alt="image" src="{{ asset($admin->image) }}" class="rounded-circle profile-widget-picture">
                      @else
                      <img alt="image" src="" class="rounded-circle profile-widget-picture">
                      @endif
                  </div>
                  <div class="profile-widget-description">
                    <form action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')
                        <div class="row">
                            <div class="form-group col-12">
                            <label>{{__('admin.New Image')}}</label>
                            <input type="file" class="form-control-file" name="image">
                            </div>
                            <div class="form-group col-12">
                            <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $admin->name }}" name="name">
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Email')}} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" value="{{ $admin->email }}" name="email">
                            </div>

                            <div class="form-group col-12">
                            <label>{{__('admin.Password')}}</label>
                            <input type="password" class="form-control" name="password">
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Confirm Password')}}</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary">{{__('admin.Update')}}</button>
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
@endsection
