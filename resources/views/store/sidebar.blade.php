@php
    $setting = App\Models\Setting::first();
@endphp

<div class="main-sidebar">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="{{ route('store.dashboard') }}">{{ $setting->sidebar_lg_header }}</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ route('store.dashboard') }}">{{ $setting->sidebar_sm_header }}</a>
      </div>
      <ul class="sidebar-menu">
          <li class="menu-header">Menu</li>
          
          <li class="{{ Route::is('store.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('store.dashboard') }}">
              <i class="fas fa-chart-pie"></i> <span>{{__('admin.Dashboard')}}</span>
            </a>
          </li>

          <li class="{{ Route::is('store.store-profile') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('store.store-profile') }}">
              <i class="fas fa-store"></i> <span>Store Profile</span>
            </a>
          </li>

          <li class="{{ Route::is('store.visitors') || Route::is('store.users.ledger') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('store.visitors') }}">
              <i class="fas fa-users"></i> <span>Visitors</span>
            </a>
          </li>

          <li class="menu-header">Account</li>

          <li class="nav-item dropdown {{ \Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'store.settings.') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown">
              <i class="fas fa-cog"></i><span>Settings</span>
            </a>
            <ul class="dropdown-menu">
              <li class="{{ Route::is('store.settings.change-password') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('store.settings.change-password') }}">
                  <i class="fas fa-key"></i> Change Password
                </a>
              </li>
              <li class="{{ Route::is('store.settings.bank') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('store.settings.bank') }}">
                  <i class="fas fa-university"></i> Bank Details
                </a>
              </li>
              <li class="{{ Route::is('store.settings.notifications') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('store.settings.notifications') }}">
                  <i class="fas fa-bell"></i> Notifications
                </a>
              </li>
            </ul>
          </li>
      </ul>

    </aside>
  </div>


