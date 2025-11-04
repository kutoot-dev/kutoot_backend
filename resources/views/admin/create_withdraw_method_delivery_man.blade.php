@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Withdraw Method')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Withdraw Method')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.delivery-man-withdraw-method.index') }}">{{__('admin.Withdraw Method')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Withdraw Method')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.withdraw-method.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Withdraw Method')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.delivery-man-withdraw-method.store') }}" method="POST" >
                            @csrf
                            <div class="row">

                                <div class="form-group col-12">
                                    <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name" value="{{ old('name') }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Minimum Amount')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="minimum_amount" class="form-control"  name="minimum_amount" value="{{ old('minimum_amount') }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Maximum Amount')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="maximum_amount" class="form-control"  name="maximum_amount" value="{{ old('maximum_amount') }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Withdraw Charge')}} <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">%</span>
                                        <input type="text" class="form-control" name="withdraw_charge">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                    <textarea name="description" id="" cols="30" rows="10" class="summernote">{{ old('description') }}</textarea>
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
