<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\Sponsor;
use Illuminate\Database\Seeder;

/**
 * SponsorSeeder - Seeds sponsors and partners with HD logos.
 *
 * DEV ONLY: Creates sample sponsors with optimized WebP logos.
 */
class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing sponsor logos...');

        $sponsors = [
            [
                'id' => 1,
                'name' => 'TechCorp Solutions',
                'type' => 'Sponsor',
                'link' => 'https://example.com/techcorp',
                'serial' => 1,
                'status' => 1,
                'picsum_id' => 1005,
            ],
            [
                'id' => 2,
                'name' => 'Global Finance Partners',
                'type' => 'Partner',
                'link' => 'https://example.com/globalfinance',
                'serial' => 2,
                'status' => 1,
                'picsum_id' => 1006,
            ],
            [
                'id' => 3,
                'name' => 'InnovateTech Labs',
                'type' => 'Sponsor',
                'link' => 'https://example.com/innovate',
                'serial' => 3,
                'status' => 1,
                'picsum_id' => 1008,
            ],
            [
                'id' => 4,
                'name' => 'EcoGreen Industries',
                'type' => 'Partner',
                'link' => 'https://example.com/ecogreen',
                'serial' => 4,
                'status' => 1,
                'picsum_id' => 1011,
            ],
            [
                'id' => 5,
                'name' => 'Digital Media Group',
                'type' => 'Sponsor',
                'link' => 'https://example.com/digitalmedia',
                'serial' => 5,
                'status' => 1,
                'picsum_id' => 1012,
            ],
            [
                'id' => 6,
                'name' => 'Sunrise Ventures',
                'type' => 'Partner',
                'link' => 'https://example.com/sunrise',
                'serial' => 6,
                'status' => 1,
                'picsum_id' => 1018,
            ],
        ];

        foreach ($sponsors as $sponsor) {
            // Download logo image
            $logoPath = ImageSeederHelper::ensureImage(
                'sponsors',
                'sponsor-' . $sponsor['id'] . '-logo',
                'logo',
                $sponsor['picsum_id']
            );

            // Download banner image
            $bannerPath = ImageSeederHelper::ensureImage(
                'sponsors',
                'sponsor-' . $sponsor['id'] . '-banner',
                'banner',
                $sponsor['picsum_id'] + 100
            );

            Sponsor::updateOrCreate(
                ['id' => $sponsor['id']],
                [
                    'name' => $sponsor['name'],
                    'type' => $sponsor['type'],
                    'logo' => $logoPath,
                    'banner' => $bannerPath,
                    'link' => $sponsor['link'],
                    'serial' => $sponsor['serial'],
                    'status' => $sponsor['status'],
                ]
            );

            $this->command->line("  ✓ Sponsor #{$sponsor['id']}: {$sponsor['name']} ({$sponsor['type']})");
        }

        $this->command->info('SponsorSeeder completed. ' . count($sponsors) . ' sponsors seeded with HD logos.');
    }
}
