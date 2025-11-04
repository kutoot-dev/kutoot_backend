<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pay with paypal</title>
    <link rel="stylesheet" href="{{ asset('user/css/bootstrap.min.css') }}">
    <script src="{{ asset('user/js/jquery-3.6.0.min.js') }}"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">

                <form action="{{ route('user.checkout.pay-with-paypal-from-react') }}" id="paypalForm">
                    <input type="hidden" name="billing_address_id" value="{{ $billing_address_id }}">
                    <input type="hidden" name="shipping_address_id" value="{{ $shipping_address_id }}">
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="shipping_method_id" value="{{ $shipping_method_id }}">


                </form>
                {{-- <a href="{{ route('user.checkout.pay-with-paypal',
                [
                    'billing_address_id' => $billing_address_id,
                    'shipping_address_id' => $shipping_address_id,
                    'token' => $token,
                    'shipping_method_id' => $shipping_method_id
                ]
                ) }}" type="submit" id="paypalBtn" class="btn btn-primary">{{__('Pay with paypal')}}</a> --}}

                {{-- <button id="alertBtn" onclick="alert('hi')"></button> --}}
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $("#paypalForm").submit();
        });

    </script>
</body>
</html>

