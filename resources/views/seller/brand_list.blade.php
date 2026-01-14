@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Brands')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Brands')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Brands')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.brand.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>{{__('admin.Name')}}</th>
                                    <th>{{__('admin.Slug')}}</th>
                                    <th>{{__('admin.Logo')}}</th>
                                    <th>{{__('admin.Status')}}</th>
                                    <th>{{__('admin.Approval')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @forelse($brands ?? [] as $index => $brand)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ $brand->slug }}</td>
                                        <td><img class="rounded-circle" src="{{ asset($brand->logo) }}" alt="" width="50px"></td>
                                        <td>
                                            @if($brand->status == 1)
                                            <a href="javascript:;" onclick="changeBrandStatus({{ $brand->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @else
                                            <a href="javascript:;" onclick="changeBrandStatus({{ $brand->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $approvalStatus = $brand->approval_status->value;
                                                $statusLabel = '';
                                                $badgeClass = '';

                                                if ($approvalStatus == 0) {
                                                    $statusLabel = __('admin.Pending');
                                                    $badgeClass = 'badge-warning';
                                                } elseif ($approvalStatus == 1) {
                                                    $statusLabel = __('admin.Approved');
                                                    $badgeClass = 'badge-success';
                                                } elseif ($approvalStatus == 2) {
                                                    $statusLabel = __('admin.Rejected');
                                                    $badgeClass = 'badge-danger';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>
                                        <a href="{{ route('seller.brand.edit',$brand->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $brand->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                  @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{__('admin.No brands found')}}</td>
                                    </tr>
                                @endforelse
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
        $("#deleteForm").attr("action",'{{ url("seller/brand/") }}'+"/"+id)
    }
    function changeBrandStatus(id){
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/seller/brand-status/')}}"+"/"+id,
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

