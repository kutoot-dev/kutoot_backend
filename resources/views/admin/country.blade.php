@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Country / Region')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Country / Region')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Country / Region')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.country.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>

            <a href="{{ route('admin.country-import-page') }}" class="btn btn-success"><i class="fas fa-plus"></i> {{__('Bulk Upload')}}</a>

            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>{{__('admin.Name')}}</th>
                                    <th>{{__('admin.Status')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($countries as $index => $country)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $country->name }}</td>
                                        <td>
                                            @if($country->status == 1)
                                                <a href="javascript:;" onclick="changeCountryStatus({{ $country->id }})">
                                                    <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                                </a>
                                                @else
                                                <a href="javascript:;" onclick="changeCountryStatus({{ $country->id }})">
                                                    <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                                </a>
                                                @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.country.edit',$country->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                            @if ($country->countryStates->count() == 0 && $country->addressCountires->count() == 0)
                                            <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $country->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
        </section>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                      <div class="modal-body">
                          {{__('admin.You can not delete this country. Because there are one or more states, cities, users and seller has been created in this country.')}}
                      </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                </div>
            </div>
        </div>
    </div>

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/country/") }}'+"/"+id)
    }
    function changeCountryStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/country-status/')}}"+"/"+id,
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
