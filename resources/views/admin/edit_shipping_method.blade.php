@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Shipping')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Edit Shipping')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.shipping.index') }}">{{__('admin.Shipping')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit Shipping')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.shipping.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Shipping')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.shipping.update',$shipping->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title" value="{{ $shipping->title }}">
                                </div>

                                @if ($shipping->is_free == 1)
                                    <div class="form-group col-12">
                                        <label>{{__('admin.Minimum Order Amount')}} <span class="text-danger">*</span></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">{{ $setting->currency_icon }}</span>
                                            <input type="text" class="form-control" name="minimum_order" value="{{ $shipping->minimum_order }}">
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group col-12">
                                        <label>{{__('admin.Shipping Coast')}} <span class="text-danger">*</span></label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">{{ $setting->currency_icon }}</span>
                                            <input type="text" class="form-control" name="shipping_coast" value="{{ $shipping->fee }}">
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control text-area-5" id="" cols="30" rows="10">{{ $shipping->description }}</textarea>
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
