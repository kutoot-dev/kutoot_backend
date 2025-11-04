@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Flash Sale')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Flash Sale')}}</h1>

          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.update-flash-sale') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Homepage Image Preview')}}</label>
                                    <div>
                                        <img class="admin-img" src="{{ asset($flash_sale->homepage_image) }}" alt="">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Homepage Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file"  name="homepage_image">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Flash Sale Page Image Preview')}}</label>
                                    <div>
                                        <img  class="w_300" src="{{ asset($flash_sale->flashsale_page_image) }}" alt="">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Flash Sale Page Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file"  name="flashsale_page_image">
                                </div>


                                <div class="form-group col-6">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title" value="{{ $flash_sale->title }}">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Offer')}} <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">%</span>
                                        <input type="text" class="form-control" name="offer" value="{{ $flash_sale->offer }}">
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.End Time')}} <span class="text-danger">*</span></label>
                                    <input type="text"  class="form-control datetimepicker_mask"  name="end_time" value="{{ $flash_sale->end_time }}" autocomplete="off">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option {{ $flash_sale->status == 1 ? 'selected' : '' }} value="1">{{__('admin.Active')}}</option>
                                        <option {{ $flash_sale->status == 0 ? 'selected' : '' }} value="0">{{__('admin.Inactive')}}</option>
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
