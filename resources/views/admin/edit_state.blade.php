@extends('admin.master_layout')
@section('title')
<title>{{__('admin.State')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create State')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.state.index') }}">{{__('admin.State')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create State')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.state.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.State')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.state.update',$state->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="form-group col-12">
                                    <label>{{__('admin.Country')}} <span class="text-danger">*</span></label>
                                    <select name="country" id="" class="form-control select2">
                                        <option value="">{{__('admin.Select Country')}}</option>
                                        @foreach ($countries as $country)
                                        <option {{ $state->country_id == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.State Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name" value="{{ $state->name }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option {{ $state->status == 1 ? 'selected' : '' }} value="1">{{__('admin.Active')}}</option>
                                        <option {{ $state->status == 0 ? 'selected' : '' }} value="0">{{__('admin.Inactive')}}</option>
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
