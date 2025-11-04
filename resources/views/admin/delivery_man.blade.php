@extends('admin.master_layout')
@section('title')
<title>{{__('Delivery Man')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Delivery man')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('Delivery Man')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.delivery-man.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('admin.SN')}}</th>
                                    <th width="10%">{{__('Name')}}</th>
                                    <th width="10%">{{__('Email')}}</th>
                                    <th width="10%">{{__('Total order')}}</th>
                                    <th width="10%">{{__('Image')}}</th>
                                    <th width="10%">{{__('Status')}}</th>
                                    <th width="15%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryMans as $index => $deliveryman)
                                @php
                                    $order=App\Models\Order::where('delivery_man_id', $deliveryman->id)->get();
                                @endphp
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $deliveryman->fname }} {{ $deliveryman->lname }}</td>
                                        <td>{{ $deliveryman->email }}</td>
                                        <td>{{ sizeOf($order) }}</td>
                                        <td>
                                          <img alt="image" src="{{ asset($deliveryman->man_image) }}" class="rounded-circle profile-widget-picture">
                                        </td>
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
                                        <td>
                                        <a href="{{ route('admin.delivery-man.show',$deliveryman->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        <a href="{{ route('admin.delivery-man.edit',$deliveryman->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $deliveryman->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
        $("#deleteForm").attr("action",'{{ url("admin/delivery-man/") }}'+"/"+id)
    }
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
@endsection
