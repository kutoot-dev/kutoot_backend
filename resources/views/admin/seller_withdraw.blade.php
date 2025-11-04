@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Seller withdraw')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Seller withdraw')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Seller withdraw')}}</div>
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
                                    <th >{{__('admin.Seller')}}</th>
                                    <th >{{__('admin.Method')}}</th>
                                    <th >{{__('admin.Charge')}}</th>
                                    <th >{{__('admin.Total Amount')}}</th>
                                    <th >{{__('admin.Withdraw Amount')}}</th>
                                    <th >{{__('admin.Status')}}</th>
                                    <th >{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdraws as $index => $withdraw)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td><a href="{{ route('admin.seller-show',$withdraw->seller->id) }}">{{ $withdraw->seller->user->name }}</a></td>
                                        <td>{{ $withdraw->method }}</td>
                                        <td>{{ $setting->currency_icon }}{{ $withdraw->withdraw_charge }}</td>
                                        <td>{{ $setting->currency_icon }}{{ $withdraw->total_amount }}</td>
                                        <td>{{ $setting->currency_icon }}{{ $withdraw->withdraw_amount }}</td>
                                        <td>
                                            @if ($withdraw->status==1)
                                            <span class="badge badge-success">{{__('admin.Success')}}</span>
                                            @else
                                            <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.show-seller-withdraw',$withdraw->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>

                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $withdraw->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
            $("#deleteForm").attr("action",'{{ url("admin/delete-seller-withdraw/") }}'+"/"+id)
        }
    </script>
@endsection
