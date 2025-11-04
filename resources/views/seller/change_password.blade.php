@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Change Password')}}</title>
@endsection
@section('seller-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('admin.Change Password')}}</h1>
        <div class="section-header-breadcrumb">
          <div class="breadcrumb-item active"><a href="{{ route('seller.my-profile') }}">{{__('admin.My Profile')}}</a></div>
          <div class="breadcrumb-item">{{__('admin.Change Password')}}</div>
        </div>
      </div>
      <div class="section-body">
        <div class="row mt-sm-4">
          <div class="col-12">
            <div class="card profile-widget">
              <div class="profile-widget-description">
                <form action="{{ route('seller.password-update') }}" method="POST">
                @csrf
                @method('PUT')
                    <div class="row">

                        <div class="form-group col-12">
                        <label>{{__('admin.New Password')}}</label>
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
