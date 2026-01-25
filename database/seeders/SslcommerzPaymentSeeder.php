<?php

namespace Database\Seeders;

use App\Models\SslcommerzPayment;
use Illuminate\Database\Seeder;

class SslcommerzPaymentSeeder extends Seeder
{
    public function run()
    {
        SslcommerzPayment::firstOrCreate(
            ['id' => 1],
            [
                'store_id' => '',
                'store_password' => '',
                'mode' => 'sandbox',
                'status' => 0,
            ]
        );

        $this->command->info('SSLCommerz payment settings seeded.');
    }
}
