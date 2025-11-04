<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pay with paypal</title>
    <link rel="stylesheet" href="{{ asset('user/css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('user.checkout.pay-with-paypal',
                [
                    'billing_address_id' => $billing_address_id,
                    'shipping_address_id' => $shipping_address_id,
                    'token' => $token,
                    'shipping_method_id' => $shipping_method_id
                ]
                ) }}" type="submit" class="btn btn-primary">{{__('Pay with paypal')}}</a>

            </div>
        </div>
    </div>
</body>
</html>

