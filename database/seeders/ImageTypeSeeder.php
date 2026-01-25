<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ImageType;

class ImageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'Banner',
            'partners',
        ];

        foreach ($types as $type) {
            ImageType::firstOrCreate(['name' => $type]);
        }

        $this->command->info('Image types seeded: ' . implode(', ', $types));
    }
}
