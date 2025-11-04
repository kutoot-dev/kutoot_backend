@extends('admin.master_layout')
@section('title')
<title>{{__('Add New Image')}}</title>
@endsection

@section('admin-content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>{{__('Add New Image')}}</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.image-items.index') }}">{{__('All Images')}}</a></div>
        <div class="breadcrumb-item">{{__('Add New Image')}}</div>
      </div>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <form action="{{ route('admin.image-items.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="title">{{__('Title')}} <span class="text-danger">*</span></label>
                  <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                  <label for="description">{{__('Description')}}</label>
                  <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>
<div class="form-group">
  <label for="image_type_id">{{__('Image Type')}}</label>
  <select name="image_type_id" id="image_type_id" class="form-control">
    <option value="">{{__('Select Existing Type')}}</option>
    @foreach ($types as $type)
      <option value="{{ $type->id }}" {{ old('image_type_id') == $type->id ? 'selected' : '' }}>
        {{ $type->name }}
      </option>
    @endforeach
  </select>
</div>

<div class="form-group">
  <label for="new_type">{{__('Or Add New Type')}}</label>
  <input type="text" name="new_type" id="new_type" class="form-control" placeholder="Enter new type name">
  <small class="text-muted">If this is filled, it will override selected existing type.</small>
</div>


                <div class="form-group">
                  <label for="images">{{__('Images')}}</label>
                  <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                </div>
              </div>

              <div class="card-footer text-right">
                <a href="{{ route('admin.image-items.index') }}" class="btn btn-secondary">{{__('Cancel')}}</a>
                <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
