@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Variant')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Variant')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.product.index') }}">{{__('admin.Products')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Product Variant')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.product.index') }}" class="btn btn-primary"><i class="fas fa-backward"></i> {{__('admin.Go Back')}}</a>

            <a href="javascript:;" data-toggle="modal" data-target="#createVariant"  class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>

            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                      <div class="card-header">
                          <h1>{{__('admin.Product')}} : {{ $product->short_name }}</h1>
                      </div>
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="10%">{{__('admin.SN')}}</th>
                                    <th width="30%">{{__('admin.Name')}}</th>
                                    <th width="30%">{{__('admin.Status')}}</th>
                                    <th width="30%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($variants as $index => $variant)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $variant->name }}</td>
                                        <td>
                                            @if($variant->status == 1)
                                            <a href="javascript:;" onclick="changeVariantStatus({{ $variant->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @else
                                            <a href="javascript:;" onclick="changeVariantStatus({{ $variant->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @endif
                                        </td>
                                        <td>

                                        <a href="{{ route('admin.product-variant-item',['product_id'=>$product->id,'variant_id'=>$variant->id]) }}" class="btn btn-success btn-sm"><i class="fas fa-cog" aria-hidden="true"></i> {{__('admin.manage options')}}</a>

                                        <a href="javascript:;" data-toggle="modal" data-target="#editVariant-{{ $variant->id }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                        @if ($variant->variantItems->count() == 0)
                                            <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $variant->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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

      <!--Create Modal -->
      <div class="modal fade" id="createVariant" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                      <div class="modal-header">
                              <h5 class="modal-title">{{__('admin.Create Variant')}}</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                          </div>
                  <div class="modal-body">
                      <div class="container-fluid">
                        <form action="{{ route('admin.store-product-variant') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control"  name="name">
                                    <input type="hidden" id="product_id" class="form-control"  name="product_id" value="{{ $product->id }}">
                                </div>
                                <div class="form-group col-12">
                                    <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{__('admin.Active')}}</option>
                                        <option value="0">{{__('admin.Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Save')}}</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                                </div>
                            </div>
                        </form>
                      </div>
                  </div>

              </div>
          </div>
      </div>

      {{-- edit modal --}}
      @foreach ($variants as $index => $variant)
        <div class="modal fade" id="editVariant-{{ $variant->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title">{{__('admin.Edit Variant')}}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <form action="{{ route('admin.update-product-variant',$variant->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" id="name" class="form-control"  name="name" value="{{ $variant->name }}">
                                        <input type="hidden" id="product_id" class="form-control"  name="product_id" value="{{ $product->id }}">
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control">
                                            <option {{ $variant->status == 1 ? 'selected' : '' }} value="1">{{__('admin.Active')}}</option>
                                            <option {{ $variant->status == 0 ? 'selected' : '' }} value="0">{{__('admin.Inactive')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
      @endforeach


<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/delete-product-variant/") }}'+"/"+id)
    }
    function changeVariantStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/product-variant-status/')}}"+"/"+id,
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
