<?php

namespace Database\Seeders;

use App\Models\Store\SellerApplication;
use Illuminate\Database\Seeder;

/**
 * MasterSettingsSeeder - Seeds default commission and discount settings.
 *
 * Updates approved seller applications with default commission/discount configurations.
 */
class MasterSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'commission_percent' => 6,
            'discount_percent' => 10,
            'min_bill_amount' => 1000,
            'last_updated_on' => now()->toDateString(),
        ];

        $appCount = 0;

        // Update all approved seller applications with default settings
        SellerApplication::query()
            ->where('status', 'APPROVED')
            ->select('id')
            ->chunkById(200, function ($applications) use ($defaults, &$appCount) {
                foreach ($applications as $app) {
                    SellerApplication::query()
                        ->where('id', $app->id)
                        ->update($defaults);
                    $appCount++;
                }
            });

        $this->command->info("MasterSettingsSeeder completed. {$appCount} seller application settings updated.");
    }
}
