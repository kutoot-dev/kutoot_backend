@extends('deliveryman.master_layout')
@section('title')
<title>{{__('My Withdraw')}}</title>
@endsection
@section('deliveryman-content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>{{__('My Withdraw')}}</h1>
      </div>

      <div class="section-body">
        <a href="{{ route('deliveryman.withdraw.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.New withdraw')}}</a>
        <div class="row mt-4">
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive table-invoice">
                    <table class="table table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th >{{__('admin.SN')}}</th>
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
                                    <td>{{ $withdraw->method }}</td>
                                    <td>{{ $setting->currency_icon }}{{ $withdraw->total_amount - $withdraw->withdraw_amount }}</td>
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
                                    <a href="{{ route('deliveryman.withdraw.show',$withdraw->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
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
