<?php

namespace Database\Seeders;

use App\Models\PaypalPayment;
use App\Models\StripePayment;
use App\Models\RazorpayPayment;
use App\Models\Flutterwave;
use App\Models\BankPayment;
use App\Models\PaystackAndMollie;
use App\Models\InstamojoPayment;
use App\Models\PaymongoPayment;
use App\Models\SslcommerzPayment;
use App\Models\MyfatoorahPayment;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        // PayPal
        PaypalPayment::firstOrCreate(['id' => 1], [
            'client_id' => '',
            'secret_id' => '',
            'account_mode' => 'sandbox',
            'status' => 0,
        ]);

        // Stripe
        StripePayment::firstOrCreate(['id' => 1], [
            'stripe_key' => '',
            'stripe_secret' => '',
            'status' => 0,
        ]);

        // Razorpay
        RazorpayPayment::firstOrCreate(['id' => 1], [
            'key' => '',
            'secret_key' => '',
            'status' => 0,
        ]);

        // Flutterwave
        Flutterwave::firstOrCreate(['id' => 1], [
            'public_key' => '',
            'secret_key' => '',
            'status' => 0,
        ]);

        // Bank Payment
        BankPayment::firstOrCreate(['id' => 1], [
            'status' => 0,
        ]);

        // Paystack and Mollie
        PaystackAndMollie::firstOrCreate(['id' => 1], [
            'paystact_public_key' => '',
            'paystact_secret_key' => '',
            'paystact_marchant_email' => '',
            'mollie_key' => '',
            'paystack_status' => 0,
            'mollie_status' => 0,
        ]);

        // Instamojo
        InstamojoPayment::firstOrCreate(['id' => 1], [
            'api_key' => '',
            'auth_token' => '',
            'account_mode' => 'sandbox',
            'status' => 0,
        ]);

        // PayMongo
        PaymongoPayment::firstOrCreate(['id' => 1], [
            'public_key' => '',
            'secret_key' => '',
            'country_code' => 'PH',
            'currency_code' => 'PHP',
            'currency_rate' => 1.00,
            'status' => 0,
        ]);

        // SSLCommerz
        SslcommerzPayment::firstOrCreate(['id' => 1], [
            'store_id' => '',
            'store_password' => '',
            'mode' => 'sandbox',
            'status' => 0,
        ]);

        // MyFatoorah
        MyfatoorahPayment::firstOrCreate(['id' => 1], [
            'api_key' => '',
            'account_mode' => 'sandbox',
            'status' => 0,
        ]);

        $this->command->info('All payment methods seeded with default values.');
    }
}
