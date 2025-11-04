@extends('user.layout')
@section('title')
    <title>{{__('Address')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
      <div class="dashboard_content">
        <h3><i class="fal fa-gift-card"></i> {{__('Address')}}</h3>
        <div class="wsus__dashboard_add">
          <div class="row">
            <div class="col-xl-6">
              <div class="wsus__dash_add_single">
                <h4>{{__('Billing Address')}}</h4>
                <ul>
                  <li><span>{{__('name')}} :</span> {{ $billing ? $billing->name : '' }}</li>
                  <li><span>{{__('Phone')}} :</span> {{ $billing ? $billing->phone : '' }}</li>
                  <li><span>{{__('email')}} :</span> {{ $billing ? $billing->email : '' }}</li>
                  <li><span>{{__('country')}} :</span> @if ($billing)
                    {{ $billing->country ? $billing->country->name : '' }}
                  @endif </li>
                  <li><span>{{__('State')}} :</span> @if ($billing)
                    {{ $billing->countryState ? $billing->countryState->name : '' }}
                  @endif</li>
                  <li><span>{{__('city')}} :</span> @if ($billing)
                    {{ $billing->city ? $billing->city->name : '' }}
                  @endif</li>
                  <li><span>{{__('zip code')}} :</span> {{ $billing ? $billing->zip_code : '' }}</li>
                  <li><span>{{__('address')}} :</span> {{ $billing ? $billing->address : '' }}</li>
                </ul>
                <div class="wsus__address_btn">
                  <a href="{{ route('user.billing-address') }}" class="edit"><i class="fal fa-edit"></i> {{__('edit')}}</a>
                </div>
              </div>
            </div>
            <div class="col-xl-6">
              <div class="wsus__dash_add_single">
                <h4>{{__('Shipping Address')}} </h4>
                <ul>
                    <li><span>{{__('name')}} :</span> {{ $shipping ? $shipping->name : '' }}</li>
                    <li><span>{{__('Phone')}} :</span> {{ $shipping ? $shipping->phone : '' }}</li>
                    <li><span>{{__('email')}} :</span> {{ $shipping ? $shipping->email : '' }}</li>
                    <li><span>{{__('country')}} :</span> @if ($shipping)
                      {{ $shipping->country ? $shipping->country->name : '' }}
                    @endif </li>
                    <li><span>{{__('State')}} :</span> @if ($shipping)
                      {{ $shipping->countryState ? $shipping->countryState->name : '' }}
                    @endif</li>
                    <li><span>{{__('city')}} :</span> @if ($shipping)
                      {{ $shipping->city ? $shipping->city->name : '' }}
                    @endif</li>
                    <li><span>{{__('zip code')}} :</span> {{ $shipping ? $shipping->zip_code : '' }}</li>
                    <li><span>{{__('address')}} :</span> {{ $shipping ? $shipping->address : '' }}</li>
                  </ul>
                <div class="wsus__address_btn">
                  <a href="{{ route('user.shipping-address') }}" class="edit"><i class="fal fa-edit"></i> {{__('edit')}}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
