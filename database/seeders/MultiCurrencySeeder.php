<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MultiCurrency;

/**
 * MultiCurrencySeeder - Seeds multi-currency data for the admin panel.
 *
 * DEV ONLY: Creates common currencies with exchange rates.
 */
class MultiCurrencySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding multi-currency data...');

        $currencies = [
            [
                'id' => 1,
                'currency_name' => 'Indian Rupee',
                'country_code' => 'IN',
                'currency_code' => 'INR',
                'currency_icon' => '₹',
                'currency_rate' => 1.0000,
                'is_default' => 'Yes',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 2,
                'currency_name' => 'US Dollar',
                'country_code' => 'US',
                'currency_code' => 'USD',
                'currency_icon' => '$',
                'currency_rate' => 0.0120,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 3,
                'currency_name' => 'Euro',
                'country_code' => 'EU',
                'currency_code' => 'EUR',
                'currency_icon' => '€',
                'currency_rate' => 0.0110,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 4,
                'currency_name' => 'British Pound',
                'country_code' => 'GB',
                'currency_code' => 'GBP',
                'currency_icon' => '£',
                'currency_rate' => 0.0095,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 5,
                'currency_name' => 'Japanese Yen',
                'country_code' => 'JP',
                'currency_code' => 'JPY',
                'currency_icon' => '¥',
                'currency_rate' => 1.7800,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 6,
                'currency_name' => 'Australian Dollar',
                'country_code' => 'AU',
                'currency_code' => 'AUD',
                'currency_icon' => 'A$',
                'currency_rate' => 0.0183,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 7,
                'currency_name' => 'Canadian Dollar',
                'country_code' => 'CA',
                'currency_code' => 'CAD',
                'currency_icon' => 'C$',
                'currency_rate' => 0.0163,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 8,
                'currency_name' => 'UAE Dirham',
                'country_code' => 'AE',
                'currency_code' => 'AED',
                'currency_icon' => 'د.إ',
                'currency_rate' => 0.0441,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 9,
                'currency_name' => 'Singapore Dollar',
                'country_code' => 'SG',
                'currency_code' => 'SGD',
                'currency_icon' => 'S$',
                'currency_rate' => 0.0161,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
            [
                'id' => 10,
                'currency_name' => 'Bangladeshi Taka',
                'country_code' => 'BD',
                'currency_code' => 'BDT',
                'currency_icon' => '৳',
                'currency_rate' => 1.3200,
                'is_default' => 'No',
                'currency_position' => 'left',
                'status' => 1,
            ],
        ];

        foreach ($currencies as $currency) {
            MultiCurrency::updateOrCreate(
                ['id' => $currency['id']],
                $currency
            );

            $default = $currency['is_default'] === 'Yes' ? ' (Default)' : '';
            $this->command->line("  ✓ {$currency['currency_name']} ({$currency['currency_code']}){$default}");
        }

        $this->command->info('MultiCurrencySeeder completed. ' . count($currencies) . ' currencies seeded.');
    }
}
