@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Service')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Service')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.service.index') }}">{{__('admin.Service')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Service')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.service.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Services')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.service.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Icon')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="slug" class="form-control custom-icon-picker"  name="icon">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                    <textarea name="description" cols="30" rows="10" class="form-control text-area-5"></textarea>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{__('admin.Active')}}</option>
                                        <option value="0">{{__('admin.Inactive')}}</option>
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
        </section>
      </div>
@endsection
