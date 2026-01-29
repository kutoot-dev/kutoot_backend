<?php

namespace Database\Seeders;

use App\Models\PaymongoPayment;
use Illuminate\Database\Seeder;

class PaymongoPaymentSeeder extends Seeder
{
    public function run()
    {
        PaymongoPayment::firstOrCreate(
            ['id' => 1],
            [
                'public_key' => '',
                'secret_key' => '',
                'country_code' => 'PH',
                'currency_code' => 'PHP',
                'currency_rate' => 1.00,
                'status' => 0,
            ]
        );

        $this->command->info('PayMongo payment settings seeded.');
    }
}
