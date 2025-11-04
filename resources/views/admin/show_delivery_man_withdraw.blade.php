@extends('admin.master_layout')
@section('title')
<title>{{__('Delivery man withdraw')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Delivery man withdraw')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('Delivery man withdraw')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.delivery-man-withdraw') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('Delivery man withdraw')}}</a>
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover">
                            <tr>
                                <td width="50%">{{__('admin.Seller')}}</td>
                                <td width="50%"><a href="{{ route('admin.seller-show',$withdraw->deliveryman->id) }}">{{ $withdraw->deliveryman->fname }} {{ $withdraw->deliveryman->lname }}</a></td>
                            </tr>
                            <tr>
                                <td width="50%">{{__('admin.Withdraw Method')}}</td>
                                <td width="50%">{{ $withdraw->method }}</td>
                            </tr>

                            <tr>
                                <td width="50%">{{__('admin.Withdraw Charge Amount')}}</td>
                                <td width="50%">{{ $setting->currency_icon }}{{ $withdraw->total_amount - $withdraw->withdraw_amount }}</td>
                            </tr>

                            <tr>
                                <td width="50%">{{__('admin.Total amount')}}</td>
                                <td width="50%">{{ $setting->currency_icon }}{{ $withdraw->total_amount }}</td>
                            </tr>
                            <tr>
                                <td width="50%">{{__('admin.Withdraw amount')}}</td>
                                <td width="50%">{{ $setting->currency_icon }}{{ $withdraw->withdraw_amount }}</td>
                            </tr>
                            <tr>
                                <td width="50%">{{__('admin.Status')}}</td>
                                <td width="50%">
                                    @if ($withdraw->status==1)
                                    <span class="badge badge-success">{{__('admin.Success')}}</span>
                                    @else
                                    <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">{{__('admin.Requested Date')}}</td>
                                <td width="50%">{{ $withdraw->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @if ($withdraw->status==1)
                                <tr>
                                    <td width="50%">{{__('admin.Approved Date')}}</td>
                                    <td width="50%">{{ $withdraw->approved_date }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td width="50%">{{__('admin.Account Information')}}</td>
                                <td width="50%">
                                    {!! clean(nl2br($withdraw->account_info)) !!}
                                </td>
                            </tr>

                        </table>

                        @if ($withdraw->status == 0)
                            <form action="{{ route('admin.approved-delivery-man-withdraw', $withdraw->id) }}" method="POST" id="approved-withdraw">
                                @csrf
                                @method('PUT')
                            </form>

                            <a href="{{ route('admin.show-delivery-man-withdraw',$withdraw->id) }}" class="btn btn-primary" onclick="event.preventDefault();
                                document.getElementById('approved-withdraw').submit();">{{__('admin.Approve withdraw')}}</i></a>
                        @endif


                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" onclick="deleteData({{ $withdraw->id }})">{{__('admin.Delete withdraw request')}}</a>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>


      <script>
        function deleteData(id){
            $("#deleteForm").attr("action",'{{ url("admin/delete-delivery-man-withdraw/") }}'+"/"+id)
        }
    </script>
@endsection
