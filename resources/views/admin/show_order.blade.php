@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Invoice')}}</title>
@endsection
<style>
    @media print {
        .section-header,
        .order-status,
        #sidebar-wrapper,
        .print-area,
        .main-footer,
        .new-product,
        .mainas,
        .plus,
        .action-btn,
        .delete-icon,
        .new-product,
        .additional_info {
            display:none!important;
        }

    }
</style>
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Invoice')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Invoice')}}</div>
            </div>
          </div>
          <div class="section-body">
            <div class="invoice">
              <div class="invoice-print">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="invoice-title">
                      <h2><img src="{{ asset($setting->logo) }}" alt="" width="120px"></h2>
                      <div class="invoice-number">Order #{{ $order->order_id }}</div>
                    </div>
                    <hr>
                    @php
                        $orderAddress = $order->orderAddress;
                    @endphp
                    <div class="row">
                      <div class="col-md-6">
                        <address>
                          <strong>{{__('admin.Billing Information')}}:</strong><br>
                            {{ $orderAddress->billing_name ?? ''}}<br>
                            @if ($orderAddress->billing_email ?? '')
                            {{ $orderAddress->billing_email ?? '' }}<br>
                            @endif
                            @if ($orderAddress->billing_phone ?? '')
                            {{ $orderAddress->billing_phone ?? ''}}<br>
                            @endif
                           @php
    $parts = array_filter([
        $orderAddress->billing_city ?? '',
        $orderAddress->billing_state ?? '',
        $orderAddress->billing_country ?? '',
    ]);
@endphp

{{ $orderAddress->billing_address ?? '' }},
{{ implode(', ', $parts) }}
<br>

                        </address>
                      </div>
                      <div class="col-md-6 text-md-right">
                        {{-- <address>
                          <strong>{{__('admin.Shipping Information')}} :</strong><br>
                          {{ $orderAddress->shipping_name ?? '' }}<br>
                            @if ($orderAddress->shipping_email )
                            {{ $orderAddress->shipping_email }}<br>
                            @endif
                            @if ($orderAddress->shipping_phone)
                            {{ $orderAddress->shipping_phone }}<br>
                            @endif
                            {{ $orderAddress->shipping_address }},
                            {{ $orderAddress->shipping_city.', '. $orderAddress->shipping_state.', '.$orderAddress->shipping_country }}<br>
                        </address> --}}
                        <strong>{{ __('admin.Shipping Information') }} :</strong><br>

@if(
    empty($orderAddress->shipping_name) &&
    empty($orderAddress->shipping_email) &&
    empty($orderAddress->shipping_phone) &&
    empty($orderAddress->shipping_address) &&
    empty($orderAddress->shipping_city) &&
    empty($orderAddress->shipping_state) &&
    empty($orderAddress->shipping_country)
)
    No address
@else
    {{ $orderAddress->shipping_name ?? '' }}<br>

    @if ($orderAddress->shipping_email)
        {{ $orderAddress->shipping_email }}<br>
    @endif

    @if ($orderAddress->shipping_phone)
        {{ $orderAddress->shipping_phone }}<br>
    @endif

    {{ $orderAddress->shipping_address ?? '' }}

    @php
        $parts = array_filter([
            $orderAddress->shipping_city,
            $orderAddress->shipping_state,
            $orderAddress->shipping_country
        ]);
    @endphp

    @if(!empty($parts))
        , {{ implode(', ', $parts) }}
    @endif
    <br>
@endif

                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <address>
                          <strong>{{__('admin.Payment Information')}}:</strong><br>
                          {{__('admin.Method')}}: {{ $order->payment_method }}<br>
                          {{__('admin.Status')}} : @if ($order->payment_status == 1)
                              <span class="badge badge-success">{{__('admin.Success')}}</span>
                              @else
                              <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                          @endif <br>
                          {{__('admin.Transaction')}}: {!! clean(nl2br($order->transection_id)) !!}
                        </address>
                      </div>
                      <div class="col-md-6 text-md-right">
                        <address>
                          <strong>{{__('admin.Order Information')}}:</strong><br>
                          {{__('admin.Date')}}: {{ $order->created_at->format('d F, Y') }}<br>
                          {{__('admin.Shipping')}}: {{ $order->shipping_method }}<br>
                          {{__('admin.Status')}} :
                          @if ($order->order_status == 1)
                          <span class="badge badge-success">{{__('admin.Progress')}} </span>
                          @elseif ($order->order_status == 2)
                          <span class="badge badge-success">{{__('admin.Delivered')}} </span>
                          @elseif ($order->order_status == 3)
                          <span class="badge badge-success">{{__('admin.Completed')}} </span>
                          @elseif ($order->order_status == 4)
                          <span class="badge badge-danger">{{__('admin.Declined')}} </span>
                          @else
                          <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                        @endif
                        </address>
                      </div>

                      @if ($order->deliveryman)
                      <div class="col-md-6">
                        <address>
                          <strong>{{__('Delivery Man Information')}}:</strong><br>
                          {{__('Name')}}: {{ $order->deliveryman->fname }} {{ $order->deliveryman->lname }}<br>
                          {{__('admin.Status')}} :
                          @if ($order->order_request == 1)
                          <span class="badge badge-success">{{__('Accept')}} </span>
                          @elseif ($order->order_request == 2)
                          <span class="badge badge-success">{{__('Ignore')}} </span>
                          @elseif ($order->order_status == 3)
                          <span class="badge badge-success">{{__('admin.Completed')}} </span>
                          @elseif ($order->order_status == 4)
                          <span class="badge badge-danger">{{__('admin.Declined')}} </span>
                          @else
                          <span class="badge badge-danger">{{__('No response')}}</span>
                        @endif
                        </address>
                      </div> 
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-md-12">
                    <div class="section-title">{{__('admin.Order Summary')}}</div>
                    <div class="table-responsive">
                      <table class="table table-striped table-hover table-md">
                        <tr>
                          <th width="5%">#</th>
                          <th width="25%">{{__('admin.Product')}}</th>
                          <th width="20%">{{__('admin.Variant')}}</th>
                          @if ($setting->enable_multivendor == 1)
                          <th width="10%">{{__('admin.Shop Name')}}</th>
                          @endif
                          <th width="10%" class="text-center">{{__('admin.Unit Price')}}</th>
                          <th width="10%" class="text-center">{{__('admin.Quantity')}}</th>
                          <th width="10%" class="text-right">{{__('admin.Total')}}</th>
                          <th width="10%" class="text-right action-btn">{{__('admin.Action')}}</th>
                        </tr>
                        @php
                            $subTotal = 0;
                            $grand_total = 0;
                        @endphp
                        @foreach ($order->orderProducts as $index => $orderProduct)
                            @php
                                $variantPrice = 0;
                                $totalVariant = $orderProduct->orderProductVariants->count();
                            @endphp
                            <tr>
                                <td>{{ ++$index }}</td>
                                <td><a href="">{{ $orderProduct->product_name }}</a></td>
                                <td>
                                    @foreach ($orderProduct->orderProductVariants as $indx => $variant)
                                        {{ $variant->variant_name.' : '.$variant->variant_value }}{{ $totalVariant == ++$indx ? '' : ',' }}
                                        <br>
                                        @php
                                            $variantPrice += $variant->variant_price;
                                        @endphp
                                    @endforeach

                                </td>
                                @if ($setting->enable_multivendor == 1)
                                <td>
                                    @if ($orderProduct->seller)
                                        <a href="{{ route('admin.seller-show',$orderProduct->seller->id) }}">{{  $orderProduct->seller->shop_name }}</a>
                                    @endif
                                </td>
                                @endif
                                <td class="text-center">{{ $setting->currency_icon }}{{ $orderProduct->unit_price }}</td>
                                <td class="text-center">
                                  <div class="count">
                                        <div class="mainas">
                                            <p>
                                                <a href="{{route('admin.order-quantity-decrement',[$orderProduct->id,$order->id])}}">
                                                    <span>
                                                        <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13 1L1 1" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </p>
                                        </div>
                                        <div class="count-text">
                                            <input type="number" name="qty_update[289]" value="{{ $orderProduct->qty }}" fdprocessedid="hj0efo">
                                        </div>
                                        <div class="plus">
                                            <p>
                                                <a href="{{route('admin.order-quantity-increment',[$orderProduct->id,$order->id])}}">
                                                    <span>
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M7 1V13M13 7L1 7" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                  </td>
                                @php
                                    $total = ($orderProduct->unit_price * $orderProduct->qty)
                                @endphp
                                <td class="text-right">{{ $setting->currency_icon }}{{ $total }}</td>
                                <td class="text-right delete-icon">
                                      <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $orderProduct->id}},{{$order->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                            @php
                                $totalVariant = 0;
                            @endphp
                        @endforeach
                      </table>
                    </div>
                    @if ($order->additional_info)
                    <div class="row additional_info">
                        <div class="col">
                            <hr>
                            <h5>{{__('admin.Additional Information')}}: </h5>
                            <p>{!! clean(nl2br($order->additional_info)) !!}</p>
                            <hr>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                      <div class="col-lg-6 order-status">
                        <div class="section-title">{{__('admin.Order Status')}}</div>

                        <form action="{{ route('admin.update-order-status',$order->id) }}" method="POST">
                          @csrf
                          @method("PUT")
                          <div class="form-group">
                              <label for="">{{__('admin.Payment')}}</label>
                            <select name="payment_status" id="" class="form-control">
                                <option {{ $order->payment_status == 0 ? 'selected' : '' }} value="0">{{__('admin.Pending')}}</option>
                                <option {{ $order->payment_status == 1 ? 'selected' : '' }} value="1">{{__('admin.Success')}}</option>
                            </select>
                          </div>

                          <div class="form-group">
                            <label for="">{{__('admin.Order')}}</label>
                            <select name="order_status" id="" class="form-control">
                              <option {{ $order->order_status == 0 ? 'selected' : '' }} value="0">{{__('admin.Pending')}}</option>
                              <option {{ $order->order_status == 1 ? 'selected' : '' }} value="1">{{__('admin.In Progress')}}</option>
                              <option {{ $order->order_status == 2 ? 'selected' : '' }}  value="2">{{__('admin.Delivered')}}</option>
                              <option {{ $order->order_status == 3 ? 'selected' : '' }} value="3">{{__('admin.Completed')}}</option>
                              <option {{ $order->order_status == 4 ? 'selected' : '' }} value="4">{{__('admin.Declined')}}</option>
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="">{{__('Assign Delivery Man')}}</label>
                          <select name="delivery_man_id" id="" class="form-control select2">
                            <option value="0" {{ $order->order_status == 0 ? 'selected' : '' }}>Select</option>
                              @foreach ($deliverymans as $deliveryman)
                              <option value="{{ $deliveryman->id }}" {{ $order->delivery_man_id == $deliveryman->id ? 'selected' : '' }}>{{ $deliveryman->fname }} {{ $deliveryman->lname }}</option>    
                              @endforeach
                          </select>
                        </div>
                          <button class="btn btn-primary" type="submit">{{__('admin.Update Status')}}</button>
                        </form>
                      </div>

                      <div class="col-lg-6 text-right">
                        @php
                            $sub_total = $order->total_amount;
                            $grand_total = ($order->total_amount+$order->shipping_cost + $order->total_amount*($setting->tex/100))-$order->coupon_coast;

                        @endphp
                        <div class="invoice-detail-item">
                          <div class="invoice-detail-name">{{__('admin.Subtotal')}} : {{ $setting->currency_icon }}{{ round($sub_total, 2) }}</div>
                        </div>
                        <div class="invoice-detail-item">
                          <div class="invoice-detail-name">{{__('admin.Discount')}}(-) : {{ $setting->currency_icon }}{{ round($order->coupon_coast, 2) }}</div>
                        </div>

                        <div class="invoice-detail-item">
                          <div class="invoice-detail-name">{{__('admin.Shipping')}} : {{ $setting->currency_icon }}{{ round($order->shipping_cost, 2) }}</div>
                        </div>

                        <div class="invoice-detail-item">
                          <div class="invoice-detail-name">{{__('admin.Tax')}} : {{($setting->tax)}}%</div>
                        </div>

                        <hr class="mt-2 mb-2">
                        <div class="invoice-detail-item">
                          <div class="invoice-detail-value invoice-detail-value-lg">{{__('admin.Total')}} : {{ $setting->currency_icon }}{{ round($grand_total, 2) }}</div>
                        </div>
                      </div>

                    </div>
                    
                  </div>
{{-- 
                  <div class="row mt-3 new-product">
                    <div class="col-lg-12">
                        <div class="section-title">Add New</div>
                          <form action="{{route('admin.add-new-product-in-order',$order->id)}}" method="POST">
                          @csrf
                            <div class="form-group">
                                <label for="">{{__('admin.Payment')}}</label>
                                <select name="product_id"  class="form-control select2">
                                    <option value=" " >{{__('admin.Select')}}</option>
                                    @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                  <label>{{__('admin.Quantity')}} <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control"  name="quantity">
                              </div>

                            <button class="btn btn-success"></i>New Order</button>
                          </form>
                      </div>
                  </div>
                </div> --}}


                {{-- <div class="row mt-3 new-product">
                    <div class="col-md-4 pt-2">
                      <button style=" background-color: #6777ef; color: #fff;" type="button" class="btn btn-info btn-primary-two" data-toggle="modal"
                              data-target="#exampleModalLong-2">
                            Add new product
                      </button>
                    </div>
                </div> --}}
        
              <div class="text-md-right print-area">
                <hr>
                <button class="btn btn-success btn-icon icon-left print_btn"><i class="fas fa-print"></i> {{__('admin.Print')}}</button>
                <button class="btn btn-danger btn-icon icon-left" data-toggle="modal" data-target="#deleteModal" onclick="deleteData({{ $order->id }})"><i class="fas fa-times"></i> {{__('admin.Delete')}}</button>
              </div>
            </div>
          </div>
        </section>
      </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModalLong-2"  role="dialog"
          aria-labelledby="exampleModalLongTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-two" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLongTitle-1"> {{__('admin.Add New Product') }}</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="modal-from">
                          <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                          @csrf
                          <div class="row">
                              <div class="form-group col-12">
                                  <label>{{__('admin.Thumbnail Image Preview')}}</label>
                                  <div>
                                      <img id="preview-img" class="admin-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                  </div>

                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Thumnail Image')}} <span class="text-danger">*</span></label>
                                  <input type="file" class="form-control-file"  name="thumb_image" onchange="previewThumnailImage(event)" required>
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Short Name')}} <span class="text-danger">*</span></label>
                                  <input type="text" id="short_name" class="form-control"  name="short_name" value="{{ old('short_name') }}" required>
                              </div>

                              <div class="form-group col-12">
                                  <label>{{__('admin.Name')}} <span class="text-danger">*</span></label>
                                  <input type="text" id="name" class="form-control"  name="name" value="{{ old('name') }}" required>
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Slug')}} <span class="text-danger">*</span></label>
                                  <input type="text" id="slug" class="form-control"  name="slug" value="{{ old('slug') }}">
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Category')}} <span class="text-danger">*</span></label>
                                  <select name="category" class="form-control select2" id="category" required>
                                      <option value="">{{__('admin.Select Category')}}</option>
                                      @foreach ($categories as $category)
                                          <option value="{{ $category->id }}">{{ $category->name }}</option>
                                      @endforeach
                                  </select>
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Sub Category')}}</label>
                                  <select name="sub_category" class="form-control select2" id="sub_category">
                                      <option value="">{{__('admin.Select Sub Category')}}</option>
                                  </select>
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Child Category')}}</label>
                                  <select name="child_category" class="form-control select2" id="child_category">
                                      <option value="">{{__('admin.Select Child Category')}}</option>
                                  </select>
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Brand')}} </label>
                                  <select name="brand" class="form-control select2" id="brand">
                                      <option value="">{{__('admin.Select Brand')}}</option>
                                      @foreach ($brands as $brand)
                                          <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                      @endforeach
                                  </select>
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.SKU')}} </label>
                                  <input type="text" class="form-control" name="sku">
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('Price')}} <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control" name="price" value="{{ old('price') }}" required>
                              </div>
                              <div class="form-group col-6">
                                  <label>{{__('admin.Offer Price')}}</label>
                                  <input type="text" class="form-control" name="offer_price" value="{{ old('offer_price') }}">
                              </div>



                              <div class="form-group col-6">
                                  <label>{{__('admin.Stock Quantity')}} <span class="text-danger">*</span></label>
                                  <input type="number" class="form-control" name="quantity" value="{{ old('quantity') }}" required>
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Weight')}} <span class="text-danger">*</span></label>
                                  <input type="text" class="form-control" name="weight" value="{{ old('weight') }}" required>
                              </div>

                              <div class="form-group col-6">
                                  <label>{{__('admin.Short Description')}} <span class="text-danger">*</span></label>
                                  <textarea name="short_description" id="" cols="30" rows="10" class="form-control text-area-5">{{ old('short_description') }}</textarea>
                              </div>
                              
                              <div class="form-group col-6">
                                  <label>{{__('admin.Long Description')}} <span class="text-danger">*</span></label>
                                  <textarea name="long_description" id="" cols="30" rows="10" class="form-control text-area-5">{{ old('long_description') }}</textarea>
                              </div>

                              <div class="form-group col-12">
                                  <label>{{__('admin.Highlight')}}</label>
                                  <div>
                                      <input type="checkbox"name="top_product" id="top_product"> <label for="top_product" class="mr-3" >{{__('admin.Top Product')}}</label>

                                      <input type="checkbox" name="new_arrival" id="new_arrival"> <label for="new_arrival" class="mr-3" >{{__('admin.New Arrival')}}</label>

                                      <input type="checkbox" name="best_product" id="best_product"> <label for="best_product" class="mr-3" >{{__('admin.Best Product')}}</label>

                                      <input type="checkbox" name="is_featured" id="is_featured"> <label for="is_featured" class="mr-3" >{{__('admin.Featured Product')}}</label>
                                  </div>
                              </div>

                              <div class="form-group col-12">
                                  <label>{{__('admin.Status')}} <span class="text-danger">*</span></label>
                                  <select name="status" class="form-control" required>
                                      <option value="1">{{__('admin.Active')}}</option>
                                      <option value="0">{{__('admin.Inactive')}}</option>
                                  </select>
                              </div>

                              <div class="form-group col-12">
                                  <label>{{__('admin.SEO Title')}}</label>
                                  <input type="text" class="form-control" name="seo_title" value="{{ old('seo_title') }}">
                              </div>

                              <div class="form-group col-12">
                                  <label>{{__('admin.SEO Description')}}</label>
                                  <textarea name="seo_description" id="" cols="30" rows="10" class="form-control text-area-5">{{ old('seo_description') }}</textarea>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-12">
                                    <button type="submit" class="modal-from-btm-btn">{{__('admin.Save') }}</button>
                              </div>
                          </div>
                      </form>
                                              
                      </div>
                  </div>
              </div>
          </div>
      </div>


      <script>
        function deleteData(id){
            $("#deleteForm").attr("action",'{{ url("admin/delete-order/") }}'+"/"+id)
        }
        (function($) {
            "use strict";
            $(document).ready(function() {

                $(".print_btn").on("click", function(){
                    $(".custom_click").click();
                    window.print()
                })

            });
        })(jQuery);

    </script>

    <script>
        function deleteData(id,order_id){
          console.log(order_id);
            $("#deleteForm").attr("action",'{{ url("admin/delete-order-product/") }}'+"/"+id+"/"+order_id)
        }
    </script>

<script>
    (function($) {
        "use strict";
        var specification = true;
        $(document).ready(function () {
            $("#name").on("focusout",function(e){
                $("#slug").val(convertToSlug($(this).val()));
            })

            $("#category").on("change",function(){
                var categoryId = $("#category").val();
                if(categoryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/admin/subcategory-by-category/')}}"+"/"+categoryId,
                        success:function(response){
                            $("#sub_category").html(response.subCategories);
                            var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                            $("#child_category").html(response);
                        },
                        error:function(err){
                            console.log(err);
                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select Sub Category')}}</option>";
                    $("#sub_category").html(response);
                    var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                    $("#child_category").html(response);
                }
            });

            $("#country").on("change",function(){
                var countryId = $("#country").val();
                if(countryId){
                    $.ajax({
                        type:"get",
                        url:"{{url('/admin/state-by-country/')}}"+"/"+countryId,
                        success:function(response){
                            $("#country").html(response.state);
                            var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                            $("#state").html(response);
                        },
                        error:function(err){
                            console.log(err);
                        }
                    })
                }else{
                    var response= "<option value=''>{{__('admin.Select Sub Category')}}</option>";
                    $("#country").html(response);
                    var response= "<option value=''>{{__('admin.Select Child Category')}}</option>";
                    $("#state").html(response);
                }
            });

           

            $("#is_return").on('change',function(){
                var returnId = $("#is_return").val();
                if(returnId == 1){
                    $("#policy_box").removeClass('d-none');
                }else{
                    $("#policy_box").addClass('d-none');
                }

            })

            $("#addNewSpecificationRow").on('click',function(){
                var html = $("#hidden-specification-box").html();
                $("#specification-box").append(html);
            })

            $(document).on('click', '.deleteSpeceficationBtn', function () {
                $(this).closest('.delete-specification-row').remove();
            });


            $("#manageSpecificationBox").on("click",function(){
                if(specification){
                    specification = false;
                    $("#specification-box").addClass('d-none');
                }else{
                    specification = true;
                    $("#specification-box").removeClass('d-none');
                }


            })

        });
    })(jQuery);

    function convertToSlug(Text){
            return Text
                .toLowerCase()
                .replace(/[^\w ]+/g,'')
                .replace(/ +/g,'-');
    }

    function previewThumnailImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    };

</script>

@endsection
