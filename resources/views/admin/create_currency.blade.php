@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Create Currency')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Currency')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Currency')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.currency.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Currency')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.currency.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Currency Name')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="currency_name" value="{{ old('currency_name') }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Country Code')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="country_code" value="{{ old('country_code') }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Currency Code')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="currency_code" value="{{ old('currency_code') }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Currency Icon')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="currency_icon" value="{{ old('currency_icon') }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Currency Rate')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="currency_rate" value="{{ old('currency_rate') }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Default')}} <span class="text-danger">*</span></label>
                                    <select name="is_default" class="form-control">
                                        <option value="No">{{__('admin.No')}}</option>
                                        <option value="Yes">{{__('admin.Yes')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Currency Position')}} <span class="text-danger">*</span></label>
                                    <select name="currency_position" class="form-control">
                                        <option value="left">{{__('admin.Before Price')}}</option>
                                        <option value="right">{{__('admin.After Price')}}</option>
                                    </select>
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
