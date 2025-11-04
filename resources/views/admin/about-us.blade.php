@extends('admin.master_layout')
@section('title')
<title>{{__('admin.About Us')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.About Us')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.About Us')}}</div>
            </div>
          </div>
​
          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        @if ($aboutUs)
                            <form action="{{ route('admin.about-us.update',$aboutUs->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="">{{__('admin.Existing Image')}}</label>
                                        <div>
                                            <img class="w_300" src="{{ asset($aboutUs->banner_image) }}" alt="">
                                        </div>
                                    </div>
​
                                    <div class="form-group col-12">
                                        <label for="">{{__('admin.New Image')}}</label>
                                        <input type="file" name="banner_image" class="form-control-file">
                                    </div>
​
​
                                    <div class="form-group col-12">
                                        <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                        <textarea name="description" cols="30" rows="10" class="summernote">{{ $aboutUs->description }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form action="{{ route('admin.about-us.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="">{{__('admin.Image')}}</label>
                                        <input type="file" name="banner_image" class="form-control-file">
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                        <textarea name="description" cols="30" rows="10" class="summernote"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">{{__('admin.Save')}}</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
