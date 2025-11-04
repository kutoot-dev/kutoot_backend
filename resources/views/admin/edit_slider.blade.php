@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Slider')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Edit Slider')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit Slider')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.slider.index') }}" class="btn btn-primary"><i class="fas fa-backward"></i> {{__('admin.Go Back')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.slider.update',$slider->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Current Slider')}}</label>
                                    <div>
                                        <img src="{{ asset($slider->image) }}" alt="" width="200px">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.New Slider')}}</label>
                                    <input type="file" name="slider_image" class="form-control-file">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Badge')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="badge" class="form-control" value="{{ $slider->badge }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Title One')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="title_one" class="form-control" value="{{ $slider->title_one }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Title Two')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="title_two" class="form-control" value="{{ $slider->title_two }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Product Link')}} <span class="text-danger">*</span></label>
                                    <select name="product_slug" id="" class="form-control select2">
                                        <option value="">{{__('admin.Select Product')}}</option>
                                        @foreach ($products as $product)
                                            <option {{ $slider->product_slug == $product->slug ? 'selected' : '' }} value="{{ $product->slug }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Serial')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="serial" class="form-control" value="{{ $slider->serial }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option {{ $slider->status == 1 ? 'selected' : '' }} value="1">{{__('admin.Active')}}</option>
                                        <option {{ $slider->status == 0 ? 'selected' : '' }} value="0">{{__('admin.Inactive')}}</option>
                                    </select>
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
        </section>
      </div>
@endsection
