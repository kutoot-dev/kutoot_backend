@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Edit Store Banner')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Edit Store Banner')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin.store-banner.index') }}">{{__('admin.Store Banners')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.store-banner.index') }}" class="btn btn-primary"><i class="fas fa-backward"></i> {{__('admin.Go Back')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.store-banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="form-group col-12">
                                    <label>{{__('admin.Current Banner Images')}}</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>{{__('admin.Desktop')}}</strong></p>
                                            @if($banner->image)
                                                <img src="{{ asset($banner->image) }}" alt="Desktop Banner" class="img-fluid" style="max-height: 150px;">
                                            @else
                                                <span class="text-muted">{{__('admin.No image')}}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>{{__('admin.Tablet')}}</strong></p>
                                            @if($banner->image_tablet)
                                                <img src="{{ asset($banner->image_tablet) }}" alt="Tablet Banner" class="img-fluid" style="max-height: 150px;">
                                            @else
                                                <span class="text-muted">{{__('admin.No image')}}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>{{__('admin.Mobile')}}</strong></p>
                                            @if($banner->image_mobile)
                                                <img src="{{ asset($banner->image_mobile) }}" alt="Mobile Banner" class="img-fluid" style="max-height: 150px;">
                                            @else
                                                <span class="text-muted">{{__('admin.No image')}}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.New Banner Image')}}</label>
                                    <input type="file" name="banner_image" class="form-control-file" accept="image/*">
                                    <small class="text-muted">{{__('admin.Leave empty to keep current images. Recommended size: 1920x600 pixels. Max 5MB. Will be optimized for all screen sizes.')}}</small>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}" required>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}}</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $banner->description) }}</textarea>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Link')}}</label>
                                    <input type="url" name="link" class="form-control" value="{{ old('link', $banner->link) }}" placeholder="https://example.com">
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Button Text')}}</label>
                                    <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $banner->button_text) }}" placeholder="{{__('admin.Shop Now')}}">
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Location')}}</label>
                                    <select name="location" class="form-control">
                                        <option value="">{{__('admin.Select Location')}}</option>
                                        <option value="use_coins_panel" {{ old('location', $banner->location) == 'use_coins_panel' ? 'selected' : '' }}>{{__('admin.Use Coins Panel')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Serial')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="serial" class="form-control" value="{{ old('serial', $banner->serial) }}" required>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Start Date')}}</label>
                                    <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d\TH:i') : '') }}">
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.End Date')}}</label>
                                    <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d\TH:i') : '') }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="1" {{ old('status', $banner->status) == 1 ? 'selected' : '' }}>{{__('admin.Active')}}</option>
                                        <option value="0" {{ old('status', $banner->status) == 0 ? 'selected' : '' }}>{{__('admin.Inactive')}}</option>
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
          </div>
        </section>
      </div>
@endsection
