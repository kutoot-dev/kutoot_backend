@extends('user.layout')
@section('title')
    <title>{{__('Shipping Address')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
      <div class="dashboard_content mt-2 mt-md-0">
        <h3><i class="fal fa-gift-card"></i>{{__('Shipping Address')}}</h3>
        <div class="wsus__dashboard_add wsus__add_address">
          <form action="{{ route('user.update-shipping-address') }}" method="POST">
              @csrf
                <div class="row">
                    @if ($shipping)
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('name')}} <b>*</b></label>
                                <input type="text" placeholder="{{__('Name')}}" name="name" value="{{ $shipping->name }}">
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('email')}} <b>*</b></label>
                                <input type="email" placeholder="{{__('Email')}}" name="email" value="{{ $shipping->email }}">
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('phone')}} <b>*</b></label>
                                <input type="text" placeholder="{{__('Phone')}}" name="phone" value="{{ $shipping->phone }}">
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('Country')}} <b>*</b></label>
                                <div class="wsus__topbar_select">
                                    <select class="select_2" name="country" id="country_id">
                                        <option value="">{{__('Select Country')}}</option>
                                        @foreach ($countries as $country)
                                            <option {{ $country->id == $shipping->country_id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('state')}} <b>*</b></label>
                                <div class="wsus__topbar_select">
                                    <select class="select_2" name="state" id="state_id">
                                        <option value="0">{{__('Select State')}}</option>
                                        @foreach ($states as $state)
                                            <option {{ $state->id == $shipping->state_id ? 'selected' : '' }} value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('city')}} <b>*</b></label>
                                <div class="wsus__topbar_select">
                                    <select class="select_2" name="city" id="city_id">
                                        <option value="0">{{__('Select City')}}</option>
                                        @foreach ($cities as $city)
                                            <option {{ $city->id == $shipping->city_id ? 'selected' : '' }} value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('zip code')}} <b>*</b></label>
                                <input type="text" placeholder="Zip Code" value="{{ $shipping->zip_code }}" name="zip_code">
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('Address')}} <b>*</b></label>
                                <input type="text" name="address" placeholder="{{__('Address')}}" value="{{ $shipping->address }}">
                            </div>
                        </div>
                    @else
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('name')}} <b>*</b></label>
                                <input type="text" placeholder="{{__('Name')}}" name="name" >
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('email')}} <b>*</b></label>
                                <input type="email" placeholder="{{__('Email')}}" name="email">
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('phone')}} <b>*</b></label>
                                <input type="text" placeholder="{{__('Phone')}}" name="phone">
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('Country')}} <b>*</b></label>
                                <div class="wsus__topbar_select">
                                    <select class="select_2" name="country" id="country_id">
                                        <option value="">{{__('Select Country')}}</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('state')}} <b>*</b></label>
                                <div class="wsus__topbar_select">
                                    <select class="select_2" name="state" id="state_id">
                                        <option value="0">{{__('Select State')}}</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('city')}} <b>*</b></label>
                                <div class="wsus__topbar_select">
                                    <select class="select_2" name="city" id="city_id">
                                        <option value="0">{{__('Select City')}}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('zip code')}} <b>*</b></label>
                                <input type="text" placeholder="{{__('zip code')}}" name="zip_code">
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="wsus__add_address_single">
                                <label>{{__('Address')}} <b>*</b></label>
                                <input type="text" name="address" placeholder="{{__('Address')}}">
                            </div>
                        </div>
                    @endif

                    <div class="col-xl-6">
                        <button type="submit" class="common_btn">{{__('Update Address')}}</button>
                    </div>
                </div>
          </form>
        </div>
      </div>
    </div>


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
