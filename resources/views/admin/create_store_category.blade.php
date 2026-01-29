@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Create Store Category')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Store Category')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin.store-category.index') }}">{{__('admin.Store Categories')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.store-category.index') }}" class="btn btn-primary"><i class="fas fa-backward"></i> {{__('admin.Go Back')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.store-category.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="form-group col-12">
                                    <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Image')}}</label>
                                    <input type="file" name="image" class="form-control-file" accept="image/*">
                                    <small class="text-muted">{{__('admin.Recommended size: 600x400 pixels. Max 5MB. Will be converted to WebP.')}}</small>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Icon')}}</label>
                                    <input type="file" name="icon" class="form-control-file" accept="image/*">
                                    <small class="text-muted">{{__('admin.Recommended size: 128x128 pixels. Max 2MB. Will be converted to WebP.')}}</small>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Serial')}}</label>
                                    <input type="number" name="serial" class="form-control" value="{{ old('serial', 0) }}" min="0">
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="is_active" class="form-control" required>
                                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>{{__('admin.Active')}}</option>
                                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>{{__('admin.Inactive')}}</option>
                                    </select>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Save')}}</button>
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
