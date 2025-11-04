@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Subscription Banner')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Subscription Banner')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Subscription Banner')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.update-subscription-banner') }}" enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="">{{__('admin.Existing Banner')}}</label>
                                <div>
                                    <img width="250px" src="{{ asset($subscription_banner->image) }}" alt="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">{{__('admin.New Banner')}}</label>
                                <input type="file" name="image" class="form-control-file">
                            </div>

                            <div class="form-group">
                                <label for="">{{__('admin.Title')}}</label>
                                <input type="text" name="title" class="form-control" value="{{ $subscription_banner->title }}">
                            </div>

                            <div class="form-group">
                                <label for="">{{__('admin.Header')}}</label>
                                <input type="text" name="header" class="form-control" value="{{ $subscription_banner->header }}">
                            </div>

                            <button type="submit" class="btn btn-primary">{{__('admin.Update')}}</button>
                        </form>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

@endsection
