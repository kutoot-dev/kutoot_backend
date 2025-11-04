@extends('admin.master_layout')
@section('title')
<title>{{__('Bulk Order')}}</title>
@endsection

@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Bulk Order Serch')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
            </div>
          </div>
          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{route ('admin.pos.bulk.order.serch')}}" method="get">

                            <div class="row">
                                <div class="form-group col-6">
                                    <label>{{ __('admin.Form') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datetimepicker_mask" name="form" value="{{ old('form') }}" placeholder="2025-09-14 14:57:00" autocomplete="off">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{ __('admin.To') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datetimepicker_mask" name="to" value="{{ old('to') }}" placeholder="2025-09-14 14:57:00" autocomplete="off">
                                </div>
                                <div class="form-group col-6">
                                    <label for="">{{ __('admin.Payment') }}</label>
                                    <select name="payment_status" id="" class="form-control">
                                        <option value="" disabled selected>{{ __('admin.Select a Payment Status') }}</option>
                                        <option value="0">{{ __('admin.Pending') }}</option>
                                        <option value="1">{{ __('admin.Success') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">{{ __('admin.Order') }}</label>
                                    <select name="order_status" id="" class="form-control">
                                      <option value="" disabled selected>{{ __('admin.Select a Order Status') }}</option>
                                      <option value="0">{{ __('admin.Pending') }}</option>
                                      <option value="1">{{ __('admin.In Progress') }}</option>
                                      <option value="2">{{ __('admin.Delivered') }}</option>
                                      <option value="3">{{ __('admin.Completed') }}</option>
                                      <option value="4">{{ __('admin.Declined') }}</option>
                                    </select>
                                  </div>
                            </div>
                            <button type="submit" class="btn btn-success">{{__('admin.Search Order')}}</button>
                        </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
