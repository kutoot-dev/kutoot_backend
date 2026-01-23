@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Create Sponsor')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Sponsor')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin.sponsor.index') }}">{{__('admin.Sponsors')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.sponsor.index') }}" class="btn btn-primary"><i class="fas fa-backward"></i> {{__('admin.Go Back')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.sponsor.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Sponsor Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Sponsor Type')}} <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="Sponsor" {{ old('type') == 'Sponsor' ? 'selected' : '' }}>Sponsor</option>
                                        <option value="Co-Sponsor" {{ old('type') == 'Co-Sponsor' ? 'selected' : '' }}>Co-Sponsor</option>
                                        <option value="Special Sponsor" {{ old('type') == 'Special Sponsor' ? 'selected' : '' }}>Special Sponsor</option>
                                        <option value="Partner" {{ old('type') == 'Partner' ? 'selected' : '' }}>Partner</option>
                                    </select>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Link URL')}}</label>
                                    <input type="url" name="link" class="form-control" value="{{ old('link') }}" placeholder="https://example.com">
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Serial')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="serial" class="form-control" value="{{ old('serial', 0) }}" min="0" required>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Logo Image')}}</label>
                                    <input type="file" name="logo" class="form-control-file" accept="image/*" onchange="previewImage(this, 'logoPreview')">
                                    <small class="text-muted">Recommended: 300x150px, Max 5MB. Will be optimized & compressed to WebP.</small>
                                    <div class="mt-2">
                                        <img id="logoPreview" src="" style="max-width: 200px; display: none;">
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Banner Image')}}</label>
                                    <input type="file" name="banner" class="form-control-file" accept="image/*" onchange="previewImage(this, 'bannerPreview')">
                                    <small class="text-muted">Recommended: 800x500px, Max 10MB. Will be optimized & compressed to WebP.</small>
                                    <div class="mt-2">
                                        <img id="bannerPreview" src="" style="max-width: 300px; display: none;">
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
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

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                document.getElementById(previewId).style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
