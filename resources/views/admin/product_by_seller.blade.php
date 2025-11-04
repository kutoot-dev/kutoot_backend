@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Products of Seller')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Products of Seller')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.seller-show',$user->seller->id) }}">{{__('admin.Seller Profile')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Products of Seller')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.seller-show',$user->seller->id) }}" class="btn btn-primary"><i class="fas fa-user"></i> {{ $user->name }}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('admin.SN')}}</th>
                                    <th width="30%">{{__('admin.Name')}}</th>
                                    <th width="10%">{{__('admin.Price')}}</th>
                                    <th width="15%">{{__('admin.Photo')}}</th>
                                    <th width="15%">{{__('admin.Type')}}</th>
                                    <th width="10%">{{__('admin.Status')}}</th>
                                    <th width="15%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td><a href="{{ route('product-detail', $product->slug) }}">{{ $product->short_name }}</a> </td>
                                        <td>{{ $setting->currency_icon }}{{ $product->price }}</td>
                                        <td> <img class="rounded-circle" src="{{ asset($product->thumb_image) }}" alt="" width="80px"></td>
                                        <td>
                                            @if ($product->is_undefine == 1)
                                                {{__('admin.Undefine Product')}}
                                            @elseif ($product->new_product == 1)
                                                {{__('admin.New Arrival')}}
                                            @elseif ($product->is_featured == 1)
                                                {{__('admin.Featured Product')}}
                                            @elseif ($product->is_top == 1)
                                                {{__('admin.Top Product')}}
                                            @elseif ($product->is_best == 1)
                                                {{__('admin.Best Product')}}
                                            @elseif ($product->is_flash_deal == 1)
                                                {{__('admin.Flash Deal')}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->status == 1)
                                            <a href="javascript:;" onclick="changeProductStatus({{ $product->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @else
                                            <a href="javascript:;" onclick="changeProductStatus({{ $product->id }})">
                                                <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                            </a>

                                            @endif
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.product.edit',$product->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $product->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        <div class="dropdown d-inline">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <i class="fas fa-cog"></i>
                                            </button>

                                            <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -131px, 0px);">
                                              <a class="dropdown-item has-icon" href="{{ route('admin.product-gallery',$product->id) }}"><i class="far fa-image"></i> {{__('admin.Image Gallery')}}</a>

                                              <a class="dropdown-item has-icon" href="{{ route('admin.product-highlight',$product->id) }}"><i class="fas fa-lightbulb"></i> {{__('admin.Highlight')}}</a>

                                              <a class="dropdown-item has-icon" href="{{ route('admin.product-variant',$product->id) }}"><i class="fas fa-cog"></i>{{__('admin.Product Variant')}}</a>

                                            </div>
                                          </div>

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
        $("#deleteForm").attr("action",'{{ url("admin/product/") }}'+"/"+id)
    }
    function changeProductStatus(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/product-status/')}}"+"/"+id,
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
