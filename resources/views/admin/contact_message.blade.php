@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Contact Message')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Contact Message')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Contact Message')}}</div>
            </div>
          </div>

        <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                        <div class="card-body">
                            <div class="alert alert-warning" role="alert">
                              <p>{{__('admin.If you want to save contact message in database, please enable this button and if you wan"t to save contact message in database, please disable this button')}}</p>
                              <p class="mb-0"></p>
                            </div>

                            @if ($setting->enable_save_contact_message == 1)
                                <a href="javascript:;" onclick="handleSaveContactMessage()">
                                    <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger">
                                </a>
                            @else
                                <a href="javascript:;" onclick="handleSaveContactMessage()">
                                    <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger">
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
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
                                    <th>{{__('admin.SN')}}</th>
                                    <th >{{__('admin.Name')}}</th>
                                    <th>{{__('admin.Email')}}</th>
                                    <th >{{__('admin.Phone')}}</th>
                                    <th >{{__('admin.Subject')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($contactMessages as $index => $contactMessage)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $contactMessage->name }}</td>
                                        <td>{{ $contactMessage->email }}</td>
                                        <td>{{ $contactMessage->phone }}</td>
                                        <td>{{ $contactMessage->subject }}</td>

                                        <td>

                                            <a href="{{ route('admin.show-contact-message', $contactMessage->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $contactMessage->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
          function handleSaveContactMessage(){
            var isDemo = "{{ env('APP_VERSION') }}"
            if(isDemo == 0){
                toastr.error('This Is Demo Version. You Can Not Change Anything');
                return;
            }
              $.ajax({
                type:"put",
                data: { _token : '{{ csrf_token() }}' },
                url:"{{ url('/admin/enable-save-contact-message') }}",
                success:function(response){
                   toastr.success(response)
                },
                error:function(err){
                    console.log(err);
                }
              })
          }

        function deleteData(id){
            $("#deleteForm").attr("action",'{{ url("admin/delete-contact-message/") }}'+"/"+id)
        }

      </script>
@endsection
