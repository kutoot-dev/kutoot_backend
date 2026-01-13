@php
    $setting = App\Models\Setting::first();
@endphp

<div class="main-sidebar">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="{{ route('seller.dashboard') }}">{{ $setting->sidebar_lg_header }}</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ route('seller.dashboard') }}">{{ $setting->sidebar_sm_header }}</a>
      </div>
      <ul class="sidebar-menu">
          <li class="{{ Route::is('seller.dashboard') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.dashboard') }}"><i class="fas fa-home"></i> <span>{{__('admin.Dashboard')}}</span></a></li>

          <li class="nav-item dropdown {{ Route::is('seller.all-order') || Route::is('seller.order-show') || Route::is('seller.pending-order') || Route::is('seller.Progress-order') || Route::is('seller.delivered-order') ||  Route::is('seller.completed-order') || Route::is('seller.declined-order') || Route::is('seller.cash-on-delivery')  ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-shopping-cart"></i><span>{{__('admin.Orders')}}</span></a>

            <ul class="dropdown-menu">

              <li class="{{ Route::is('seller.all-order') || Route::is('seller.order-show') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.all-order') }}">{{__('admin.All Orders')}}</a></li>

              <li class="{{ Route::is('seller.pending-order') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.pending-order') }}">{{__('admin.Pending Orders')}}</a></li>

              <li class="{{ Route::is('seller.progress-order') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.progress-order') }}">{{__('admin.Progress Orders')}}</a></li>
              <li class="{{ Route::is('seller.delivered-order') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.delivered-order') }}">{{__('admin.Delivered Orders')}}</a></li>
              <li class="{{ Route::is('seller.completed-order') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.completed-order') }}">{{__('admin.Completed Orders')}}</a></li>

              <li class="{{ Route::is('seller.declined-order') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.declined-order') }}">{{__('admin.Declined Orders')}}</a></li>
              <li class="{{ Route::is('seller.cash-on-delivery') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.cash-on-delivery') }}">{{__('admin.Cash On Delivery')}}</a></li>
            </ul>
          </li>


          <li class="nav-item dropdown {{ Route::is('seller.product.*') || Route::is('seller.product-brand.*') || Route::is('seller.brand.*') || Route::is('seller.product-variant') || Route::is('seller.create-product-variant') || Route::is('seller.edit-product-variant') || Route::is('seller.product-gallery') || Route::is('seller.product-variant-item') || Route::is('seller.create-product-variant-item') || Route::is('seller.edit-product-variant-item') || Route::is('seller.product-review') || Route::is('seller.wholesale') || Route::is('seller.create-wholesale') || Route::is('seller.edit-wholesale') || Route::is('seller.pending-product') || Route::is('admin.product-highlight') || Route::is('seller.show-product-review')  || Route::is('seller.show-product-report') || Route::is('seller.product-report') ||  Route::is('seller.stockout-product') || Route::is('seller.product-import-page') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-th-large"></i><span>{{__('admin.Manage Products')}}</span></a>

            <ul class="dropdown-menu">

            <li class="{{ Route::is('seller.product-import-page') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.product-import-page') }}">{{__('admin.Product Bulk Import')}}</a></li>

            <li class="{{ Route::is('seller.product.create') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.product.create') }}">{{__('admin.Product Create')}}</a></li>

            <li class="{{ Route::is('seller.product.index') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.product.index') }}">{{__('admin.Products')}}</a></li>

            <li class="{{ Route::is('seller.pending-product') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.pending-product') }}">{{__('admin.Awaiting for approval')}}</a></li>

            <li class="{{ Route::is('seller.stockout-product') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.stockout-product') }}">{{__('admin.Stock out')}}</a></li>

            <li class="{{ Route::is('seller.brand.index') || Route::is('seller.brand.create') || Route::is('seller.brand.edit') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.brand.index') }}">{{__('admin.Brands')}}</a></li>

            <li class="{{ Route::is('seller.product-review') || Route::is('seller.show-product-review') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.product-review') }}">{{__('admin.Product Reviews')}}</a></li>


            <li class="{{ Route::is('seller.product-report') || Route::is('seller.show-product-report') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.product-report') }}">{{__('admin.Product Report')}}</a></li>

            </ul>
          </li>

          <li class="{{ Route::is('seller.inventory') || Route::is('seller.stock-history') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.inventory') }}"><i class="fas fa-th-large"></i> <span>{{__('admin.Inventory')}}</span></a></li>

          <li class="{{ Route::is('seller.my-withdraw.index') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.my-withdraw.index') }}"><i class="far fa-newspaper"></i> <span>{{__('admin.My Withdraw')}}</span></a></li>

           <!-- <li class="{{ Route::is('seller.message') ? 'active' : '' }}"><a class="nav-link" href="{{ route('seller.message') }}"><i class="far fa-newspaper"></i> <span>{{__('Message')}}</span></a></li> -->



          {{-- <li class=""><a class="nav-link" href="{{ route('user.dashboard') }}"><i class="fas fa-user"></i> <span>{{__('admin.Visit User Dashboard')}}</span></a></li> --}}

        </ul>

    </aside>
  </div>
