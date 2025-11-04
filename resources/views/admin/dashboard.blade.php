@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Dashboard')}}</title>
@endsection
@section('admin-content')
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
                    {{ $todayPendingOrder }}
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
                    {{ $todayTotalOrder }}
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
                    {{ $totalPendingOrder }}
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
                    {{ $totalDeclinedOrder }}
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
                    {{ $totalCompleteOrder }}
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
                    {{ $setting->currency_icon }}{{ round($todayEarning,2) }}
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
                    {{ $setting->currency_icon }}{{ round($todayPendingEarning,2) }}
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
                    {{ $setting->currency_icon }}{{ round($thisMonthEarning,2) }}
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
                    {{ $setting->currency_icon }}{{ round($thisYearEarning,2) }}
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
                    {{ $setting->currency_icon }}{{ round($totalEarning,2) }}
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
                    {{ $totalProduct }}
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
                    {{ $reports }}
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
                    {{ $reviews }}
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
                    <h4>{{__('admin.Total Seller')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $sellers }}
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
                    <h4>{{__('admin.Total User')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $users }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Subscriber')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $subscribers }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="far fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Blog')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $blogs }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="far fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Product Category')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $categories }}
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="far fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>{{__('admin.Total Brand')}}</h4>
                  </div>
                  <div class="card-body">
                    {{ $brands }}
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
                                <th width="10%">{{__('admin.Date')}}</th>
                                <th width="10%">{{__('admin.Quantity')}}</th>
                                <th width="10%">{{__('admin.Amount')}}</th>
                                <th width="10%">{{__('admin.Order Status')}}</th>
                                <th width="10%">{{__('admin.Payment')}}</th>
                                <th width="15%">{{__('admin.Action')}}</th>
                              </tr>
                        </thead>
                        <tbody>
                            @foreach ($todayOrders->where('order_status',0) as $index => $order)
                                <tr>
                                    <td>{{ ++$index ?? ''}}</td>
                                    <td>{{ $order->user->name ?? ''}}</td>
                                    <td>{{ $order->order_id ?? ''}}</td>
                                    <td>{{ $order->created_at->format('d F, Y') ?? ''}}</td>
                                    <td>{{ $order->product_qty ?? ''}}</td>
                                    <td>{{ $setting->currency_icon ?? '' }}{{ $order->amount_real_currency ?? ''}}</td>
                                    <td>
                                        @if ($order->order_status == 1)
                                        <span class="badge badge-success">{{__('admin.Progress')}} </span>
                                        @elseif ($order->order_status == 2)
                                        <span class="badge badge-success">{{__('admin.Delivered')}} </span>
                                        @elseif ($order->order_status == 3)
                                        <span class="badge badge-success">{{__('admin.Completed')}}</span>
                                        @elseif ($order->order_status == 4)
                                        <span class="badge badge-danger">{{__('admin.Declined')}}</span>
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

                                    <a href="{{ route('admin.order-show',$order->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>

                                    <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $order->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>

                                    <a href="javascript:;" data-toggle="modal" data-target="#orderModalId-{{ $order->id }}" class="btn btn-warning btn-sm"><i class="fas fa-truck" aria-hidden="true"></i></a>
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

   <!-- Modal -->
   @foreach ($todayOrders as $index => $order)
   <div class="modal fade" id="orderModalId-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
       <div class="modal-dialog" role="document">
           <div class="modal-content">
                   <div class="modal-header">
                           <h5 class="modal-title">{{__('admin.Order Status')}}</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                       </div>
               <div class="modal-body">
                   <div class="container-fluid">
                       <form action="{{ route('admin.update-order-status',$order->id) }}" method="POST">
                         @method('PUT')
                           @csrf
                           <div class="form-group">
                               <label for="">{{__('admin.Payment')}}</label>
                               <select name="payment_status" id="" class="form-control">
                                   <option {{ $order->payment_status == 0 ? 'selected' : '' }} value="0">{{__('admin.Pending')}}</option>
                                   <option {{ $order->payment_status == 1 ? 'selected' : '' }} value="1">{{__('admin.Success')}}</option>
                               </select>
                           </div>
                           <div class="form-group">
                               <label for="">{{__('admin.Order')}}</label>
                               <select name="order_status" id="" class="form-control">
                                 <option {{ $order->order_status == 0 ? 'selected' : '' }} value="0">{{__('admin.Pending')}}</option>
                                 <option {{ $order->order_status == 1 ? 'selected' : '' }} value="1">{{__('admin.In Progress')}}</option>
                                 <option {{ $order->order_status == 2 ? 'selected' : '' }}  value="2">{{__('admin.Delivered')}}</option>
                                 <option {{ $order->order_status == 3 ? 'selected' : '' }} value="3">{{__('admin.Completed')}}</option>
                                 <option {{ $order->order_status == 4 ? 'selected' : '' }} value="4">{{__('admin.Declined')}}</option>
                               </select>
                           </div>


                   </div>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                   <button type="submit" class="btn btn-primary">{{__('admin.Update Status')}}</button>
               </div>
             </form>
           </div>
       </div>
   </div>

   @endforeach

   <script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/delete-order/") }}'+"/"+id)
    }
</script>
@endsection
