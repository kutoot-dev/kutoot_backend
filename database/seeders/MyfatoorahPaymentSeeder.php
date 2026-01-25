<?php

namespace Database\Seeders;

use App\Models\MyfatoorahPayment;
use Illuminate\Database\Seeder;

class MyfatoorahPaymentSeeder extends Seeder
{
    public function run()
    {
        MyfatoorahPayment::firstOrCreate(
            ['id' => 1],
            [
                'api_key' => '',
                'account_mode' => 'sandbox',
                'status' => 0,
            ]
        );

        $this->command->info('MyFatoorah payment settings seeded.');
    }
}
