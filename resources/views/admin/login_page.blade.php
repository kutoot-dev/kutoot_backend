@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Login page')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Login page')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Login page')}}</div>
            </div>
          </div>

        <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.update-login-page') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>{{__('admin.Existing Image')}}</label>
                                        <div>
                                            <img src="{{ asset($banner->image) }}" alt="" width="200px">
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.New Background Image')}}</label>
                                        <input type="file" name="bg_image" class="form-control-file">
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        </section>
      </div>

@endsection
