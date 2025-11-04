@extends('admin.master_layout')
@section('title')
<title>{{__('Delivery man review')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Delivery man review')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('Delivery man review')}}</div>
            </div>
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
                                    <th >{{__('Delivery man')}}</th>
                                    <th >{{__('Customer')}}</th>
                                    <th >{{__('Order id')}}</th>
                                    <th >{{__('Review')}}</th>
                                    <th >{{__('Rating')}}</th>
                                    <th >{{__('admin.Status')}}</th>
                                    <th >{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryManReviews as $index => $review)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td><a href="{{ route('admin.delivery-man.show',$review->delivery_man_id) }}">{{ $review->deliveryman->fname }} {{ $review->deliveryman->lname }}</a></td>
                                        <td><a href="{{ route('admin.customer-show',$review->user_id) }}">{{ $review->user->name }}</a></td>
                                        <td><a href="{{ route('admin.order-show',$review->order_id) }}">{{ $review->order->order_id }}</a></td>
                                        <td>{{ $review->review }}</td>
                                        <td>{{ $review->rating }} <i class="fas fa-star text-warning"></i></td>
                                        <td>
                                          @if($review->status == 1)
                                          <a href="javascript:;" onclick="changeProductTaxStatus({{ $review->id }})">
                                              <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                          </a>

                                          @else
                                          <a href="javascript:;" onclick="changeProductTaxStatus({{ $review->id }})">
                                              <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                          </a>

                                          @endif
                                        </td>
                                        <td>
                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $review->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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

      <script>
        function deleteData(id){
            $("#deleteForm").attr("action",'{{ url("admin/delete-delivery-man-review/") }}'+"/"+id)
        }
        function changeProductTaxStatus(id){
            var isDemo = "{{ env('APP_VERSION') }}"
            if(isDemo == 0){
                toastr.error('This Is Demo Version. You Can Not Change Anything');
                return;
            }
            $.ajax({
                type:"put",
                data: { _token : '{{ csrf_token() }}' },
                url:"{{url('/admin/delivery-man-review-status/')}}"+"/"+id,
                success:function(response){
                    toastr.success(response)
                },
                error:function(err){
                    console.log(err);
    
                }
            })
        }
    </script>
@endsection
