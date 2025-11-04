
@extends('admin.master_layout')
@section('title')
<title>{{__('Dalivery Man Details')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Delivery Man Details')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.delivery-man.index') }}">{{__('Delivery Man')}}</a></div>
              <div class="breadcrumb-item">{{__('Dlivery Man Details')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.delivery-man.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('Delivery Man')}}</a>
            <div class="row mt-5">
                  <div class="col-md-4">
                    <div class="card card-statistic-1">
                      <div class="card-icon bg-warning">
                        <i class="far fa-file"></i>
                      </div>
                      <div class="card-wrap">
                        <div class="card-header">
                          <h4>{{__('Current product Balance')}}</h4>
                        </div>
                        <div class="card-body">
                          {{ $setting->currency_icon }}{{ round($current_product_amount, 2) }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card card-statistic-1">
                      <div class="card-icon bg-primary">
                          <i class="fas fa-coins"></i>
                      </div>
                      <div class="card-wrap">
                        <div class="card-header">
                          <h4>{{__('Completed order')}}</h4>
                        </div>
                        <div class="card-body">
                          {{ $completeOrder->count() }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                      <a href="">
                    <div class="card card-statistic-1">
                      <div class="card-icon bg-success">
                        <i class="fas fa-circle"></i>
                      </div>
                      <div class="card-wrap">
                        <div class="card-header">
                          <h4>{{__('Running order')}}</h4>
                        </div>
                        <div class="card-body">
                          {{ $runingOrder->count() }}
                        </div>
                      </div>
                    </div>
                  </a>
                  </div>
                  <div class="col-md-4">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                        <i class="far fa-newspaper"></i>
                        </div>
                        <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{__('Total earn')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $setting->currency_icon }}{{ $tota_earn }}
                        </div>
                        </div>
                    </div>
                </div>
                  <div class="col-md-4">
                    <a href="{{ route('admin.delivery-man-withdraw-list', $deliveryman->id) }}">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                            <i class="far fa-newspaper"></i>
                            </div>
                            <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{__('admin.Total Withdraw')}}</h4>
                            </div>
                            <div class="card-body">
                                {{ $setting->currency_icon }}{{ $deliveryManWithdraw }}
                            </div>
                            </div>
                        </div>
                    </a>
                </div>
              </div>
            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-12">
                  <div class="card profile-widget">
                    <div class="profile-widget-header">
                       
                        <img alt="image" src="{{ asset($deliveryman->man_image) }}" class="rounded-circle profile-widget-picture">
                      
                      {{-- <div class="profile-widget-items">
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label">{{__('admin.Joined at')}}</div>
                          <div class="profile-widget-item-value"></div>
                        </div>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label">{{__('admin.Balance')}}</div>
                          <div class="profile-widget-item-value"></div>
                        </div>
                      </div> --}}
                    </div>
                    <div class="profile-widget-description">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <td>{{__('admin.Name')}}</td>
                                    <td>{{ $deliveryman->fname }} {{ $deliveryman->lname }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.Email')}}</td>
                                    <td>{{ $deliveryman->email }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.Phone')}}</td>
                                    <td>{{ $deliveryman->phone }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('Delivery Man Type')}}</td>
                                    <td>{{ ucfirst($deliveryman->man_type) }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('Identity Type')}}</td>
                                    <td>{{ ucfirst($deliveryman->idn_type) }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('Identity Number')}}</td>
                                    <td>{{ $deliveryman->idn_num }}</td>
                                </tr>
                                <tr>
                                    <td>{{__('Identity Image')}}</td>
                                    <td><img alt="image" src="{{ asset($deliveryman->idn_image) }}" class="profile-widget-picture mt-3 mb-5"></td>
                                </tr>
                                <tr>
                                    <td>{{__('admin.User Status')}}</td>
                                    <td>
                                        @if($deliveryman->status == 1)
                                        <a href="javascript:;" onclick="manageDeliveryManStatus({{ $deliveryman->id }})">
                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                        </a>
                                        @else
                                        <a href="javascript:;" onclick="manageDeliveryManStatus({{ $deliveryman->id }})">
                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                        </a>
                                    @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- <div class="card-footer text-center">
                      <div class="font-weight-bold mb-2">{{__('admin.Follow')}}</div>

                        <a href="" class="btn btn-social-icon  mr-1">
                            <i class=""></i>
                        </a>

                    </div> --}}
                  </div>
                </div>
              </div>
          </div>
        </section>
      </div>

<script>
    function manageDeliveryManStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/delivery-man-status/')}}"+"/"+id,
            success:function(response){
                toastr.success(response)
            },
            error:function(err){
                console.log(err);

            }
        })
    }
</script>

<script>
    (function($) {
        "use strict";
        $(document).ready(function () {

            $("#country_id").on("change",function(){
                var countryId = $("#country_id").val();
                if(countryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/admin/state-by-country/')}}"+"/"+countryId,
                        success:function(response){
                            $("#state_id").html(response.states);
                            var response= "<option value=''>{{__('admin.Select a City')}}</option>";
                            $("#city_id").html(response);
                        },
                        error:function(err){
                            console.log(err);
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
                        url:"{{url('/admin/city-by-state/')}}"+"/"+countryId,
                        success:function(response){
                            console.log(response);
                            $("#city_id").html(response.cities);
                        },
                        error:function(err){
                            console.log(err);
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
