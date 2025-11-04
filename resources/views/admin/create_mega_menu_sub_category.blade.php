@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Mega Menu Sub Category')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Create Mega Menu Sub Category')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Create Mega Menu Sub Category')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.mega-menu-sub-category',$megaMenuCategory->id) }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Mega Menu Sub Category')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                      <div class="card-header">
                          <h1>{{__('admin.Category')}} : {{ $megaMenuCategory->category->name }}</h1>
                      </div>
                    <div class="card-body">
                        <form action="{{ route('admin.store-mega-menu-sub-category',$megaMenuCategory->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Sub Category')}} <span class="text-danger">*</span></label>
                                    <select name="sub_category" id="" class="form-control select2">
                                        <option value="">{{__('admin.Select Sub Category')}}</option>
                                        @foreach ($subCategories as $subCategory)
                                            <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Serial')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="serial" class="form-control">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
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
