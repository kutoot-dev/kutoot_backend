@extends('user.layout')
@section('title')
    <title>{{__('Order')}}</title>
@endsection
@section('user-content')
<div class="row">
    <div class="col-xl-9 col-xxl-10 col-lg-9  ms-auto">
      <div class="dashboard_content">
        <h3><i class="fas fa-list-ul"></i> {{__('Order')}}</h3>
        <div class="wsus__dashboard_order">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th class="package">{{__('Order Id')}}</th>
                  <th class="p_date">{{__('Date')}}</th>
                  <th class="e_date">{{__('Total Amount')}}</th>
                  <th class="price">{{__('Quantity')}}</th>
                  <th class="method">{{__('Order Status')}}</th>
                  <th class="tr_id">{{__('Payment Status')}}</th>
                  <th class="status">{{__('Action')}}</th>
                </tr>
              </thead>
              <tbody>
                  @foreach ($orders as $order)
                    <tr>
                        <td class="package">{{ $order->order_id }} </td>
                        <td class="p_date">{{ $order->created_at->format('d F, Y') }}</td>
                        <td class="e_date">{{ $setting->currency_icon }}{{ $order->amount_real_currency }}</td>
                        <td class="price">{{ $order->product_qty }}</td>



                        @if ($order->order_status == 1)
                            <td class="method"> <span class="badge bg-success">{{__('Progress')}}</span></td>
                        @elseif ($order->order_status == 2)
                            <td class="method"> <span class="badge bg-success">{{__('Delivered')}}</span></td>
                        @elseif ($order->order_status == 3)
                            <td class="method"> <span class="badge bg-success">{{__('Completed')}}</span></td>
                        @elseif ($order->order_status == 4)
                            <td class="method"> <span class="badge bg-danger">{{__('Declined')}}</span></td>
                        @else
                            <td class="method"> <span class="badge bg-danger">{{__('Pending')}}</span></td>
                        @endif


                        @if ($order->payment_status == 1)
                            <td class="tr_id"> <span class="badge bg-success">{{__('Active')}}</span></td>
                        @else
                            <td class="tr_id"> <span class="badge bg-danger">{{__('Inactive')}}</span></td>
                        @endif

                        <td class="status"><a href="{{ route('user.order-show', $order->order_id) }}">{{__('view')}}</a></td>
                    </tr>
                  @endforeach

              </tbody>
            </table>
          </div>
          <div id="pagination">
            {{ $orders->links('custom_paginator') }}
            </div>
        </div>
      </div>
    </div>
@endsection
