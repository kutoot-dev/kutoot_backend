@extends('admin.master_layout')
@section('title')
  <title>{{__('admin.Country')}}</title>
@endsection
@section('admin-content')
  <!-- Main Content -->
  <div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('admin.Edit Country')}}</h1>
        <div class="section-header-breadcrumb">
          <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
          <div class="breadcrumb-item active"><a href="{{ route('admin.country.index') }}">{{__('admin.Country')}}</a>
          </div>
          <div class="breadcrumb-item">{{__('admin.Edit Country')}}</div>
        </div>
      </div>

      <div class="section-body">
        <a href="{{ route('admin.country.index') }}" class="btn btn-primary"><i class="fas fa-list"></i>
          {{__('admin.Country')}}</a>
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <form action="{{ route('admin.country.update', $country->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="row">
                    <div class="form-group col-12">
                      <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                      <input type="text" id="name" class="form-control" name="name" value="{{ $country->name }}">
                    </div>
                    <div class="form-group col-12">
                      <label>{{__('admin.Phone Code')}}</label>
                      <input type="text" id="phone_code" class="form-control" name="phone_code"
                        value="{{ $country->phone_code }}" placeholder="+1">
                    </div>
                    <div class="form-group col-12">
                      <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                      <select name="status" class="form-control">
                        <option value="1" {{ $country->status == 1 ? 'selected' : '' }}>{{__('admin.Active')}}</option>
                        <option value="0" {{ $country->status == 0 ? 'selected' : '' }}>{{__('admin.Inactive')}}</option>
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