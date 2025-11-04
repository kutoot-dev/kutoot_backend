@extends('user.layout')
@section('title')
    <title>{{__('Dashboard')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
      <div class="dashboard_content">
        <div class="wsus__dashboard">
          <div class="row">
            <div class="col-lg-4 col-6 col-md-4">
                <a class="wsus__dashboard_item blue" href="{{ route('user.order') }}">
                  <i class="fas fa-shopping-cart"></i>
                  <p>{{__('Total Order')}}</p>
                  <span class="text-white">{{ $orders->count() }}</span>
                </a>
              </div>
            <div class="col-lg-4 col-6 col-md-4">
                <a class="wsus__dashboard_item purple" href="{{ route('user.complete-order') }}">
                  <i class="fas fa-shopping-cart"></i>
                  <p>{{__('Complete Order')}}</p>
                  <span class="text-white">{{ $orders->where('order_status',3)->count() }}</span>
                </a>
              </div>

            <div class="col-lg-4 col-6 col-md-4">
              <a class="wsus__dashboard_item green" href="{{ route('user.pending-order') }}">
                <i class="fas fa-shopping-cart"></i>
                <p>{{__('Pending Order')}}</p>
                <span class="text-white">{{ $orders->where('order_status',0)->count() }}</span>
              </a>
            </div>

            <div class="col-lg-4 col-6 col-md-4">
                <a class="wsus__dashboard_item red" href="{{ route('user.declined-order') }}">
                  <i class="fas fa-shopping-cart"></i>
                  <p>{{__('Declined Order')}}</p>
                  <span class="text-white">{{ $orders->where('order_status',4)->count() }}</span>
                </a>
              </div>

            <div class="col-lg-4 col-6 col-md-4">
              <a class="wsus__dashboard_item sky" href="{{ route('user.review') }}">
                <i class="fas fa-star"></i>
                <p>{{__('Review')}}</p>
                <span class="text-white">{{ $reviews->count() }}</span>
              </a>
            </div>
            <div class="col-lg-4 col-6 col-md-4">
              <a class="wsus__dashboard_item blue" href="{{ route('user.wishlist') }}">
                <i class="far fa-heart"></i>
                <p>{{__('Wishlist')}}</p>
                <span class="text-white">{{ $wishlists->count() }}</span>
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
