@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Sponsors')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Sponsors')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Sponsors')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.sponsor.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>{{__('admin.Logo')}}</th>
                                    <th>{{__('admin.Banner')}}</th>
                                    <th>{{__('admin.Name')}}</th>
                                    <th>{{__('admin.Type')}}</th>
                                    <th>{{__('admin.Serial')}}</th>
                                    <th>{{__('admin.Status')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sponsors as $index => $sponsor)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($sponsor->logo)
                                                <img src="{{ asset($sponsor->logo) }}" width="80px" alt="{{ $sponsor->name }}" class="brand-img">
                                            @else
                                                <span class="brand-img-fallback" style="display:inline-flex;align-items:center;justify-content:center;width:80px;height:50px;background:#f5f5f5;border-radius:4px;color:#999;"><i class="fas fa-building"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sponsor->banner)
                                                <img src="{{ asset($sponsor->banner) }}" width="120px" alt="{{ $sponsor->name }}" class="banner-img">
                                            @else
                                                <span class="banner-img-fallback" style="display:inline-flex;align-items:center;justify-content:center;width:120px;height:60px;background:#f5f5f5;border-radius:4px;color:#999;"><i class="fas fa-image"></i></span>
                                            @endif
                                        </td>
                                        <td>{{ $sponsor->name }}</td>
                                        <td><span class="badge badge-info">{{ $sponsor->type }}</span></td>
                                        <td>{{ $sponsor->serial }}</td>
                                        <td>
                                            @if($sponsor->status == 1)
                                            <a href="javascript:;" onclick="changeSponsorStatus({{ $sponsor->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @else
                                            <a href="javascript:;" onclick="changeSponsorStatus({{ $sponsor->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.sponsor.edit', $sponsor->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                            <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $sponsor->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
        $("#deleteForm").attr("action",'{{ url("admin/sponsor/") }}'+"/"+id)
    }
    function changeSponsorStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/sponsor-status/')}}"+"/"+id,
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
