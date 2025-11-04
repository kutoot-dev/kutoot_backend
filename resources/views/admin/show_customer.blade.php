
@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Customer List')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Customer List')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Customer List')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.customer-list') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Customer List')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped table-bordered">
                            <tr>
                                <td>{{__('admin.Image')}}</td>
                                <td>
                                    @if ($customer->image)
                                    <img src="{{ asset($customer->image) }}" class="rounded-circle" alt="" width="80px">
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{__('admin.Name')}}</td>
                                <td>{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <td>{{__('admin.Email')}}</td>
                                <td>{{ $customer->email }}</td>
                            </tr>
                            <tr>
                                <td>{{__('admin.Phone')}}</td>
                                <td>{{ $customer->phone }}</td>
                            </tr>
                            <tr>
                                <td>{{__('admin.Address')}}</td>
                                <td>{{ $customer->address }}</td>
                            </tr>

                            <tr>
                                <td>{{__('admin.Status')}}</td>
                                <td>
                                    @if($customer->status == 1)
                                        <a href="javascript:;" onclick="manageCustomerStatus({{ $customer->id }})">
                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                        </a>
                                        @else
                                        <a href="javascript:;" onclick="manageCustomerStatus({{ $customer->id }})">
                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

<script>
    function manageCustomerStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/customer-status/')}}"+"/"+id,
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
