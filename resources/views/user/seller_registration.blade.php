@extends('user.layout')
@section('title')
    <title>{{__('Become a Seller')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
      <div class="dashboard_content mt-2 mt-md-0">
        <h3><i class="far fa-user"></i> {{__('Become a Seller')}}</h3>
        <div class="wsus__dashboard_profile mb-4">
          <div class="wsus__dash_pro_area">
            {!! $setting->seller_condition !!}
          </div>
        </div>

        <div class="wsus__dashboard_profile">
          <div class="wsus__dash_pro_area">
            <form action="{{ route('user.seller-request') }}" method="POST" enctype="multipart/form-data">
                @csrf
              <div class="row">
                <div class="col-xl-12">
                  <div class="row">
                    <div class="col-12">
                        <label for="">{{__('Banner Image')}}</label>
                      <div class="wsus__dash_pro_single">
                        <input type="file" name="banner_image">
                      </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                      <div class="wsus__dash_pro_single">
                        <input type="text" placeholder="{{__('Shop Name')}}" name="shop_name">
                      </div>
                    </div>

                    <div class="col-xl-6 col-md-6">
                      <div class="wsus__dash_pro_single">
                        <input type="email" placeholder="{{__('Email')}}" name="email">
                      </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                      <div class="wsus__dash_pro_single">
                        <input type="text" placeholder="{{__('Phone')}}" name="phone">
                      </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                      <div class="wsus__dash_pro_single">
                        <input type="text" placeholder="{{ __('user.Address')}}" name="address">
                      </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                      <div class="wsus__dash_pro_single">
                        <input type="text" placeholder="{{__('Opens at')}}" name="open_at" class="clockpicker" data-align="top" data-autoclose="true" autocomplete="off">
                      </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                      <div class="wsus__dash_pro_single">
                        <input type="text" placeholder="{{__('Closed at')}}" name="closed_at" class="clockpicker" data-align="top" data-autoclose="true" autocomplete="off">
                      </div>
                    </div>


                    <div class="col-xl-12">
                      <div class="wsus__dash_pro_single">
                        <textarea cols="3" rows="5" name="about" placeholder="{{__('About You')}}"></textarea>
                      </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="terms_area">
                            <div class="form-check">
                                <input required name="agree_terms_condition" class="form-check-input" type="checkbox" value="1" id="flexCheckChecked3">
                                <label class="form-check-label" for="flexCheckChecked3">
                                    {{__('I have read and agree with terms and conditions')}}
                                </label>
                            </div>
                        </div>
                    </div>

                  </div>
                </div>

                <div class="col-xl-12">
                  <button class="common_btn mb-4 mt-2" type="submit">{{__('Submit Request')}}</button>
                </div>

              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
@endsection
