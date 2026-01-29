@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Brand')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Brand')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Product Brand')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.product-brand.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>{{ __('admin.Seller') }}</th>
                                    <th>{{__('admin.Name')}}</th>
                                    <th>{{__('admin.Slug')}}</th>
                                    <th>{{__('admin.Logo')}}</th>
                                    <th>{{__('admin.Status')}}</th>
                                    <th>{{__('admin.Approval')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($brands as $index => $brand)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $brand->seller ? $brand->seller->shop_name : __('admin.Admin') }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ $brand->slug }}</td>
                                        <td> <img class="rounded-circle brand-img" src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" width="50px"></td>
                                        <td>
                                            @if($brand->status == 1)
                                            <a href="javascript:;" onclick="changeProductBrandStatus({{ $brand->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @else
                                            <a href="javascript:;" onclick="changeProductBrandStatus({{ $brand->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @endif
                                        </td>
                                        <td>
                                            <select name="approval_status" class="form-control" onchange="changeApprovalStatus({{ $brand->id }}, this.value)">
                                                <option {{ $brand->approval_status->value == 0 ? 'selected' : '' }} value="0">{{__('admin.Pending')}}</option>
                                                <option {{ $brand->approval_status->value == 1 ? 'selected' : '' }} value="1">{{__('admin.Approved')}}</option>
                                                <option {{ $brand->approval_status->value == 2 ? 'selected' : '' }} value="2">{{__('admin.Rejected')}}</option>
                                            </select>
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.product-brand.edit',$brand->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        @if ($brand->products->count() == 0)
                                            <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $brand->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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

      <!-- Modal -->
      <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                        <div class="modal-body">
                            {{__('admin.You can not delete this brand. Because there are one or more products associated with this brand.')}}
                        </div>

                  <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                  </div>
              </div>
          </div>
      </div>

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/product-brand/") }}'+"/"+id)
    }
    function changeProductBrandStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/product-brand-status/')}}"+"/"+id,
            success:function(response){
                toastr.success(response)
            },
            error:function(err){
                console.log(err);

            }
        })
    }

    function changeApprovalStatus(id, val){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: {
                _token : '{{ csrf_token() }}',
                approval_status: val
            },
            url:"{{url('/admin/product-brand-approval-status/')}}"+"/"+id,
            success:function(response){
                toastr.success(response.message)
            },
            error:function(err){
                console.log(err);
            }
        })
    }
</script>
@endsection
