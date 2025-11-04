@extends('user.layout')
@section('title')
    <title>{{__('Invoice')}}</title>
@endsection
@section('user-content')
<style>
    @media print {
        .dashboard_sidebar,
        .wsus__dashboard_menu,
        .invoice_print  {
            display:none!important;
        }

    }
</style>
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
        <div class="dashboard_content">
            <div class="wsus__invoice_area">
                <div class="wsus__invoice_header">
                    <div class="wsus__invoice_content">
                        <div class="row">
                            @php
                                $orderAddress = $order->orderAddress;
                            @endphp
                            <div class="col-xl-4 col-md-4 mb-5 mb-md-0">
                                <div class="wsus__invoice_single">
                                    <h5>{{__('Billing Information')}}</h5>
                                    <h6>{{ $orderAddress->billing_name }}</h6>

                                    @if ($orderAddress->billing_email)
                                        <p>{{ $orderAddress->billing_email }}</p>
                                    @endif

                                    @if ($orderAddress->billing_phone)
                                        <p>{{ $orderAddress->billing_phone }}</p>
                                    @endif
                                    <p>{{ $orderAddress->billing_address }}
                                        {{ $orderAddress->billing_city ? ', '. $orderAddress->billing_city : '' }} {{ $orderAddress->billing_state ? ', '. $orderAddress->billing_state : '' }} {{$orderAddress->billing_country ? ', '. $orderAddress->billing_country : '' }}</p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4 mb-5 mb-md-0">
                                <div class="wsus__invoice_single text-md-center">
                                    <h5>{{__('shipping information')}}</h5>
                                    <h6>{{ $orderAddress->shipping_name }}</h6>

                                    @if ($orderAddress->shipping_email)
                                        <p>{{ $orderAddress->shipping_email }}</p>
                                    @endif

                                    @if ($orderAddress->shipping_phone)
                                        <p>{{ $orderAddress->shipping_phone }}</p>
                                    @endif
                                    <p>{{ $orderAddress->shipping_address }}
                                        {{ $orderAddress->shipping_city ? ', '. $orderAddress->shipping_city : '' }} {{ $orderAddress->shipping_state ? ', '. $orderAddress->shipping_state : '' }} {{$orderAddress->shipping_country ? ', '. $orderAddress->shipping_country : '' }}</p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <div class="wsus__invoice_single text-md-end">
                                    <h5>{{__('Order Id')}}: #{{ $order->order_id }}</h5>
                                    <p>{{__('Date')}}: {{ $order->created_at->format('d F, Y') }}</p>
                                    @if ($order->agree_terms_condition == 'yes')
                                    <p>{{__('Agree with Terms & Conditions')}}: <span class="badge bg-success">{{__('Yes')}}</span></p>
                                    @else
                                    <p>{{__('Agree with Terms & Conditions')}}: <span class="badge bg-danger">{{__('No')}}</span></p>
                                    @endif

                                    <p>{{__('Order Status')}}: @if ($order->order_status == 1)
                                        <span class="badge bg-success">{{__('Progress')}} </span>
                                        @elseif ($order->order_status == 2)
                                        <span class="badge bg-success">{{__('Delivered')}} </span>
                                        @elseif ($order->order_status == 3)
                                        <span class="badge bg-success">{{__('Completed')}} </span>
                                        @elseif ($order->order_status == 4)
                                        <span class="badge bg-danger">{{__('Declined')}} </span>
                                        @else
                                        <span class="badge bg-danger">{{__('Pending')}}</span>
                                      @endif</p>

                                    <p>{{__('Payment Method')}}: {{ $order->payment_method }}</p>
                                    <p>{{__('Payment Status')}}: @if ($order->payment_status == 1)
                                        <span class="badge bg-success">{{__('Success')}}</span>
                                        @else
                                        <span class="badge bg-danger">{{__('Pending')}}</span>
                                    @endif</p>

                                    <p>{{__('Shipping')}}: {{ $order->shipping_method }}</p>
                                    <p>{{__('Transaction')}} : {!! clean(nl2br($order->transection_id)) !!}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="wsus__invoice_description">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th class="name">
                                        {{__('product')}}
                                    </th>
                                    @if ($setting->enable_multivendor == 1)
                                    <th class="amount">
                                        {{__('Shop Name')}}
                                    </th>
                                    @endif
                                    <th class="amount">
                                        {{__('Unit Price')}}
                                    </th>

                                    <th class="quentity">
                                        {{__('quantity')}}
                                    </th>
                                    <th class="total">
                                        {{__('Total')}}
                                    </th>
                                </tr>
                                <tr>
                                    @foreach ($order->orderProducts as $index => $orderProduct)
                                    <td class="name">
                                        @php
                                            $product = $products->where('id',$orderProduct->product_id)->first();
                                        @endphp
                                        <p><a href="{{ route('product-detail', $product->slug) }}">{{ $orderProduct->product_name }}</a></p>

                                        @foreach ($orderProduct->orderProductVariants as $indx => $variant)
                                            <span>{{ $variant->variant_name }} : {{ $variant->variant_value }}</span>
                                        @endforeach

                                    </td>
                                    @if ($setting->enable_multivendor == 1)
                                    <td class="amount">
                                        @if ($orderProduct->seller)
                                            <a href="{{ route('seller-detail',['shop_name' => $orderProduct->seller->slug]) }}">{{  $orderProduct->seller->shop_name }}</a>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="quentity">
                                        {{ $setting->currency_icon }}{{ $orderProduct->unit_price }}
                                    </td>
                                    <td class="total">
                                        {{ $orderProduct->qty }}
                                    </td>
                                    <td class="total">
                                        {{ $setting->currency_icon }}{{ $orderProduct->unit_price * $orderProduct->qty }}
                                    </td>

                                </tr>

                                @endforeach

                            </table>
                        </div>
                    </div>
                </div>
                @if ($order->additional_info)
                    <div class="wsus__invoice_footer mb-3">
                        <h5>{{__('Additional Information')}} : </h5>
                        <p>{!! clean(nl2br($order->additional_info)) !!}</p>
                    </div>
                @endif


                <div class="wsus__invoice_footer">
                    <p><span>{{__('Sub Total')}}:</span> {{ $setting->currency_icon }} {{ $order->sub_total }}</p>
                    <p><span>{{__('Shipping')}}(+):</span> {{ $setting->currency_icon }} {{ $order->shipping_cost }}</p>
                    <p><span>{{__('Tax')}}(+):</span> {{ $setting->currency_icon }}{{ $order->order_vat }}</p>
                    <p><span>{{__('Discount')}}(-): </span> {{ $setting->currency_icon }}{{ $order->coupon_coast }}</p>
                    <p><span>{{__('Total Amount')}}:</span>  {{ $setting->currency_icon }} {{ $order->amount_real_currency }} </p>
                </div>
                <a onclick="window.print()" href="javascript:;" class="invoice_print common_btn mt-3"><i class="fal fa-print"></i> {{__('print')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection
