@extends('deliveryman.master_layout')
@section('title')
<title>{{__('My Review')}}</title>
@endsection
@section('deliveryman-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('My Review')}}</h1>
      </div>

      <div class="section-body">
          <div class="row mt-4">
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive table-invoice">
                    <table class="table table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th >{{__('admin.SN')}}</th>
                                <th >{{__('admin.Customer')}}</th>
                                <th >{{__('Order id')}}</th>
                                <th >{{__('Review')}}</th>
                                <th >{{__('Rating')}}</th> 
                              </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviews as $index => $review)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>{{ $review->user->name }}</td>
                                    <td><a href="{{ route('deliveryman.order-show',$review->order_id) }}">{{ $review->order->order_id }}</a></td>
                                    <td>{{ $review->review }}</td>
                                    <td>{{ $review->rating }} <i class="fas fa-star text-warning"></i></td>
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
