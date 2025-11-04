@if ($cartContents->count() != 0)
<!--============================
      CART VIEW PAGE START
==============================-->
<section id="wsus__cart_view">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="wsus__cart_tab">
                    <li><a class="wsus__order_active" href="{{ route('cart') }}">{{__('Shopping Cart')}}</a></li>
                    <li><a href="{{ route('user.checkout.billing-address') }}">{{__('Checkout')}}</a></li>
                    <li><a href="javascript:;">{{__('payment')}}</a></li>
                </ul>
            </div>
            <div class="col-xl-12">
                <div class="wsus__cart_list">
                    <div class="table-responsive" id="response_cart_view">
                        <form id="cartUpdateForm">
                        <table>
                            <tbody>
                                <tr class="d-flex">
                                    <th class="wsus__pro_img">
                                        {{__('Image')}}
                                    </th>

                                    <th class="wsus__pro_name">
                                        {{__('Product details')}}
                                    </th>

                                    <th class="wsus__pro_status">
                                        {{__('Price')}}
                                    </th>

                                    <th class="wsus__pro_select">
                                        {{__('quantity')}}
                                    </th>

                                    <th class="wsus__pro_tk">
                                        {{__('Sub Total')}}
                                    </th>

                                    <th class="wsus__pro_icon">
                                        <a href="javascript:;" id="cartClear" class="common_btn">{{__('clear cart')}}</a>
                                    </th>
                                </tr>


                                    @csrf
                                @php
                                    $subTotal = 0;
                                @endphp
                                @foreach ($cartContents as $cartContent)
                                    @php
                                        $variantPrice = 0;
                                    @endphp
                                <tr class="d-flex">
                                    <td class="wsus__pro_img"><img src="{{ asset($cartContent->options->image) }}" alt="product" class="img-fluid w-100">
                                    </td>

                                    <td class="wsus__pro_name">
                                        <p><a href="{{ route('product-detail', $cartContent->options->slug) }}">{{ $cartContent->name }}</a></p>
                                            @foreach ($cartContent->options->variants as $indx => $variant)
                                                <span>{{ $variant }}: {{ $cartContent->options->values[$indx] }}({{ $setting->currency_icon }}{{ $cartContent->options->prices[$indx] }})</span>

                                                @php
                                                    $variantPrice += $cartContent->options->prices[$indx];
                                                @endphp
                                            @endforeach

                                    </td>

                                    @php
                                        $productPrice = $cartContent->price;
                                        $total = $productPrice * $cartContent->qty ;
                                        $subTotal += $total;
                                    @endphp

                                    <td class="wsus__pro_status">
                                        <p>{{ $setting->currency_icon }}{{ $productPrice }}</p>
                                    </td>

                                    <td class="wsus__pro_select">
                                        <div class="modal_btn">
                                            <button onclick="shppingCartDecrement('{{ $cartContent->rowId }}')" type="button" class="btn btn-danger btn-sm">-</button>
                                            <input id="qty-{{ $cartContent->rowId }}" name="quantities[]" readonly type="text" min="1" max="100" value="{{ $cartContent->qty }}" />
                                            <button onclick="shppingCartIncrement('{{ $cartContent->rowId }}')" type="button" class="btn btn-success btn-sm">+</button>
                                        </div>
                                        <input type="hidden" name="rowIds[]" value="{{ $cartContent->rowId }}">
                                        <input type="hidden" name="ids[]" value="{{ $cartContent->id }}">
                                    </td>
                                    <td class="wsus__pro_tk">
                                        <h6>{{ $setting->currency_icon }}{{ $total  }} </h6>
                                    </td>
                                    <td class="wsus__pro_icon">
                                        <a href="javascript:;" onclick="cartItemRemove('{{ $cartContent->rowId }}')"><i class="far fa-times"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="wsus__cart_list_footer mt-5 p-4">
            <div class="row justify-content-between">
                <div class="col-xl-6 col-md-6">
                    <div class="wsus__cart_list_footer_top">
                        <h6 class="mb-4">{{__('Do you have a coupon code?')}} </h6>
                        <form id="couponFormId">
                            <input type="text" name="coupon" placeholder="{{__('Enter Your Coupon Code')}}">
                            <button type="submit" class="common_btn">{{__('apply')}}</button>
                        </form>
                        <a class="common_btn mt-4 text-center" href="{{ route('product') }}"><i class="fab fa-shopify"></i> {{__('go to shop')}}</a>
                    </div>
                </div>
                @php
                    $tax_amount = 0;
                    $total_price = 0;
                    $coupon_price = 0;

                    foreach ($cartContents as $key => $content) {
                        $tax = $content->options->tax * $content->qty;
                        $tax_amount = $tax_amount + $tax;
                    }

                    $total_price = $tax_amount + $subTotal;

                    if(Session::get('coupon_price') && Session::get('offer_type')) {
                        if(Session::get('offer_type') == 1) {
                            $coupon_price = Session::get('coupon_price');
                            $coupon_price = ($coupon_price / 100) * $total_price;
                        }else {
                            $coupon_price = Session::get('coupon_price');
                        }
                    }

                    $total_price = $total_price - $coupon_price ;
                @endphp
                <div class="col-xl-4 col-md-6">
                    <div class="wsus__cart_list_footer_button">
                        <h6>{{__('total cart')}}</h6>
                        <p>{{__('subtotal')}}: <span>{{ $setting->currency_icon }}{{ $subTotal }}</span></p>
                        <p>{{__('Tax')}}(+): <span>{{ $setting->currency_icon }}{{ $tax_amount }}</span></p>
                        <p>{{__('Coupon')}}(-): <span>{{ $setting->currency_icon }}{{  $coupon_price  }}</span></p>
                        <p class="total"><span>{{__('total')}}:</span> <span>{{ $setting->currency_icon }}{{ $total_price }}</span></p>
                        <a class="common_btn mt-4 w-100 text-center" href="{{ route('user.checkout.billing-address') }}">{{__('checkout')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@php
    $bannerOne = $banners->where('id',9)->first();
    $bannerTwo = $banners->where('id',10)->first();
@endphp
@if ($bannerOne->status == 1)
<section id="wsus__single_banner">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="wsus__single_banner_content">
                    <div class="wsus__single_banner_img">
                        <img src="{{ asset($bannerOne->image) }}" alt="banner" class="img-fluid w-100">
                    </div>
                    <div class="wsus__single_banner_text">
                        <h6>{{ $bannerOne->description }}</h6>
                        <h3>{{ $bannerOne->title }}</h3>
                        <a class="shop_btn" href="{{ $bannerOne->link }}">{{__('shop now')}}</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="wsus__single_banner_content single_banner_2">
                    <div class="wsus__single_banner_img">
                        <img src="{{ asset($bannerTwo->image) }}" alt="banner" class="img-fluid w-100">
                    </div>
                    <div class="wsus__single_banner_text">
                        <h6>{{ $bannerTwo->description }}</h6>
                        <h3>{{ $bannerTwo->title }}</h3>
                        <a class="shop_btn" href="{{ $bannerTwo->link }}">{{__('shop now')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<!--============================
      CART VIEW PAGE END
==============================-->
@else
        <!--============================
            CART VIEW PAGE START
        ==============================-->
<section id="wsus__cart_view">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="wsus__cart_list cart_empty p-3 p-sm-5 text-center">
                    <p class="mb-4">{{__('your shopping cart is empty')}}</p>
                    <a href="{{ route('product') }}" class="common_btn"><i class="fal fa-store me-2"></i>{{__('Go to Shop')}}</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!--============================
      CART VIEW PAGE END
==============================-->
@endif


<script>
    (function($) {
        "use strict";
        $(document).ready(function () {
            $(".shoppingDecrementId").on("click", function(e){
                cartItemIncrement();
            })
            $(".shoppingIncrementId").on("click", function(e){
                cartItemIncrement();
            })

            $("#cartClear").on("click", function(e){
                $.ajax({
                    type: 'get',
                    url: "{{ route('cart-clear') }}",
                    success: function (response) {
                        $("#CartResponse").html(response)
                        toastr.success("{{__('Your shopping cart is cleared')}}");
                        $.ajax({
                            type: 'get',
                            url: "{{ route('load-sidebar-cart') }}",
                            success: function (response) {
                                $("#load_sidebar_cart").html(response)
                                $.ajax({
                                    type: 'get',
                                    url: "{{ route('get-cart-qty') }}",
                                    success: function (response) {
                                        $("#cartQty").text(response.qty);
                                    },
                                });
                            },
                        });
                    },
                    error: function(err) {
                        toastr.error("{{__('Something went wrong')}}");

                    }
                });
            })

            $("#couponFormId").on("submit", function(e){
                e.preventDefault();
                $.ajax({
                    type: 'get',
                    data: $('#couponFormId').serialize(),
                    url: "{{ route('apply-coupon') }}",
                    success: function (response) {
                        if(response.status == 0){
                            toastr.error(response.message);
                        }else{
                            $("#CartResponse").html(response)
                            toastr.success("{{__('Your shopping cart is cleared')}}");
                        }

                    },
                    error: function(err) {
                        toastr.error("{{__('Something went wrong')}}");

                    }
                });
            })

        });
    })(jQuery);

    function shppingCartDecrement(id){
        let qty = $("#qty-"+id).val();
        if(qty > 1){
            qty = qty - 1;
            $("#qty-"+id).val(qty);
            cartItemIncrement();
        }

    }

    function shppingCartIncrement(id){
        let qty = $("#qty-"+id).val();
        qty = qty*1 + 1*1;
        $("#qty-"+id).val(qty);
        cartItemIncrement();
    }

    function cartItemIncrement(){
        $.ajax({
            type: 'POST',
            data: $('#cartUpdateForm').serialize(),
            url: "{{ route('cart-update') }}",
            success: function (response) {
                if(response.status == 0){
                    toastr.error(response.message);
                    return ;
                }
                $("#CartResponse").html(response)
                toastr.success("{{__('Item Updated Successfully')}}");
                $.ajax({
                    type: 'get',
                    url: "{{ route('load-sidebar-cart') }}",
                    success: function (response) {
                        $("#load_sidebar_cart").html(response)
                        $.ajax({
                            type: 'get',
                            url: "{{ route('get-cart-qty') }}",
                            success: function (response) {
                                $("#cartQty").text(response.qty);
                            },
                        });
                    },
                });
            },
            error: function(err) {
                toastr.error("{{__('Something went wrong')}}");

            }
        });
    }

    function cartItemRemove(rowId){
        $.ajax({
            type: 'get',
            url: "{{ url('cart-item-remove') }}" + "/" + rowId,
            success: function (response) {
                $("#CartResponse").html(response)
                toastr.success("{{__('Item Removed Successfully')}}");
                $.ajax({
                    type: 'get',
                    url: "{{ route('load-sidebar-cart') }}",
                    success: function (response) {
                        $("#load_sidebar_cart").html(response)
                        $.ajax({
                            type: 'get',
                            url: "{{ route('get-cart-qty') }}",
                            success: function (response) {
                                $("#cartQty").text(response.qty);
                            },
                        });
                    },
                });
            },
            error: function(err) {
                toastr.error("{{__('Something went wrong')}}");

            }
        });
        }
</script>
