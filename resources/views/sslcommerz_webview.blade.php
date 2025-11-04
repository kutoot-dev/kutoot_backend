<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>




<div class="tab-pane fade" id="v-sslcommerz-payment" role="tabpanel" aria-labelledby="v-sslcommerz-payment-tab">
    <button class="btn btn-primary btn-lg btn-block" id="sslczPayBtn"
        token="if you have any token validation" postdata=""
        order="{{json_encode([
            'amount' => $total_price,
            'cus_email' => 'johndoe@gmail.com',
            'cus_phone' => '123456789',
            'cus_name' => 'Ibrahim Khalil',
            'currency' => 'BDT',
        ]) }}"
        endpoint="{{ route('user.checkout.sslcommerz-pay',['token' => $token]) }}"> SslCommerz
    </button>
</div>



@if ($sslcommerzPaymentInfo->mode == 'sandbox')
    <script>
        (function(window, document) {
            var loader = function() {
                var script = document.createElement("script"),
                    tag = document.getElementsByTagName("script")[0];
                script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(
                    7);
                tag.parentNode.insertBefore(script, tag);
            };

            window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload",
                loader);
        })(window, document);
    </script>
@else
    <script>
        (function (window, document) {
            var loader = function () {
                var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
                script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
                tag.parentNode.insertBefore(script, tag);
            };

            window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
        })(window, document);
    </script>
@endif
</body>
</html>
