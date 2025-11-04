@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Message')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Message')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Message')}}</div>
            </div>
          </div>
          <div class="section-body" id="app" data={{ $auth_user->id }}>
                <MainApp />
          </div>
        </section>
      </div>
@endsection
