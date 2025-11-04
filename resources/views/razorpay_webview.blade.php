<p style="text-align: center">Please wait. Your payment is processing....</p>
<p style="text-align: center">Do not press browser back or forward button while you are in payment page</p>



<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<form name='razorpayform' action="{{ route('user.checkout.razorpay-pay-verify') }}" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $orderId }}">
    <input type="hidden" name="token" value="{{ $token }}">


    <input type="hidden" name="frontend_success_url" value="{{ $frontend_success_url }}">
    <input type="hidden" name="frontend_faild_url" value="{{ $frontend_faild_url }}">
    <input type="hidden" name="request_from" value="{{ $request_from }}">
    <input type="hidden" name="shipping_address_id" value="{{ $shipping_address_id }}">
    <input type="hidden" name="billing_address_id" value="{{ $billing_address_id }}">
    <input type="hidden" name="shipping_method_id" value="{{ $shipping_method_id }}">
    <input type="hidden" name="reedem_coins" value="{{ $reedem_coins }}">
    <input type="hidden" name="coupon" value="{{ $coupon }}">
</form>
<script>
 var options = {
   "key": "{{ $razorpay->key }}",
   "amount": "{{ $payable_amount }}",
   "currency": "{{ $razorpay->currency_code }}",
   "name": "{{ $razorpay->name }}",
   "description": "{{ $razorpay->description }}",
   "image": "{{ asset($razorpay->image) }}",
   "order_id": "{{ $orderId }}",
   "handler": function(response) {
     alert(response.razorpay_payment_id);
     alert(response.razorpay_order_id);
     alert(response.razorpay_signature)
   },
   "theme": {
     "color": "{{ $razorpay->color }}"
   }
 };
 options.handler = function(response) {
   document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
   document.getElementById('razorpay_signature').value = response.razorpay_signature;
   document.razorpayform.submit();
 };
 options.theme.image_padding = false;
 options.modal = {
   ondismiss: function() {
    //  window.location.href = "/pay?payment=false"
    console.log('dismiss');

   },
   escape: true,
   backdropclose: false
 };
 var rzp1 = new Razorpay(options);
 rzp1.on('payment.failed', function(response) {
   alert(response.error.code);
   alert(response.error.description);
   alert(response.error.source);
   alert(response.error.step);
   alert(response.error.reason);
   alert(response.error.metadata.order_id);
   alert(response.error.metadata.payment_id);
 });
 rzp1.open();
</script>
