@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Edit Sponsor')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Edit Sponsor')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item"><a href="{{ route('admin.sponsor.index') }}">{{__('admin.Sponsors')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.sponsor.index') }}" class="btn btn-primary"><i class="fas fa-backward"></i> {{__('admin.Go Back')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.sponsor.update', $sponsor->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Sponsor Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $sponsor->name) }}" required>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Sponsor Type')}} <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="Sponsor" {{ old('type', $sponsor->type) == 'Sponsor' ? 'selected' : '' }}>Sponsor</option>
                                        <option value="Co-Sponsor" {{ old('type', $sponsor->type) == 'Co-Sponsor' ? 'selected' : '' }}>Co-Sponsor</option>
                                        <option value="Special Sponsor" {{ old('type', $sponsor->type) == 'Special Sponsor' ? 'selected' : '' }}>Special Sponsor</option>
                                        <option value="Partner" {{ old('type', $sponsor->type) == 'Partner' ? 'selected' : '' }}>Partner</option>
                                    </select>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Link URL')}}</label>
                                    <input type="url" name="link" class="form-control" value="{{ old('link', $sponsor->link) }}" placeholder="https://example.com">
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Serial')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="serial" class="form-control" value="{{ old('serial', $sponsor->serial) }}" min="0" required>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Logo Image')}}</label>
                                    @if($sponsor->logo)
                                        <div class="mb-2">
                                            <img src="{{ asset($sponsor->logo) }}" alt="Current Logo" style="max-width: 150px;">
                                            <p class="text-muted small">{{__('admin.Current logo')}}</p>
                                        </div>
                                    @endif
                                    <input type="file" name="logo" class="form-control-file" accept="image/*" onchange="previewImage(this, 'logoPreview')">
                                    <small class="text-muted">Leave empty to keep current. Max 5MB.</small>
                                    <div class="mt-2">
                                        <img id="logoPreview" src="" style="max-width: 200px; display: none;">
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Banner Image')}}</label>
                                    @if($sponsor->banner)
                                        <div class="mb-2">
                                            <img src="{{ asset($sponsor->banner) }}" alt="Current Banner" style="max-width: 250px;">
                                            <p class="text-muted small">{{__('admin.Current banner')}}</p>
                                        </div>
                                    @endif
                                    <input type="file" name="banner" class="form-control-file" accept="image/*" onchange="previewImage(this, 'bannerPreview')">
                                    <small class="text-muted">Leave empty to keep current. Max 10MB.</small>
                                    <div class="mt-2">
                                        <img id="bannerPreview" src="" style="max-width: 300px; display: none;">
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ $sponsor->status == 1 ? 'selected' : '' }}>{{__('admin.Active')}}</option>
                                        <option value="0" {{ $sponsor->status == 0 ? 'selected' : '' }}>{{__('admin.Inactive')}}</option>
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
