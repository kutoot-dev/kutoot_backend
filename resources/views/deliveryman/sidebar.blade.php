{{-- @php
    $setting = App\Models\Setting::first();
@endphp --}}

<div class="main-sidebar">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="{{ route('deliveryman.dashboard') }}">{{ $setting->sidebar_lg_header }}</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ route('deliveryman.dashboard') }}">{{ $setting->sidebar_sm_header }}</a>
      </div>
      <ul class="sidebar-menu">
          <li class="{{ Route::is('deliveryman.dashboard') ? 'active' : '' }}"><a class="nav-link" href="{{ route('deliveryman.dashboard') }}"><i class="fas fa-home"></i> <span>{{__('admin.Dashboard')}}</span></a></li>

          <li class="nav-item dropdown {{ Route::is('deliveryman.orders') || Route::is('deliveryman.order-show') || Route::is('deliveryman.completed-order') || Route::is('deliveryman.order-request') ||  Route::is('deliveryman.completed-order') || Route::is('deliveryman.message-with-customer') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-shopping-cart"></i><span>{{__('admin.Orders')}}</span></a>

            <ul class="dropdown-menu">

              <li class="{{ Route::is('deliveryman.order-request') || Route::is('seller.order-show') ? 'active' : '' }}"><a class="nav-link" href="{{ route('deliveryman.order-request') }}">{{__('Order Request')}}</a></li>

              <li class="{{ Route::is('deliveryman.orders') || Route::is('seller.order-show') ? 'active' : '' }}"><a class="nav-link" href="{{ route('deliveryman.orders') }}">{{__('Running Orders')}}</a></li>

              <li class="{{ Route::is('deliveryman.completed-order') ? 'active' : '' }}"><a class="nav-link" href="{{ route('deliveryman.completed-order') }}">{{__('admin.Completed Orders')}}</a></li>
            </ul>
          </li>

          <li class="{{ Route::is('deliveryman.withdraw.index') || Route::is('deliveryman.withdraw.create') || Route::is('deliveryman.withdraw.show') ? 'active' : '' }}"><a class="nav-link" href="{{ route('deliveryman.withdraw.index') }}"><i class="far fa-newspaper"></i> <span>{{__('admin.My Withdraw')}}</span></a></li>

          <li class="{{ Route::is('deliveryman.my-review') ? 'active' : '' }}"><a class="nav-link" href="{{ route('deliveryman.my-review') }}"><i class="far fa-newspaper"></i> <span>{{__('My Review')}}</span></a></li>
        </ul>

    </aside>
  </div>
