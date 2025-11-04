@extends('deliveryman.master_layout')
@section('title')
<title>{{__('admin.Dashboard')}}</title>
@endsection
@section('deliveryman-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('admin.Dashbaord')}}</h1>
      </div>

      <div class="section-body">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('Total Order Request')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalOrderRequest->count() }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('Today Accept Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $todayAcceptOrders->count() }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('Runing Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $runnignOrder->count() }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('Completed Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalCompletedOrder->count() }}
                  </div>
                </div>
              </div>
            </div>


            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Declined Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalDeclinedOrder->count() }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Today Earning')}}</h4>
                  </div>
                  <div class="card-body">
                      {{ $setting->currency_icon }}{{ $todayEarning }}
                  </div>
                </div>
              </div>
            </div>

            {{-- <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Today Pending Earning')}}</h4>
                  </div>
                  <div class="card-body">
                    @php
                        $todayPendingEarning = 0;
                        foreach ($todayOrders->where('order_status',0) as $key => $todayOrder) {
                            $orderProducts = $todayOrder->orderProducts->where('seller_id',$seller->id);
                            foreach ($orderProducts as $key => $orderProduct) {
                                $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                                $todayPendingEarning = $todayPendingEarning + $price;
                            }
                        }
                    @endphp

                    {{ $setting->currency_icon }}{{ $todayPendingEarning }}
                  </div>
                </div>
              </div>
            </div> --}}

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('This Month Earning')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $setting->currency_icon }}{{ $thisMonthEarning }}
                  </div>
                </div>
              </div>
            </div>



            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.This Year Earning')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $setting->currency_icon }}{{ $thisYearEarning }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Earning')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $setting->currency_icon }}{{ $totalEarning }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
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
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('Pending Withdraw')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $setting->currency_icon }}{{ $deliveryManPendingWithdraw }}
                  </div>
                </div>
              </div>
            </div>
            
          </div>
      </div>

      <div class="section-body">
        <div class="row mt-4">
            <div class="col">
              <div class="card">
                  <div class="card-header">
                      <h3>{{__('Runing Order')}}</h3>
                  </div>
                <div class="card-body">
                  <div class="table-responsive table-invoice">
                    <table class="table table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th width="5%">{{__('admin.SN')}}</th>
                                <th width="10%">{{__('admin.Customer')}}</th>
                                <th width="10%">{{__('admin.Order Id')}}</th>
                                <th width="15%">{{__('admin.Date')}}</th>
                                <th width="10%">{{__('admin.Quantity')}}</th>
                                <th width="10%">{{__('admin.Amount')}}</th>
                                <th width="10%">{{__('admin.Order Status')}}</th>
                                <th width="10%">{{__('admin.Payment')}}</th>
                                <th width="5%">{{__('admin.Action')}}</th>
                              </tr>
                        </thead>
                        <tbody>
                            @foreach ($runningOrder as $index => $order)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->created_at->format('d F, Y') }}</td>
                                    <td>{{ $order->product_qty }}</td>
                                    <td>{{ $setting->currency_icon }}{{ $order->total_amount }}</td>
                                    <td>
                                        @if ($order->order_status == 1)
                                        <span class="badge badge-success">{{__('admin.Progress')}} </span>
                                        @elseif ($order->order_status == 2)
                                        <span class="badge badge-success">{{__('admin.Delivered')}} </span>
                                        @elseif ($order->order_status == 3)
                                        <span class="badge badge-success">{{__('admin.Completed')}} </span>
                                        @elseif ($order->order_status == 4)
                                        <span class="badge badge-danger">{{__('admin.Declined')}} </span>
                                        @else
                                        <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment_status == 1)
                                        <span class="badge badge-success">{{__('admin.success')}} </span>
                                        @else
                                        <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                                        @endif
                                    </td>

                                    <td>

                                    <a href="{{ route('deliveryman.order-show',$order->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                              @endforeach
                        </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
      </div>
    </section>
  </div>
@endsection
