@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Dashboard')}}</title>
@endsection
@section('seller-content')
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
                    <h4>{{__('admin.Today Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $todayOrders->count() }}
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
                    <h4>{{__('admin.Today Pending Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $todayOrders->where('order_status',0)->count() }}
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
                    <h4>{{__('admin.Total Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalOrders->count() }}
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
                    <h4>{{__('admin.Total Pending Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalOrders->where('order_status',0)->count() }}
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
                    {{ $totalOrders->where('order_status',4)->count() }}
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
                    <h4>{{__('admin.Total Complete Order')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalOrders->where('order_status',3)->count() }}
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
                      @php
                        $todayEarning = 0;
                        $todayProductSale = 0;
                        foreach ($todayOrders as $key => $todayOrder) {
                            $orderProducts = $todayOrder->orderProducts->where('seller_id',$seller->id);
                            foreach ($orderProducts as $key => $orderProduct) {
                                $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                                $todayEarning = $todayEarning + $price;
                                $todayProductSale = $todayProductSale + $orderProduct->qty;
                            }
                        }
                      @endphp
                    {{ $setting->currency_icon }}{{ $todayEarning }}
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
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.This month Earning')}}</h4>
                  </div>
                  <div class="card-body">
                    @php
                        $thisMonthEarning = 0;
                        $thisMonthProductSale = 0;
                        foreach ($monthlyOrders as $key => $monthlyOrder) {
                            $orderProducts = $monthlyOrder->orderProducts->where('seller_id',$seller->id);
                            foreach ($orderProducts as $key => $orderProduct) {
                                $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                                $thisMonthEarning = $thisMonthEarning + $price;
                                $thisMonthProductSale = $thisMonthProductSale + $orderProduct->qty;
                            }
                        }
                    @endphp
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
                    @php
                        $thisYearEarning = 0;
                        $thisYearProductSale = 0;
                        foreach ($yearlyOrders as $key => $yearlyOrder) {
                            $orderProducts = $yearlyOrder->orderProducts->where('seller_id',$seller->id);
                            foreach ($orderProducts as $key => $orderProduct) {
                                $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                                $thisYearEarning = $thisYearEarning + $price;
                                $thisYearProductSale = $thisYearProductSale + $orderProduct->qty;
                            }
                        }
                    @endphp
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
                    @php
                        $totalEarning = 0;
                        $totalProductSale = 0;
                        foreach ($totalOrders as $key => $totalOrder) {
                            $orderProducts = $totalOrder->orderProducts->where('seller_id',$seller->id);
                            foreach ($orderProducts as $key => $orderProduct) {
                                $price = ($orderProduct->unit_price * $orderProduct->qty) + $orderProduct->vat;
                                $totalEarning = $totalEarning + $price;
                                $totalProductSale = $totalProductSale + $orderProduct->qty;
                            }
                        }
                    @endphp
                    {{ $setting->currency_icon }}{{ $totalEarning }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Today Product Sale')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $todayProductSale }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.This Month Product Sale')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $thisMonthProductSale }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.This Year Product Sale')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $thisYearProductSale }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Product Sale')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalProductSale }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="far fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Product')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $products->count() }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Product Report')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $reports->count() }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Product Review')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $reviews->count() }}
                  </div>
                </div>
              </div>
            </div>


            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-user"></i>
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
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Pending Withraw')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $setting->currency_icon }}{{ $totalPendingWithdraw }}
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
                      <h3>{{__('admin.Today New Order')}}</h3>
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
                            @foreach ($todayOrders->where('order_status',0) as $index => $order)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->created_at->format('d F, Y') }}</td>
                                    <td>{{ $order->product_qty }}</td>
                                    <td>{{ $setting->currency_icon }}{{ $order->amount_real_currency }}</td>
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

                                    <a href="{{ route('seller.order-show',$order->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
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
