@extends('admin.master_layout')
@section('title')
<title>{{__('Delivery Man')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Add New Delivery Man
                ')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.blog.index') }}">{{__('Delivery Man')}}</a></div>
              <div class="breadcrumb-item">{{__('Create')}}</div>
            </div>
          </div>

        <div class="section-body">
            <a href="{{ route('admin.delivery-man.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('Delivery Man')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                    <form action="{{ route('admin.delivery-man.store') }}" method="POST" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-body">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>{{__('Delivery Man Image')}} <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control-file" name="man_image">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('First Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" id="fname" class="form-control"  name="fname" value="{{ old('fname') }}">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Last Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" id="lname" class="form-control"  name="lname" value="{{ old('lname') }}">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Email')}} <span class="text-danger">*</span></label>
                                        <input type="text" id="email" class="form-control"  name="email" value="{{ old('email') }}">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Delivery Man Type')}} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="man_type" id="man_type">
                                            <option value="freelancer">Freelancer</option>
                                            <option value="salary based">Salary based</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Identity Type')}} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="idn_type" id="idn_type">
                                            <option value="passport">Passport</option>
                                            <option value="driving license">Driving license</option>
                                            <option value="nid">Nid</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Identity Number')}} <span class="text-danger">*</span></label>
                                        <input type="text" id="idn_num" class="form-control"  name="idn_num" value="{{ old('idn_num') }}">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Identity Image')}} <span class="text-danger">*</span></label>
                                        <input type="file" id="idn_image" class="form-control-file"  name="idn_image">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Phone')}} <span class="text-danger">*</span></label>
                                        <input type="text" id="phone" class="form-control"  name="phone" value="{{ old('phone') }}">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Password')}} <span class="text-danger">*</span></label>
                                        <input type="text" id="password" class="form-control"  name="password" value="{{ old('password') }}">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{__('Confirm Password')}} <span class="text-danger">*</span></label>
                                        <input type="text" id="c_password" class="form-control"  name="c_password" value="{{ old('c_password') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">{{__('admin.Save')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

<script>
    (function($) {
        "use strict";
        $(document).ready(function () {
            $("#title").on("focusout",function(e){
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

    function previewThumnailImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
        }

        reader.readAsDataURL(event.target.files[0]);
    };

    function previewBannerImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-banner-img');
            output.src = reader.result;
        }

        reader.readAsDataURL(event.target.files[0]);
    };

</script>
@endsection
