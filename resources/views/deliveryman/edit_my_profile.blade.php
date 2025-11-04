@extends('deliveryman.master_layout')
@section('title')
<title>{{__('Edit Profile')}}</title>
@endsection
@section('deliveryman-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('Edit Profile')}}</h1>
      </div>

      <div class="section-body">
        <div class="row mt-4">
            <div class="col-12">
                <form action="{{ route('deliveryman.update-profile') }}" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                                @csrf
                                @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('Delivery Man Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file" name="man_image">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('First Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="fname" class="form-control"  name="fname" value="{{ $deliveryman->fname }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('Last Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="lname" class="form-control"  name="lname" value="{{ $deliveryman->lname }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('Email')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="email" class="form-control"  name="email" value="{{ $deliveryman->email }}" readonly>
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('Delivery Man Type')}} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="man_type" id="man_type">
                                        <option value="freelancer" {{ $deliveryman->man_type=='freelancer'?'selected':'' }}>Freelancer</option>
                                        <option value="salary based" {{ $deliveryman->man_type=='salary based'?'selected':'' }}>Salary based</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('Identity Type')}} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="idn_type" id="idn_type">
                                        <option value="passport" {{ $deliveryman->idn_type=='passport'?'selected':'' }}>Passport</option>
                                        <option value="driving license" {{ $deliveryman->idn_type=='driving license'?'selected':'' }}>Driving license</option>
                                        <option value="nid" {{ $deliveryman->idn_type=='passport'?'nid':'' }}>Nid</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('Identity Number')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="idn_num" class="form-control"  name="idn_num" value="{{ $deliveryman->idn_num }}">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('Identity Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" id="idn_image" class="form-control-file"  name="idn_image">
                                </div>
                                <div class="form-group col-6">
                                    <label>{{__('Phone')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="phone" class="form-control"  name="phone" value="{{ $deliveryman->phone }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </section>
  </div>

@endsection
