@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Shipping Rule')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Shipping Rule')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Shipping Rule')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="javascript:;" data-toggle="modal" data-target="#modelId" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>

            <a href="{{ route('admin.shipping-import-page') }}" class="btn btn-success"><i class="fas fa-plus"></i> {{__('Bulk Upload')}}</a>

            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.Rule')}}</th>
                                    <th>{{__('admin.Condition')}}</th>
                                    <th>{{__('admin.Fee')}}</th>
                                    <th>{{__('admin.Address')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($shippings as $index => $shipping)
                                    <tr>
                                        <td>{{ $shipping->shipping_rule }}</td>
                                        <td>
                                            @if ($shipping->type == 'base_on_price')
                                                @if ($shipping->condition_to == -1)
                                                    {{ $setting->currency_icon. $shipping->condition_from }} - {{__('admin.Unlimited')}}
                                                @else
                                                    {{ $setting->currency_icon. $shipping->condition_from }} - {{ $setting->currency_icon. $shipping->condition_to }}
                                                @endif
                                            @elseif($shipping->type == 'base_on_weight')
                                                @if ($shipping->condition_to == -1)
                                                {{  $shipping->condition_from.'g' }} - {{__('admin.Unlimited')}}
                                                @else
                                                    {{  $shipping->condition_from.'g' }} - {{ $shipping->condition_to.'g' }}
                                                @endif
                                            @elseif($shipping->type == 'base_on_qty')
                                                @if ($shipping->condition_to == -1)
                                                    {{  $shipping->condition_from.'qty' }} - {{__('admin.Unlimited')}}
                                                @else
                                                    {{  $shipping->condition_from.'qty' }} - {{ $shipping->condition_to.'qty' }}
                                                @endif

                                            @endif
                                        </td>
                                        <td>
                                            {{ $setting->currency_icon }}{{ $shipping->shipping_fee }}

                                        </td>
                                        <td>
                                            @if ($shipping->city_id == 0)
                                                {{__('admin.All Delivery Area')}}
                                            @else
                                                {{ $shipping->city->name }}, {{ $shipping->city->countryState->name }}, {{ $shipping->city->countryState->country->name }}
                                            @endif
                                        </td>
                                        <td>
                                        <a href="javascript:;" onclick="editShipping('{{ $shipping->id }}')" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $shipping->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
      <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                            <h5 class="modal-title">{{__('admin.Create Shipping Rule')}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                        </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="{{ route('admin.shipping.store') }}" method="POST">
                            @csrf
                            <div class="row">

                                <div class="form-group col-12">
                                    <label>{{__('admin.City / Delivery Area')}} <span class="text-danger">*</span></label>
                                    <select name="city_id" id="" class="form-control select2">
                                        <option value="0">{{__('admin.All')}}</option>
                                        @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}, {{ $city->countryState->name }}, {{ $city->countryState->country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Shipping Rule')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="shipping_rule" class="form-control"  name="shipping_rule" autocomplete="off">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Type')}} <span class="text-danger">*</span></label>
                                    <select name="type" id="type_id" class="form-control">
                                        <option value="base_on_price">{{__('admin.Based on product price')}}</option>
                                        <option value="base_on_weight">{{__('admin.Based on product weight (g)')}}</option>
                                        <option value="base_on_qty">{{__('admin.Based on product quantity')}}</option>

                                    </select>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Condition From')}} <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text type_class"> {{ $setting->currency_icon }}</span>
                                        <input type="text" class="form-control" name="condition_from" autocomplete="off">
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label>{{__('admin.Condition To')}} <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text type_class"> {{ $setting->currency_icon }}</span>
                                        <input type="text" class="form-control" name="condition_to" autocomplete="off">
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Shipping Fee')}} <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text"> {{ $setting->currency_icon }}</span>
                                        <input type="text" class="form-control" name="shipping_fee" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Save')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="editModelId" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                            <h5 class="modal-title">{{__('admin.Edit Shipping Rule')}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                        </div>
                <div class="modal-body">
                    <div class="container-fluid edit_box">

                    </div>
                </div>
            </div>
        </div>
      </div>



<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/shipping/") }}'+"/"+id)
    }

    function editShipping(id){
        $.ajax({
            type:"get",
            url:"{{url('/admin/shipping')}}"+"/"+id,
            success:function(response){
                $("#editModelId").modal('show')
                $(".edit_box").html(response)
            },
            error:function(err){
                console.log(err);
            }
        })
    }

(function($) {
    "use strict";
    $(document).ready(function () {
        $("#type_id").on("change", function(){
            if($("#type_id").val() == 'base_on_price'){
                $(".type_class").html('{{ $setting->currency_icon }}');
            }else if($("#type_id").val() == 'base_on_weight'){
                $(".type_class").html('g');
            }else if($("#type_id").val() == 'base_on_qty'){
                $(".type_class").html('qty');
            }


        })
    });
})(jQuery);

</script>
@endsection
