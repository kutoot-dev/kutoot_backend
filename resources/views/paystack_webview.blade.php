<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Instamojo Payment</title>
</head>
<body>
    <p style="text-align: center">Please wait. Your payment is processing....</p>
<p style="text-align: center">Do not press browser back or forward button while you are in payment page</p>

<script src="{{ asset('user/js/jquery-3.6.0.min.js') }}"></script>

<script src="https://js.paystack.co/v1/inline.js"></script>

{{-- <button onclick="payWithPaystack()">click me</button> --}}


@php
    $public_key = $paystack->paystack_public_key;
    $currency = $paystack->paystack_currency_code;
    $currency = strtoupper($currency);

    $ngn_amount = $total_price * $paystack->paystack_currency_rate;
    $ngn_amount = $ngn_amount * 100;
    $ngn_amount = round($ngn_amount);
@endphp
<script>
    payWithPaystack();
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


        let frontend_success_url = "{{ $frontend_success_url }}";
        let frontend_faild_url = "{{ $frontend_faild_url }}";
        let request_from = "{{ $request_from }}";
        let shipping_address_id = "{{ $shipping_address_id }}";
        let billing_address_id = "{{ $billing_address_id }}";
        let shipping_method_id = "{{ $shipping_method_id }}";
        let coupon = "{{ $coupon }}";
        let token = "{{ $token }}";


      $.ajax({
          type: "post",
          data: {reference, tnx_id, _token, shipping_address_id, billing_address_id, shipping_method_id, coupon, token},
          url: "{{ route('user.checkout.pay-with-paystack') }}",
          success: function(response) {
            if(response.status == 'success'){
                let order_id = response.order_id;
                if(request_from == 'react_web'){
                    let success_url = frontend_success_url+"/"+ order_id;
                    window.location.href = success_url;
                }else{
                    let mobile_success_url = "{{ route('user.checkout.order-success-url-for-mobile-app') }}";
                    window.location.href = mobile_success_url;
                }


            }else{
                if(request_from == 'react_web'){
                    window.location.href = frontend_faild_url;
                }else{
                    let mobile_faild_url = "{{ route('user.checkout.order-fail-url-for-mobile-app') }}";
                    window.location.href = mobile_faild_url;
                }

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
</body>
</html>
