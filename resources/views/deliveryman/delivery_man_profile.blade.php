@extends('deliveryman.master_layout')
@section('title')
<title>{{__('My Profile')}}</title>
@endsection
@section('deliveryman-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('My Profile')}}</h1>
      </div>
      <div class="section-body">
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
            <a href="{{ route('deliveryman.withdraw.index') }}">
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

@endsection
