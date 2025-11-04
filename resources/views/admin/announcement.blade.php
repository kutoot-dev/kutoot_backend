@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Announcement')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Announcement')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Announcement')}}</div>
            </div>
          </div>

          <div class="section-body">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.announcement-update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="">{{__('admin.Announcement Status')}}</label>
                                <div>
                                    @if ($announcement->status == 1)
                                        <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                        @else
                                        <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">{{__('admin.Existing Image')}}</label>
                                <div>
                                    <img src="{{ asset($announcement->image) }}" width="200px" alt="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">{{__('admin.New Image')}}</label>
                                <input type="file" class="form-control-file" name="image">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('admin.Title')}}</label>
                                <input type="text" class="form-control" name="title" value="{{ $announcement->title }}">
                            </div>

                            <div class="form-group">
                                <label for="">{{__('admin.Description')}}</label>
                                <textarea name="description" id="" cols="30" rows="10" class="form-control text-area-5">{{ $announcement->description }}</textarea>
                            </div>


                            <div class="form-group">
                                <label for="">{{__(' Expired Date')}}</label>
                                <input type="date" class="form-control" name="expired_date" value="{{ $announcement->expired_date }}">
                            </div>



                            <button class="btn btn-primary" type="submit">{{__('admin.Update')}}</button>
                        </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
