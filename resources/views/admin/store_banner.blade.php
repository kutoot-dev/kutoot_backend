@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Store Banners')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Store Banners')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Store Banners')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.store-banner.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.Image')}}</th>
                                    <th>{{__('admin.Title')}}</th>
                                    <th>{{__('admin.Location')}}</th>
                                    <th>{{__('admin.Serial')}}</th>
                                    <th>{{__('admin.Status')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($banners as $index => $banner)
                                    <tr>
                                        <td>
                                            <img src="{{ asset($banner->image) }}" width="150px" alt="{{ $banner->title }}" class="banner-img">
                                        </td>
                                        <td>{{ $banner->title }}</td>
                                        <td>{{ $banner->location ?? '-' }}</td>
                                        <td>{{ $banner->serial }}</td>
                                        <td>
                                            @if($banner->status == 1)
                                            <a href="javascript:;" onclick="changeBannerStatus({{ $banner->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @else
                                            <a href="javascript:;" onclick="changeBannerStatus({{ $banner->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.store-banner.edit', $banner->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                            <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $banner->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
          </div>
        </section>
      </div>

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/store-banner/") }}'+"/"+id)
    }
    function changeBannerStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/store-banner-status/')}}"+"/"+id,
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
