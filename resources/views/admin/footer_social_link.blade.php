@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Social Link')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Social Link')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Social Link')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="javascript:;" data-toggle="modal" data-target="#createIcon" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>{{__('admin.Link')}}</th>
                                    <th>{{__('admin.Icon')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($links as $index => $link)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $link->link }}</td>
                                        <td> <i class="{{ $link->icon }}"></i></td>
                                        <td>
                                        <a href="javascript:;" data-toggle="modal" data-target="#editIcon-{{ $link->id }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $link->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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


      <!--Create Modal -->
      <div class="modal fade" id="createIcon" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                      <div class="modal-header">
                              <h5 class="modal-title">{{__('admin.Create Social Link')}}</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                          </div>
                  <div class="modal-body">
                      <div class="container-fluid">
                        <form action="{{ route('admin.social-link.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="">{{__('admin.Icon')}}</label>
                                <input type="text" class="form-control custom-icon-picker" name="icon">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('admin.Link')}}</label>
                                <input type="text" class="form-control" name="link">
                            </div>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                            <button type="submit" class="btn btn-primary">{{__('admin.Save')}}</button>
                        </form>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      {{-- edit modal --}}
      @foreach ($links as $link)
        <div class="modal fade" id="editIcon-{{ $link->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title">{{__('admin.Edit Social Link')}}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                        <form action="{{ route('admin.social-link.update',$link->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="">{{__('admin.Icon')}}</label>
                                <input type="text" class="form-control custom-icon-picker" name="icon" value="{{ $link->icon }}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('admin.Link')}}</label>
                                <input type="text" class="form-control" name="link" value="{{ $link->link }}">
                            </div>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                            <button type="submit" class="btn btn-primary">{{__('admin.Update')}}</button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      @endforeach

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/social-link/") }}'+"/"+id)
    }
</script>
@endsection
