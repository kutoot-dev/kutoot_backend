@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Banner Image')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Banner Image')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Banner Image')}}</div>
            </div>
          </div>

          <div class="section-body">

            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="30%">{{__('admin.Location')}}</th>
                                    <th width="30%">{{__('admin.Image')}}</th>
                                    <th width="30%">{{__('admin.New Image')}}</th>
                                    <th width="10%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($images as $index => $image)
                                    <tr>
                                        <td>{{ $image->location }}</td>
                                        <td><img src="{{ asset($image->image) }}" width="200px" class="m-2" alt=""></td>
                                        <form action="{{ route('admin.banner-image.update',$image->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                        <td>
                                            <input type="file" class="form-control-file" name="image" required>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary" type="submit">{{__('admin.Update')}}</button>
                                        </td>
                                    </form>
                                    </tr>
                                  @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
