@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Privacy Policy')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Privacy Policy')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Privacy Policy')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        @if ($isPrivacyPolicy)
                            <form action="{{ route('admin.privacy-policy.update',$privacyPolicy->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">

                                    <div class="form-group col-12">
                                        <label>{{__('admin.Privacy Policy')}}<span class="text-danger">*</span></label>
                                        <textarea name="privacy_policy" cols="30" rows="10" class="summernote">{{ $privacyPolicy->privacy_policy }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form action="{{ route('admin.privacy-policy.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                    <div class="form-group col-12">
                                        <label>{{__('admin.Privacy Policy')}}<span class="text-danger">*</span></label>
                                        <textarea name="privacy_policy" cols="30" rows="10" class="summernote"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">{{__('admin.Save')}}</button>
                                    </div>
                                </div>
                            </form>
                        @endif

                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>


<script>
    function previewThumnailImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    };
</script>

@endsection
