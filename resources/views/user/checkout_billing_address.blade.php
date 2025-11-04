@extends('layout')
@section('title')
    <title>{{__('Billing Address')}}</title>
@endsection
@section('meta')
    <meta name="description" content="{{__('Billing Address')}}">
@endsection

@section('public-content')


    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{  asset($banner->image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('Billing Address')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('user.checkout.billing-address') }}">{{__('Billing Address')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BREADCRUMB END
    ==============================-->


    <!--============================
          CHECK OUT PAGE START
    ==============================-->
    <section id="wsus__cart_view">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="wsus__cart_tab">
                        <li><a href="{{ route('cart') }}">{{__('Shopping Cart')}}</a></li>
                        <li><a  class="wsus__order_active" href="{{ route('user.checkout.billing-address') }}">{{__('Billing Address')}}</a></li>
                        <li><a  href="javascript:;">{{__('Shipping Address')}}</a></li>
                        <li><a href="javascript:;">{{__('payment')}}</a></li>
                    </ul>
                </div>
                <form class="wsus__checkout_form" method="POST" action="{{ route('user.checkout.update-billing-address') }}">
                    @csrf
                    <div class="row">
                        <div class="col-xl-7 col-lg-6">
                            <div class="wsus__check_form">
                                <h5>{{__('Billing Address')}}</h5>
                                @if ($billing)
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="wsus__add_address_single">
                                                <input type="text" placeholder="{{__('Name')}}*" name="name" value="{{ $billing->name }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="wsus__add_address_single">
                                                <input type="email" placeholder="{{__('Email')}}*" name="email" value="{{ $billing->email }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="wsus__add_address_single">
                                                <input type="text" placeholder="{{__('Phone')}}*" name="phone" value="{{ $billing->phone }}">
                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="wsus__check_single_form">
                                                <select class="select_2" name="country" id="country_id">
                                                    <option value="">{{__('Select Country')}}*</option>
                                                    @foreach ($countries as $country)
                                                        <option {{ $country->id == $billing->country_id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="wsus__check_single_form">
                                                <select class="select_2" name="state" id="state_id">
                                                    <option value="0">{{__('Select State')}}</option>
                                                    @foreach ($states as $state)
                                                        <option {{ $state->id == $billing->state_id ? 'selected' : '' }} value="{{ $state->id }}">{{ $state->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="wsus__check_single_form">
                                                <select class="select_2" name="city" id="city_id">
                                                    <option value="0">{{__('Select City')}}</option>
                                                    @foreach ($cities as $city)
                                                        <option {{ $city->id == $billing->city_id ? 'selected' : '' }} value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="wsus__add_address_single">
                                                <input type="text" placeholder="{{__('Zip Code')}}" name="zip_code" value="{{ $billing->zip_code }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="wsus__add_address_single">
                                                <input type="text" name="address" placeholder="{{__('Address')}}*" value="{{ $billing->address }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <input type="checkbox" name="same_as_shipping" id="same_as_shipping"> <label for="same_as_shipping">{{__('Same as Shipping Address')}}</label>
                                        </div>

                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-xl-6 col-md-6">
                                            <div class="wsus__add_address_single">
                                                <input type="text" placeholder="{{__('Name')}}*" name="name" >
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6">
                                            <div class="wsus__add_address_single">
                                                <input type="email" placeholder="{{__('Email')}}*" name="email">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6">
                                            <div class="wsus__add_address_single">
                                                <input type="text" placeholder="{{__('Phone')}}*" name="phone">
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-md-6">
                                            <div class="wsus__check_single_form">
                                                <select class="select_2" name="country" id="country_id">
                                                    <option value="">{{__('Select Country')}}*</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6">
                                            <div class="wsus__check_single_form">
                                                <select class="select_2" name="state" id="state_id">
                                                    <option value="0">{{__('Select State')}}</option>
                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6">
                                            <div class="wsus__check_single_form">
                                                <select class="select_2" name="city" id="city_id">
                                                    <option value="0">{{__('Select City')}}</option>
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-md-6">
                                            <div class="wsus__add_address_single">
                                                <input type="text" placeholder="{{__('Zip Code')}}" name="zip_code">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-6">
                                            <div class="wsus__add_address_single">
                                                <input type="text" name="address" placeholder="{{__('Address')}}*">
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <input type="checkbox" name="same_as_shipping" id="same_as_shipping"> <label for="same_as_shipping">{{__('Same as Shipping Address')}}</label>
                                        </div>

                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-6">
                            <div class="wsus__order_details" id="sticky_sidebar">
                                <h5>{{__('products')}}</h5>
                                <ul class="wsus__order_details_item">
                                    @php
                                        $subTotal = 0;
                                    @endphp
                                    @foreach ($cartContents as $cartContent)
                                        @php
                                            $variantPrice = 0;
                                        @endphp
                                        <li>
                                            <div class="wsus__order_details_img">
                                                <img src="{{ asset($cartContent->options->image) }}" alt="blazer" class="img-fluid w-100">
                                                <span>{{ $cartContent->qty }}</span>
                                            </div>
                                            <div class="wsus__order_details_text">
                                                <p>{{ $cartContent->name }}</p>
                                                <span>
                                                    @php
                                                        $totalVariant = count($cartContent->options->variants);
                                                    @endphp
                                                    @foreach ($cartContent->options->variants as $indx => $variant)
                                                        @php
                                                            $variantPrice += $cartContent->options->prices[$indx];
                                                        @endphp
                                                        {{ $variant }}: {{ $cartContent->options->values[$indx] }}{{ $totalVariant == ++$indx ? '' : ',' }}
                                                    @endforeach

                                                </span>
                                            </div>

                                            @php
                                                $productPrice = $cartContent->price ;
                                                $total = $productPrice * $cartContent->qty ;
                                                $subTotal += $total;
                                            @endphp
                                            <div class="wsus__order_details_tk">
                                                <p>{{ $setting->currency_icon }}{{ $total }}</p>
                                            </div>
                                        </li>
                                        @php
                                            $totalVariant = 0;
                                        @endphp
                                    @endforeach
                                </ul>

                                @php
                                    $tax_amount = 0;
                                    $total_price = 0;
                                    $coupon_price = 0;
                                    foreach ($cartContents as $key => $content) {
                                        $tax = $content->options->tax * $content->qty;
                                        $tax_amount = $tax_amount + $tax;
                                    }

                                    $total_price = $tax_amount + $subTotal;

                                    if(Session::get('coupon_price') && Session::get('offer_type')) {
                                        if(Session::get('offer_type') == 1) {
                                            $coupon_price = Session::get('coupon_price');
                                            $coupon_price = ($coupon_price / 100) * $total_price;
                                        }else {
                                            $coupon_price = Session::get('coupon_price');
                                        }
                                    }

                                    $total_price = $total_price - $coupon_price ;
                                @endphp

                                <div class="wsus__order_details_summery">
                                    <p>{{__('subtotal')}}: <span>{{ $setting->currency_icon }}{{ $subTotal }}</span></p>
                                    <p>{{__('Tax')}}(+): <span>{{ $setting->currency_icon }}{{ $tax_amount }}</span></p>
                                    <p>{{__('Coupon')}}(-): <span>{{ $setting->currency_icon }}{{  $coupon_price  }}</span></p>
                                    <p class="total"><span>{{__('total')}}:</span> <span>{{ $setting->currency_icon }}{{ $total_price }}</span></p>
                                </div>
                                <button type="submit" class="common_btn">{{__('Continue Shopping')}}</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </section>
    <!--============================
         CHECK OUT PAGE END
    ==============================-->


    <script>
        (function($) {
            "use strict";
            $(document).ready(function () {

                $("#country_id").on("change",function(){
                    var countryId = $("#country_id").val();
                    if(countryId){
                        $.ajax({
                            type:"get",
                            url:"{{url('/user/state-by-country/')}}"+"/"+countryId,
                            success:function(response){
                                $("#state_id").html(response.states);
                                var response= "<option value=''>{{__('Select a City')}}</option>";
                                $("#city_id").html(response);
                            },
                            error:function(err){
                            }
                        })
                    }else{
                        var response= "<option value=''>{{__('Select a State')}}</option>";
                        $("#state_id").html(response);
                        var response= "<option value=''>{{__('Select a City')}}</option>";
                        $("#city_id").html(response);
                    }

                })

                $("#state_id").on("change",function(){
                    var countryId = $("#state_id").val();
                    if(countryId){
                        $.ajax({
                            type:"get",
                            url:"{{url('/user/city-by-state/')}}"+"/"+countryId,
                            success:function(response){
                                $("#city_id").html(response.cities);
                            },
                            error:function(err){
                            }
                        })
                    }else{
                        var response= "<option value=''>{{__('Select a City')}}</option>";
                        $("#city_id").html(response);
                    }

                })
            });
        })(jQuery);
    </script>
@endsection
