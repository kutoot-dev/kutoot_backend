@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Subscribers')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Subscribers')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
            </div>
          </div>

        <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h1>{{__('admin.Send Email to All Subscriber')}}</h1>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.each-subscriber-email') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="">{{__('admin.Subject')}}</label>
                                    <input type="text" name="subject" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">{{__('admin.Message')}}</label>
                                    <textarea name="message" id="message" class="form-control text-area-5" cols="30" rows="10"></textarea>
                                </div>
                                <button class="btn btn-primary">{{__('admin.Send Email')}}</button>
                            </form>
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
                                    <th>{{__('admin.Email')}}</th>
                                    <th>{{__('admin.Verified')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscribers as $index => $subscriber)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $subscriber->email }}</td>
                                        <td>
                                            @if($subscriber->is_verified == 1)
                                                <span class="badge badge-success">{{__('admin.Yes')}}</span>
                                            @else
                                                <span class="badge badge-danger">{{__('admin.No')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                        <a href="javascript:;" data-toggle="modal" data-target="#sendEmailModal-{{ $subscriber->id }}" class="btn btn-primary btn-sm"><i class="far fa-envelope" aria-hidden="true"></i></a>

                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $subscriber->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>


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

      @foreach ($subscribers as $index => $subscriber)
      <!-- Modal -->
      <div class="modal fade" id="sendEmailModal-{{ $subscriber->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                      <div class="modal-header">
                              <h5 class="modal-title">{{__('admin.Send To')}} : {{ $subscriber->email }}</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                          </div>
                  <div class="modal-body">
                      <div class="container-fluid">
                          <form action="{{ route('admin.specification-subscriber-email',$subscriber->id) }}" method="POST">
                              @csrf
                              <div class="form-group">
                                  <label for="">{{__('admin.Subject')}}</label>
                                  <input type="text" name="subject" class="form-control">
                              </div>
                              <div class="form-group">
                                  <label for="">{{__('admin.Message')}}</label>
                                  <textarea name="message" id="message" class="form-control text-area-5" cols="30" rows="10"></textarea>
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


<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/delete-subscriber/") }}'+"/"+id)
    }
</script>
@endsection
