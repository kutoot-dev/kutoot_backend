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
              <a href="{{ route('admin.send-email-to-all-customer') }}" class="btn btn-primary">{{__('admin.Send email to all user')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th >{{__('admin.SN')}}</th>
                                    <th >{{__('admin.Name')}}</th>
                                    <th >{{__('admin.Email')}}</th>
                                    <th >{{__('admin.Image')}}</th>
                                    <th >{{__('admin.Status')}}</th>
                                    <th >{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $index => $customer)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>
                                            @if ($customer->image)
                                            <img src="{{ asset($customer->image) }}" class="rounded-circle" alt="" width="80px">
                                            @endif
                                        </td>
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
                                        <td>

                                        <a href="{{ route('admin.customer-show',$customer->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>

                                        <a href="javascript:;" data-toggle="modal" data-target="#sendEmailModal-{{ $customer->id }}" class="btn btn-success btn-sm"><i class="far fa-envelope" aria-hidden="true"></i></a>

                                        @php
                                            $existOrder = $orders->where('user_id',13)->count();
                                            $isSeller = $customer->seller;
                                        @endphp

                                        @if ($existOrder == 0 && !$isSeller)
                                            <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $customer->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @else
                                            <a href="javascript:;" data-toggle="modal" data-target="#canNotDeleteModal" class="btn btn-danger btn-sm" disabled><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif


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


      @foreach ($customers as $index => $customer)
      <!-- Modal -->
      <div class="modal fade" id="sendEmailModal-{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                      <div class="modal-header">
                              <h5 class="modal-title">{{__('admin.Send To')}} : {{ $customer->email }}</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                          </div>
                  <div class="modal-body">
                      <div class="container-fluid">
                          <form action="{{ route('admin.send-mail-to-single-user',$customer->id) }}" method="POST">
                              @csrf
                              <div class="form-group">
                                  <label for="">{{__('admin.Subject')}}</label>
                                  <input type="text" name="subject" class="form-control">
                              </div>
                              <div class="form-group">
                                  <label for="">{{__('admin.Message')}}</label>
                                  <textarea name="message" id="message" class="summernote" cols="30" rows="10"></textarea>
                              </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                      <button type="submit" class="btn btn-primary">{{__('admin.Send Email')}}</button>
                  </div>
                </form>
              </div>
          </div>
      </div>
      @endforeach


      <!-- Modal -->
      <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                      <div class="modal-body">
                          {{__('admin.You can not delete this customer. Because there are one or more order and shop account has been created in this customer.')}}
                      </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                </div>
            </div>
        </div>
    </div>

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/customer-delete/") }}'+"/"+id)
    }
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
