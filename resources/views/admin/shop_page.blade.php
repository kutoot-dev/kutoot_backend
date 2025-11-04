@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Shop Page')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Shop Page')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Shop Page')}}</div>
            </div>
          </div>
          <div class="section-body">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.update-filter-price') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="">{{__('admin.Filter Maximum price range')}}</label>
                                    <input type="text" name="filter_price_range" value="{{ $shop_page->filter_price_range }}" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">{{__('admin.Save')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
      </div>
@endsection
