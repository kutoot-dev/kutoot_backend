@extends('seller.master_layout')
@section('title')
<title>{{__('admin.My Profile')}}</title>
@endsection
@section('seller-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('admin.My Profile')}}</h1>
        <div class="section-header-breadcrumb">
          <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
          <div class="breadcrumb-item">{{__('admin.My Profile')}}</div>
        </div>
      </div>
      <div class="section-body">
        <div class="row mt-5">
            <div class="col-md-3">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Product Sale')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalSoldProduct }}
                  </div>
                </div>
              </div>
            </div>

                <div class="col-md-3">
                    <a href="{{ route('seller.my-withdraw.index') }}">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                            <i class="far fa-newspaper"></i>
                            </div>
                            <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{__('admin.Total Withdraw')}}</h4>
                            </div>
                            <div class="card-body">
                                {{ $setting->currency_icon }}{{ $totalWithdraw }}
                            </div>
                            </div>
                        </div>
                    </a>
                </div>



            <div class="col-md-3">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="far fa-file"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Current Balance')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $setting->currency_icon }}{{ $totalAmount - $totalWithdraw }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
                <a href="{{ route('seller.product.index') }}">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Products')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $seller->products->count() }}
                  </div>
                </div>
              </div>
            </a>
            </div>
          </div>
        <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-5">
              <div class="card profile-widget">
                <div class="profile-widget-header">
                    @if ($user->image)
                        <img alt="image" src="{{ asset($user->image) }}" class="rounded-circle profile-widget-picture">
                    @else
                        <img alt="image" src="{{ asset($defaultProfile->image) }}" class="rounded-circle profile-widget-picture">
                    @endif
                  <div class="profile-widget-items">
                    <div class="profile-widget-item">
                      <div class="profile-widget-item-label">{{__('admin.Joined at')}}</div>
                      <div class="profile-widget-item-value">{{ $user->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="profile-widget-item">
                      <div class="profile-widget-item-label">{{__('admin.Balance')}}</div>
                      <div class="profile-widget-item-value">{{ $setting->currency_icon }}{{ $totalAmount }}</div>
                    </div>
                  </div>
                </div>
                <div class="profile-widget-description">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>{{__('admin.Name')}}</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td>{{__('admin.Email')}}</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td>{{__('admin.Phone')}}</td>
                                <td>{{ $user->phone }}</td>
                            </tr>
                            <tr>
                                <td>{{__('admin.User Status')}}</td>
                                <td>
                                    @if($user->status == 1)
                                    <span class="badge badge-success">{{__('admin.Active')}}</span>
                                    @else
                                    <span class="badge badge-danger">{{__('admin.Inactive')}}</span>
                                @endif
                                </td>
                            </tr>

                            <tr>
                                <td>{{__('admin.Shop Status')}}</td>
                                <td>
                                    @if($seller->status == 1)
                                    <span class="badge badge-success">{{__('admin.Active')}}</span>
                                    @else
                                    <span class="badge badge-danger">{{__('admin.Inactive')}}</span>
                                @endif
                                </td>
                            </tr>


                        </table>
                    </div>
                </div>

                <div class="card-footer text-center">
                  <div class="font-weight-bold mb-2">{{__('admin.Follow')}} {{ $user->name }}</div>
                  @php
                      $colorId = 1;
                  @endphp
                  @foreach ($seller->socialLinks as $index => $socialLink)
                    @php
                        if($index % 4 == 0){
                            $colorId=1;
                        }
                        $color = "";
                        if($colorId == 1){
                            $color = "btn-facebook";
                        }else if($colorId == 2){
                            $color = "btn-twitter";
                        }else if($colorId == 3){
                            $color = "btn-instagram";
                        }else if($colorId == 4){
                            $color = "btn-github";
                        }
                    @endphp

                    <a href="{{ $socialLink->link }}" class="btn btn-social-icon {{ $color }} mr-1">
                        <i class="{{ $socialLink->icon }}"></i>
                    </a>

                    @php
                        $colorId++;
                    @endphp
                  @endforeach

                </div>
              </div>

              <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h1>{{__('admin.Seller Action')}}</h1>
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{ route('seller.shop-profile') }}" class="btn btn-success btn-block btn-lg my-2">{{__('admin.Go To Shop')}}</a>
                                </div>
                                <div class="col-12">
                                    <a href="{{ route('seller.product-review') }}" class="btn btn-primary btn-block btn-lg my-2">{{__('admin.My Reviews')}}</a>
                                </div>
                                <div class="col-12">
                                    <a href="{{ route('seller.email-history') }}" class="btn btn-info btn-block btn-lg my-2">{{__('admin.Email Log')}}</a>
                                </div>
                                <div class="col-12">
                                    <a href="{{ route('seller.change-password') }}" class="btn btn-warning btn-block btn-lg my-2">{{__('admin.Change Password')}}</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>


            <div class="col-12 col-md-12 col-lg-7">
                <div class="card">
                    <form action="{{ route('seller.update-seller-profile') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h4>{{__('admin.Edit Profile')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.New Image')}}</label>
                                    <input type="file" class="form-control-file" name="image">
                                    </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $user->name }}" name="name">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Email')}} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" value="{{ $user->email }}" name="email" readonly>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Phone')}} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $user->phone }}" name="phone">
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Address')}} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $user->address }}" name="address">
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">{{__('admin.Update')}}</button>
                        </div>

                    </form>
                </div>
            </div>
          </div>
      </div>
    </section>
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
                        url:"{{url('/seller/state-by-country/')}}"+"/"+countryId,
                        success:function(response){
                            $("#state_id").html(response.states);
                            var response= "<option value=''>{{__('admin.Select a City')}}</option>";
                            $("#city_id").html(response);
                        },
                        error:function(err){
                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select a State')}}</option>";
                    $("#state_id").html(response);
                    var response= "<option value=''>{{__('admin.Select a City')}}</option>";
                    $("#city_id").html(response);
                }

            })

            $("#state_id").on("change",function(){
                var countryId = $("#state_id").val();
                if(countryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/seller/city-by-state/')}}"+"/"+countryId,
                        success:function(response){
                            $("#city_id").html(response.cities);
                        },
                        error:function(err){
                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select a City')}}</option>";
                    $("#city_id").html(response);
                }

            })


        });
    })(jQuery);
</script>
@endsection
