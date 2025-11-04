@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Blog')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Edit Blog')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.blog.index') }}">{{__('admin.Blog')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit Blog')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.blog.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Blog')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.blog.update',$blog->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Thumbnail Image Preview')}}</label>
                                    <div>
                                        <img id="preview-img" class="admin-img" src="{{ asset($blog->image) }}" alt="">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.New Thumbnail Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file"  name="image" onchange="previewThumnailImage(event)">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title" value="{{ $blog->title }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Slug')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="slug" class="form-control"  name="slug" value="{{ $blog->slug }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control select2" id="category">
                                        <option value="">{{__('admin.Select Category')}}</option>
                                        @foreach ($categories as $category)
                                            <option {{ $category->id == $blog->blog_category_id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}} <span class="text-danger">*</span></label>
                                    <textarea name="description" id="" cols="30" rows="10" class="summernote">{{ $blog->description }}</textarea>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Show Homepage ?')}}  <span class="text-danger">*</span></label>
                                    <select name="show_homepage" class="form-control">
                                        <option {{ $blog->show_homepage == 0 ? 'selected' : '' }} value="0">{{__('admin.No')}}</option>
                                        <option {{ $blog->show_homepage == 1 ? 'selected' : '' }} value="1">{{__('admin.Yes')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option {{ $blog->status == 1 ? 'selected' : '' }} value="1">{{__('admin.Active')}}</option>
                                        <option {{ $blog->status == 0 ? 'selected' : '' }} value="0">{{__('admin.Inactive')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.SEO Title')}}</label>
                                   <input type="text" class="form-control" name="seo_title" value="{{ $blog->seo_title }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.SEO Description')}}</label>
                                    <textarea name="seo_description" id="" cols="30" rows="10" class="form-control text-area-5">{{ $blog->seo_description }}</textarea>
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

        reader.readAsDataURL(event.target.files[0]);
    };

    function previewBannerImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    };

</script>
@endsection
