@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Specification Key')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Specification Key')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.specification-key.index') }}">{{__('admin.Specification Key')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Specification Key')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.specification-key.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Specification Key')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.specification-key.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Key')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="key" class="form-control"  name="key">
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
