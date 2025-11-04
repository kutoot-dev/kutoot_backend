@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Edit Currency')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Edit Currency')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit Currency')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.currency.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Currency')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.currency.update', $multiCurrency->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Currency Name')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="currency_name" value="{{ $multiCurrency->currency_name }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Country Code')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="country_code" value="{{ $multiCurrency->country_code  }}">
                                </div>
                                
                                <div class="form-group col-12">
                                    <label>{{__('admin.Currency Code')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="currency_code" value="{{ $multiCurrency->currency_code }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Currency Icon')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="currency_icon" value="{{ $multiCurrency->currency_icon }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Currency Rate')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="currency_rate" value="{{ $multiCurrency->currency_rate }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Default')}} <span class="text-danger">*</span></label>
                                    <select name="is_default" class="form-control">
                                        <option value="No" {{ $multiCurrency->is_default == 'No' ? 'selected':'' }}>{{__('admin.No')}}</option>
                                        <option value="Yes" {{ $multiCurrency->is_default == 'Yes' ? 'selected':'' }}>{{__('admin.Yes')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Currency Position')}} <span class="text-danger">*</span></label>
                                    <select name="currency_position" class="form-control">
                                        <option value="left" {{ $multiCurrency->currency_position == 'left' ? 'selected':'' }}>{{__('admin.Before Price')}}</option>
                                        <option value="right" {{ $multiCurrency->currency_position == 'right' ? 'selected':'' }}>{{__('admin.After Price')}}</option>
                                    </select>
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ $multiCurrency->status == 1 ? 'selected':'' }}>{{__('admin.Active')}}</option>
                                        <option value="0" {{ $multiCurrency->status == 0 ? 'selected':'' }}>{{__('admin.Inactive')}}</option>
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
