@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Brand')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Edit Product Brand')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.product-brand.index') }}">{{__('admin.Product Brand')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit Product Brand')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.product-brand.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Product Brand')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.product-brand.update',$brand->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="form-group col-12">
                                    <label>{{__('admin.Existing Logo')}}</label>
                                    <div>
                                        <img src="{{ asset($brand->logo) }}" width="100px" alt="">
                                    </div>
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Logo')}}</label>
                                    <input type="file" class="form-control-file"  name="logo">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name" value="{{ $brand->name }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Slug')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="slug" class="form-control"  name="slug" value="{{ $brand->slug }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option {{ $brand->status==1 ? 'selected': '' }} value="1">{{__('admin.Active')}}</option>
                                        <option {{ $brand->status==0 ? 'selected': '' }}  value="0">{{__('admin.Inactive')}}</option>
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

<script>
    (function($) {
        "use strict";
        $(document).ready(function () {
            $("#name").on("focusout",function(e){
                $("#slug").val(convertToSlug($(this).val()));
            })
        });
    })(jQuery);

    function convertToSlug(Text)
        {
            return Text
                .toLowerCase()
                .replace(/[^\w ]+/g,'')
                .replace(/ +/g,'-');
        }
</script>
@endsection
