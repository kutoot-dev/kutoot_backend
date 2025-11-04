@extends('deliveryman.master_layout')
@section('title')
<title>{{__('admin.Invoice')}}</title>
@endsection
@section('deliveryman-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('admin.Invoice')}}</h1>
      </div>

      <div class="section-body">
        <div class="invoice">
          <div class="invoice-print">
            <div class="row">
              <div class="col-lg-12">
                <div class="invoice-title">
                  <h2><img src="{{ asset($setting->logo) }}" alt="" width="120px"></h2>
                  <div class="invoice-number">Order #{{ $order->order_id }}</div>
                </div>
                <hr>
                @php
                    $orderAddress = $order->orderAddress;
                @endphp
                <div class="row">
                  <div class="col-md-6">
                    <address>
                      <strong>{{__('admin.Billing Information')}}:</strong><br>
                        {{ $orderAddress->billing_name }}<br>
                        @if ($orderAddress->billing_email)
                        {{ $orderAddress->billing_email }}<br>
                        @endif
                        @if ($orderAddress->billing_phone)
                        {{ $orderAddress->billing_phone }}<br>
                        @endif
                        {{ $orderAddress->billing_address }},
                        {{ $orderAddress->billing_city.', '. $orderAddress->billing_state.', '.$orderAddress->billing_country }}<br>
                    </address>
                  </div>
                  <div class="col-md-6 text-md-right">
                    <address>
                      <strong>{{__('admin.Shipping Information')}} :</strong><br>
                      {{ $orderAddress->shipping_name }}<br>
                        @if ($orderAddress->shipping_email)
                        {{ $orderAddress->shipping_email }}<br>
                        @endif
                        @if ($orderAddress->shipping_phone)
                        {{ $orderAddress->shipping_phone }}<br>
                        @endif
                        {{ $orderAddress->shipping_address }},
                        {{ $orderAddress->shipping_city.', '. $orderAddress->shipping_state.', '.$orderAddress->shipping_country }}<br>
                    </address>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <address>
                      <strong>{{__('admin.Payment Information')}}:</strong><br>
                      {{__('admin.Method')}}: {{ $order->payment_method }}<br>
                      {{__('admin.Status')}} : @if ($order->payment_status == 1)
                          <span class="badge badge-success">{{__('admin.Success')}}</span>
                          @else
                          <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                      @endif <br>
                      {{__('admin.Transaction')}}: {!! clean(nl2br($order->transection_id)) !!}
                    </address>
                  </div>
                  <div class="col-md-6 text-md-right">
                    <address>
                      <strong>{{__('admin.Order Information')}}:</strong><br>
                      {{__('admin.Date')}}: {{ $order->created_at->format('d F, Y') }}<br>
                      {{__('admin.Shipping')}}: {{ $order->shipping_method }}<br>
                      {{__('admin.Status')}} :
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
                    </address>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-md-12">
                <div class="section-title">{{__('admin.Order Summary')}}</div>
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-md">
                    <tr>
                      <th width="5%">#</th>
                      <th width="25%">{{__('admin.Product')}}</th>
                      <th width="20%">{{__('admin.Variant')}}</th>
                      @if ($setting->enable_multivendor == 1)
                      <th width="10%">{{__('admin.Shop Name')}}</th>
                      @endif
                      <th width="10%" class="text-center">{{__('admin.Unit Price')}}</th>
                      <th width="10%" class="text-center">{{__('admin.Quantity')}}</th>
                      <th width="10%" class="text-right">{{__('admin.Total')}}</th>
                    </tr>
                    @php
                        $subTotal = 0;
                    @endphp
                    @foreach ($order->orderProducts as $index => $orderProduct)
                        @php
                            $variantPrice = 0;
                            $totalVariant = $orderProduct->orderProductVariants->count();
                        @endphp
                        <tr>
                            <td>{{ ++$index }}</td>
                            <td><a href="">{{ $orderProduct->product_name }}</a></td>
                            <td>
                                @foreach ($orderProduct->orderProductVariants as $indx => $variant)
                                    {{ $variant->variant_name.' : '.$variant->variant_value }}{{ $totalVariant == ++$indx ? '' : ',' }}
                                    <br>
                                    @php
                                        $variantPrice += $variant->variant_price;
                                    @endphp
                                @endforeach

                            </td>
                            @if ($setting->enable_multivendor == 1)
                            <td>
                                @if ($orderProduct->seller)
                                    <a href="{{ route('admin.seller-show',$orderProduct->seller->id) }}">{{  $orderProduct->seller->shop_name }}</a>
                                @endif
                            </td>
                            @endif
                            <td class="text-center">{{ $setting->currency_icon }}{{ $orderProduct->unit_price }}</td>
                            <td class="text-center">{{ $orderProduct->qty }}</td>
                            @php
                                $total = ($orderProduct->unit_price * $orderProduct->qty)
                            @endphp
                            <td class="text-right">{{ $setting->currency_icon }}{{ $total }}</td>
                        </tr>
                        @php
                            $totalVariant = 0;
                        @endphp
                    @endforeach
                  </table>
                </div>
                @if ($order->additional_info)
                <div class="row additional_info">
                    <div class="col">
                        <hr>
                        <h5>{{__('admin.Additional Information')}}: </h5>
                        <p>{!! clean(nl2br($order->additional_info)) !!}</p>
                        <hr>
                    </div>
                </div>
                @endif

                <div class="row mt-3">
                  <div class="col-lg-6 order-status">
                  @if ($order->order_request==0)
                  <div class="section-title">{{__('admin.Order Status')}}</div>
                  <form action="{{ route('deliveryman.order-request-status',$order->id) }}" method="POST">
                    @csrf
                    @method("PUT")
                    <div class="form-group">
                          <label for="">{{__('Request')}}</label>
                          <select name="order_request_status" id="" class="form-control">
                            <option value="1">{{__('Accept')}}</option>
                            <option value="2">{{__('Ignore')}}</option>
                          </select>
                        </div>
                    <button class="btn btn-primary" type="submit">{{__('admin.Update Status')}}</button>
                  </form>
                  @elseif($order->order_status < 3 )
                  <div class="section-title">{{__('admin.Order Status')}}</div>
                  <form action="{{ route('deliveryman.update-order-status',$order->id) }}" method="POST">
                    @csrf
                    @method("PUT")
                    @if ($order->cash_on_delivery == 1)
                      <div class="form-group">
                        <label for="">{{__('admin.Payment')}}</label>
                        <select name="payment_status" id="" class="form-control">
                          <option {{ $order->payment_status == 0 ? 'selected' : '' }} value="0">{{__('admin.Pending')}}</option>
                          <option {{ $order->payment_status == 1 ? 'selected' : '' }} value="1">{{__('admin.Success')}}</option>
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                          <label for="">{{__('admin.Order')}}</label>
                          <select name="order_status" id="" class="form-control">
                            <option {{ $order->order_status == 3 ? 'selected' : '' }} value="3">{{__('admin.Completed')}}</option>
                            <option {{ $order->order_status == 4 ? 'selected' : '' }} value="4">{{__('admin.Declined')}}</option>
                          </select>
                        </div>
                    <button class="btn btn-primary" type="submit">{{__('admin.Update Status')}}</button>
                  </form>
                  @endif
                  </div>
                  

                  <div class="col-lg-6 text-right">
                    @php
                        $sub_total = $order->total_amount;
                        $sub_total = $sub_total - $order->shipping_cost;
                        $sub_total = $sub_total + $order->coupon_coast;

                    @endphp
                    <div class="invoice-detail-item">
                      <div class="invoice-detail-name">{{__('admin.Subtotal')}} : {{ $setting->currency_icon }}{{ round($sub_total, 2) }}</div>
                    </div>
                    <div class="invoice-detail-item">
                      <div class="invoice-detail-name">{{__('admin.Discount')}}(-) : {{ $setting->currency_icon }}{{ round($order->coupon_coast, 2) }}</div>
                    </div>
                    <div class="invoice-detail-item">
                      <div class="invoice-detail-name">{{__('admin.Shipping')}} : {{ $setting->currency_icon }}{{ round($order->shipping_cost, 2) }}</div>
                    </div>

                    <hr class="mt-2 mb-2">
                    <div class="invoice-detail-item">
                      <div class="invoice-detail-value invoice-detail-value-lg">{{__('admin.Total')}} : {{ $setting->currency_icon }}{{ round($order->total_amount, 2) }}</div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <div class="text-md-right print-area">
            <hr>
            <button class="btn btn-success btn-icon icon-left print_btn"><i class="fas fa-print"></i> {{__('admin.Print')}}</button>
            
        </div>
        </div>
      </div>
    </section>
  </div>
  <script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/delete-order/") }}'+"/"+id)
    }

    (function($) {
        "use strict";
        $(document).ready(function() {

            $(".print_btn").on("click", function(){
                $(".custom_click").click();
                window.print();
            })

        });
    })(jQuery);
</script>
@endsection
