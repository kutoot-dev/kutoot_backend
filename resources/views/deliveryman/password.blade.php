@extends('deliveryman.master_layout')
@section('title')
<title>{{__('Change Password')}}</title>
@endsection
@section('deliveryman-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('Change password')}}</h1>
      </div>

      <div class="section-body">
        <div class="row mt-4">
            <div class="col-12">
                <form action="{{ route('deliveryman.update-password') }}" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                                @csrf
                                @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('Password')}} <span class="text-danger">*</span></label>
                                    <input type="password" id="password" class="form-control"  name="password" value="{{ old('password') }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('Confirm Password')}} <span class="text-danger">*</span></label>
                                    <input type="password" id="c_password" class="form-control"  name="c_password" value="{{ old('c_password') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </section>
  </div>

@endsection
