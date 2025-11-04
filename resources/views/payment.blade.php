@extends('layout')
@section('title')
    <title>{{__('Payment')}}</title>
@endsection
@section('meta')
    <meta name="description" content="{{__('payment')}}">
@endsection

@section('public-content')


    <!--============================
         BREADCRUMB START
    ==============================-->
    <section id="wsus__breadcrumb" style="background: url({{  asset($banner->image) }});">
        <div class="wsus_breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>{{__('payment')}}</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">{{__('home')}}</a></li>
                            <li><a href="{{ route('user.checkout.payment') }}">{{__('payment')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        BREADCRUMB END
    ==============================-->

        <!--============================
        PAYMENT PAGE START
    ==============================-->
    <section id="wsus__cart_view">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="wsus__cart_tab">

                        <li><a href="{{ route('cart') }}">{{__('Shopping Cart')}}</a></li>
                        <li><a href="{{ route('user.checkout.billing-address') }}">{{__('Billing Address')}}</a></li>
                        <li><a href="{{ route('user.checkout.checkout') }}">{{__('Shipping Address')}}</a></li>
                        <li><a class="wsus__order_active" href="{{ route('user.checkout.payment') }}">{{__('payment')}}</a></li>

                    </ul>
                </div>
            </div>

            @php
                $subTotal = 0;
                foreach ($cartContents as $cartContent) {
                    $variantPrice = 0;
                    foreach ($cartContent->options->variants as $indx => $variant) {
                        $variantPrice += $cartContent->options->prices[$indx];
                    }
                    $productPrice = $cartContent->price;
                    $total = $productPrice * $cartContent->qty ;
                    $subTotal += $total;
                }

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
                $total_price += $shipping_fee;
            @endphp
            <div class="wsus__pay_info_area">
                <div class="row">
                    <div class="col-xl-2 col-lg-2">
                        <div class="wsus__payment_menu">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                @if ($stripe->status == 1)
                                <button class="nav-link common_btn active" id="v-stripe-payment-tab" data-bs-toggle="pill" data-bs-target="#v-stripe-payment" type="button" role="tab" aria-controls="v-stripe-payment" aria-selected="true">{{__('Stripe')}}</button>
                                @endif

                                @if ($paypal->status == 1)
                                <button class="nav-link common_btn" id="v-paypal-payment-tab" data-bs-toggle="pill" data-bs-target="#v-paypal-payment" type="button" role="tab" aria-controls="v-paypal-payment" aria-selected="false">{{__('Paypal')}}</button>
                                @endif

                                @if ($razorpay->status == 1)
                                <button class="nav-link common_btn" id="v-razorpay-payment-tab" data-bs-toggle="pill" data-bs-target="#v-razorpay-payment" type="button" role="tab" aria-controls="v-razorpay-payment" aria-selected="false">{{__('Razorpay')}}</button>
                                @endif

                                @if ($flutterwave->status == 1)
                                    <button class="nav-link common_btn" id="v-flutterwave-payment-tab" data-bs-toggle="pill" data-bs-target="#v-flutterwave-payment" type="button" role="tab" aria-controls="v-flutterwave-payment" aria-selected="false">{{__('Flutterwave')}}</button>
                                @endif

                                @if ($paystack->mollie_status == 1)
                                <button class="nav-link common_btn" id="v-mollie-payment-tab" data-bs-toggle="pill" data-bs-target="#v-mollie-payment" type="button" role="tab" aria-controls="v-mollie-payment" aria-selected="false">{{__('Mollie')}}</button>
                                @endif

                                @if ($paystack->paystack_status == 1)
                                <button class="nav-link common_btn" id="v-paystack-payment-tab" data-bs-toggle="pill" data-bs-target="#v-paystack-payment" type="button" role="tab" aria-controls="v-paystack-payment" aria-selected="false">{{__('Paystack')}}</button>
                                @endif

                                @if ($instamojoPayment->status == 1)
                                    <button class="nav-link common_btn" id="v-instamojo-payment-tab" data-bs-toggle="pill" data-bs-target="#v-instamojo-payment" type="button" role="tab" aria-controls="v-instamojo-payment" aria-selected="false">{{__('Instamojo')}}</button>
                                @endif

                                @if ($bankPayment->status == 1)
                                <button class="nav-link common_btn" id="v-bank-payment-tab" data-bs-toggle="pill" data-bs-target="#v-bank-payment" type="button" role="tab" aria-controls="v-bank-payment" aria-selected="false">{{__('Bank')}}</button>
                                @endif

                                @if ($bankPayment->cash_on_delivery_status == 1)
                                <button class="nav-link common_btn" id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settings" type="button" role="tab" aria-controls="v-pills-settings" aria-selected="false">{{__('Cash on delivery')}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="tab-content" id="v-pills-tabContent">
                            @if ($stripe->status == 1)
                            <div class="tab-pane fade show active" id="v-stripe-payment" role="tabpanel" aria-labelledby="v-stripe-payment-tab">
                                <div class="row">
                                    <div class="col-xl-12 m-auto">
                                        <div class="wsus__payment_area">
                                            <form role="form" action="{{ route('user.checkout.pay-with-stripe') }}" method="POST" class="require-validation"
                                                    data-cc-on-file="false"
                                                    data-stripe-publishable-key="{{ $stripe->stripe_key }}"
                                                    id="payment-form">
                                                    @csrf
                                             <div class="row">
                                                 <div class="col-12">
                                                     <input class="input" name="name" type="text" placeholder="{{__('Name')}}">
                                                 </div>
                                                 <div class="col-12">
                                                     <input class="input card-number" name="card_number" type="text" placeholder="{{__('Card Number')}}">
                                                 </div>
                                                 <div class="col-4">
                                                     <input class="input card-expiry-month" name="month" type="text" placeholder="{{__('Month')}}">
                                                 </div>
                                                 <div class="col-4">
                                                     <input class="input card-expiry-year" name="year" type="text" placeholder="{{__('Year')}}">
                                                 </div>

                                                 <div class="col-4 ms-auto">
                                                     <input class="input card-cvc" name="cvc" type="text" placeholder="{{__('CVC')}}">
                                                 </div>
                                             </div>

                                             <button type="submit" class="common_btn">{{__('Payment')}}</button>
                                         </form>
                                        </div>
                                    </div>
                                </div>
                                <div class='form-row row mt-4'>
                                    <div class='col-md-12 error d-none form-group '>
                                        <div class='alert-danger alert '>{{__('Please provide your valid card information')}}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="tab-pane fade" id="v-paypal-payment" role="tabpanel" aria-labelledby="v-paypal-payment-tab">
                                <a href="{{ route('user.checkout.pay-with-paypal') }}" type="submit" class="common_btn mt-4">{{__('Pay with paypal')}}</a>
                            </div>

                            <div class="tab-pane fade" id="v-razorpay-payment" role="tabpanel" aria-labelledby="v-razorpay-payment-tab">
                                <form action="{{ route('user.checkout.pay-with-razorpay') }}" method="POST" >
                                    @csrf
                                    @php
                                        $payable_amount = $total_price * $razorpay->currency_rate;
                                        $payable_amount = round($payable_amount, 2);
                                    @endphp
                                    <script src="https://checkout.razorpay.com/v1/checkout.js"
                                            data-key="{{ $razorpay->key }}"
                                            data-currency="{{ $razorpay->currency_code }}"
                                            data-amount= "{{ $payable_amount * 100 }}"
                                            data-buttontext="{{__('Pay')}} {{ $payable_amount }} {{ $razorpay->currency_code }}"
                                            data-name="{{ $razorpay->name }}"
                                            data-description="{{ $razorpay->description }}"
                                            data-image="{{ asset($razorpay->image) }}"
                                            data-prefill.name=""
                                            data-prefill.email=""
                                            data-theme.color="{{ $razorpay->color }}">
                                    </script>
                                </form>

                            </div>

                            <div class="tab-pane fade" id="v-flutterwave-payment" role="tabpanel" aria-labelledby="v-flutterwave-payment-tab">
                                <button type="button" onclick="makePayment()" class="common_btn mt-4">{{__('Payment with Flutterwave')}}</button>
                            </div>

                            <div class="tab-pane fade" id="v-mollie-payment" role="tabpanel" aria-labelledby="v-mollie-payment-tab">
                                <a href="{{ route('user.checkout.pay-with-mollie') }}" class="common_btn mt-4">{{__('Payment with Mollie')}}</a>
                            </div>

                            <div class="tab-pane fade" id="v-paystack-payment" role="tabpanel" aria-labelledby="v-paystack-payment-tab">
                                <button onclick="payWithPaystack()" type="button" class="common_btn mt-4">{{__('Payment with Paystack')}}</button>
                            </div>

                            <div class="tab-pane fade" id="v-instamojo-payment" role="tabpanel" aria-labelledby="v-instamojo-payment-tab">
                                <a href="{{ route('user.checkout.pay-with-instamojo') }}" class="common_btn mt-4">{{__('Payment with Instamojo')}}</a>
                            </div>

                            <div class="tab-pane fade" id="v-bank-payment" role="tabpanel" aria-labelledby="v-bank-payment-tab">

                                {!! nl2br(e($bankPayment->account_info)) !!}

                                <form class="wsus__input_area mt-3" action="{{ route('user.checkout.pay-with-bank') }}" method="POST">
                                    @csrf
                                    <textarea cols="3" rows="2" name="tnx_info" placeholder="{{__('Payment Information')}}" required></textarea>
                                    <button type="submit" class="common_btn mt-4">{{__('Submit Order')}}</button>
                                </form>
                            </div>




                            <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                <form class="wsus__input_area" action="{{ route('user.checkout.cash-on-delivery') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="common_btn mt-4">{{__('Submit Order')}}</button>
                                </form>
                            </div>
                          </div>
                    </div>


                    <div class="col-xl-4 col-lg-4">
                        <div class="wsus__pay_booking_summary" id="sticky_sidebar2">
                            <h5>{{__('Order Summary')}}</h5>
                            <p>{{__('subtotal')}}: <span>{{ $setting->currency_icon }}{{ $subTotal }}</span></p>
                            <p>{{__('shipping fee')}}(+): <span>{{ $setting->currency_icon }}{{ $shipping_fee }} </span></p>
                            <p>{{__('Tax')}}(+): <span>{{ $setting->currency_icon }}{{ $tax_amount }}</span></p>
                            <p>{{__('Coupon')}}(-): <span>{{ $setting->currency_icon }}{{  $coupon_price  }}</span></p>
                            <h6>{{__('total')}} <span>{{ $setting->currency_icon }}{{ $total_price }}</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--============================
        PAYMENT PAGE END
    ==============================-->


<script>

// start stripe payment
$(function() {
    var $form = $(".require-validation");
    $('form.require-validation').bind('submit', function(e) {
        var $form         = $(".require-validation"),
        inputSelector = ['input[type=email]', 'input[type=password]',
                            'input[type=text]', 'input[type=file]',
                            'textarea'].join(', '),
        $inputs       = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid         = true;
        $errorMessage.addClass('d-none');

        $('.has-error').removeClass('has-error');
        $inputs.each(function(i, el) {
            var $input = $(el);
            if ($input.val() === '') {
                $input.parent().addClass('has-error');
                $errorMessage.removeClass('d-none');
                e.preventDefault();
            }
        });

        if (!$form.data('cc-on-file')) {
        e.preventDefault();
        Stripe.setPublishableKey($form.data('stripe-publishable-key'));
        Stripe.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
        }, stripeResponseHandler);
        }

    });

    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('d-none')
                .find('.alert')
                .text(response.error.message);
        } else {
            var token = response['id'];
            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }
});
</script>
{{-- end stripe payment --}}

{{-- start flutterwave payment --}}
<script src="https://checkout.flutterwave.com/v3.js"></script>
@php
    $payable_amount = $total_price * $flutterwave->currency_rate;
    $payable_amount = round($payable_amount, 2);

@endphp

<script>
    function makePayment() {
      FlutterwaveCheckout({
        public_key: "{{ $flutterwave->public_key }}",
        tx_ref: "RX1",
        amount: {{ $payable_amount }},
        currency: "{{ $flutterwave->currency_code }}",
        country: "{{ $flutterwave->country_code }}",
        payment_options: " ",
        customer: {
          email: "{{ $user->email }}",
          phone_number: "{{ $user->phone }}",
          name: "{{ $user->name }}",
        },
        callback: function (data) {
            var tnx_id = data.transaction_id;
            var _token = "{{ csrf_token() }}";
            $.ajax({
                type: 'post',
                data : {tnx_id,_token},
                url: "{{ route('user.checkout.pay-with-flutterwave') }}",
                success: function (response) {
                    if(response.status == 'success'){
                        toastr.success(response.message);
                        window.location.href = "{{ route('user.order') }}";
                    }else{
                        toastr.error(response.message);
                        window.location.reload();
                    }
                },
                error: function(err) {}
            });

        },
        customizations: {
          title: "{{ $flutterwave->title }}",
          logo: "{{ asset($flutterwave->logo) }}",
        },
      });
    }
</script>
{{-- end flutterwave payment --}}


<script src="https://js.paystack.co/v1/inline.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
@php
    $public_key = $paystack->paystack_public_key;
    $currency = $paystack->paystack_currency_code;
    $currency = strtoupper($currency);

    $ngn_amount = $total_price * $paystack->paystack_currency_rate;
    $ngn_amount = $ngn_amount * 100;
    $ngn_amount = round($ngn_amount);
@endphp
<script>
function payWithPaystack(){
  var handler = PaystackPop.setup({
    key: '{{ $public_key }}',
    email: '{{ $user->email }}',
    amount: '{{ $ngn_amount }}',
    currency: "{{ $currency }}",
    callback: function(response){
      let reference = response.reference;
      let tnx_id = response.transaction;
      let _token = "{{ csrf_token() }}";
      $.ajax({
          type: "post",
          data: {reference, tnx_id, _token},
          url: "{{ route('user.checkout.pay-with-paystack') }}",
          success: function(response) {
            if(response.status == 'success'){
                window.location.href = "{{ route('user.order') }}";
            }else{
                window.location.reload();
            }
          }
      });
    },
    onClose: function(){
        alert('window closed');
    }
  });
  handler.openIframe();
}
</script>

@endsection
