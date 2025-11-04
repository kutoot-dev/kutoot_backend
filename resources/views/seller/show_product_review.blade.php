@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Product Review')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Review')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('seller.product.index') }}">{{__('admin.Product')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Product Review')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.product-review') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Product Review')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped table-bordered">
                           <tr>
                               <td>{{__('admin.User Name')}}</td>
                               <td>{{ $review->user->name }}</td>
                           </tr>
                           <tr>
                               <td>{{__('admin.User Email')}}</td>
                               <td>{{ $review->user->email }}</td>
                           </tr>
                           <tr>
                               <td>{{__('admin.Product')}}</td>
                               <td><a href="{{ route('seller.product.edit', $review->product->id) }}">{{ $review->product->name }}</a></td>
                           </tr>
                           <tr>
                               <td>{{__('admin.Rating')}}</td>
                               <td>{{ $review->rating }}</td>
                           </tr>
                           <tr>
                               <td>{{__('admin.Review')}}</td>
                               <td>{{ $review->review }}</td>
                           </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

@endsection
