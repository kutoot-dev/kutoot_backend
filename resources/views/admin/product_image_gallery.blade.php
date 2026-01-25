@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Gallery')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Gallery')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.product.index') }}">{{__('admin.Products')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Product Gallery')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.product.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Products')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-header">
                        <h1>{{__('admin.Product')}} : {{ $product->name }}</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.store-product-gallery') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="">{{__('admin.New Image (Multiple)')}}</label>
                                <input type="file" class="form-control-file" name="images[]" multiple>
                            </div>
                            <input type="hidden" name="product_id" required value="{{ $product->id }}">
                            <button type="submit" class="btn btn-primary">{{__('admin.Upload')}}</button>
                        </form>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>{{__('admin.Image')}}</th>
                                    <th>{{__('admin.Status')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($gallery as $index => $image)
                                    <tr>
                                        <td> <img class="p-2 product-img" src="{{ asset($image->image) }}" alt="Product image" width="200px"></td>
                                        {{-- <td>
                                            @if($image->status == 1)
                                            <a href="javascript:;" onclick="changeProductStatus({{ $image->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @else
                                            <a href="javascript:;" onclick="changeProductStatus({{ $image->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.InActive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>
                                            @endif
                                        </td> --}}

<td>
    <input
        type="checkbox"
        class="status-toggle"
        data-id="{{ $image->id }}"
        {{ $image->status == 1 ? 'checked' : '' }}
        data-toggle="toggle"
        data-on="{{__('admin.Active')}}"
        data-off="{{__('admin.InActive')}}"
        data-onstyle="success"
        data-offstyle="danger">
</td>


                                        <td>

                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $image->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
        $("#deleteForm").attr("action",'{{ url("admin/delete-product-image/") }}'+"/"+id)
    }
    // function changeProductStatus(id){

    //     $.ajax({
    //         type:"put",
    //         data: { _token : '{{ csrf_token() }}' },
    //         url:"{{url('/admin/product-gallery-status/')}}"+"/"+id,
    //         success:function(response){
    //             toastr.success(response)
    //         },
    //         error:function(err){
    //             console.log(err);

    //         }
    //     })
    // }
$(document).on('change', '.status-toggle', function(){
    let id = $(this).data('id');

    $.ajax({
        url: "{{ url('/admin/product-gallery-status') }}/" + id,
        type: "PUT",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(response){
            toastr.success(response);
        },
        error: function(xhr, status, error){
            console.log("Status:", status);
            console.log("Error:", error);
            console.log("Response:", xhr.responseText);
            toastr.error("Something went wrong!");
        }
    });
});



</script>
@endsection
