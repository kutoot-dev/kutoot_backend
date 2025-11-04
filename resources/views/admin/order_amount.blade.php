@extends('admin.master_layout')
@section('title')
<title>{{__('Receive Amount')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Receive amount')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('Receive amount')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.delivery-man-order-amount.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th >{{__('admin.SN')}}</th>
                                    <th >{{__('Delivery Man')}}</th>
                                    <th >{{__('admin.Total Amount')}}</th>
                                    <th >{{__('Date')}}</th>
                                    <th >{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderAmounts as $index => $orderamount)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td><a href="{{ route('admin.delivery-man.show',$orderamount->delivery_man_id) }}">{{ $orderamount->deliveryman->fname }} {{ $orderamount->deliveryman->lname }}</a></td>
                                        <td>{{ $setting->currency_icon }}{{ $orderamount->total_amount }}</td>
                                        <td>{{ $orderamount->created_at->format('Y-m-d') }}</td>
                                        <td>
                                          <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $orderamount->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
            $("#deleteForm").attr("action",'{{ url("admin/delete-delivery-order-amount/") }}'+"/"+id)
        }
    </script>
@endsection
