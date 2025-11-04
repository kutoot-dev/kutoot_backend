@extends('admin.master_layout')
@section('title')
<title>{{__('admin.FAQ')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create FAQ')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.faq.index') }}">{{__('admin.FAQ')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create FAQ')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.faq.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.FAQ')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.faq.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Question')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="question" class="form-control"  name="question" value="{{ old('question') }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Answer')}} <span class="text-danger">*</span></label>
                                    <textarea name="answer" id="" cols="30" rows="10" class="summernote">{{ old('answer') }}</textarea>
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
