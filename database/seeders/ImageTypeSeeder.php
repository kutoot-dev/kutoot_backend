<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ImageType;
use App\Models\ImageItem;
use App\Helpers\ImageSeederHelper;

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

        // Seed image items for each type
        $this->seedImageItems();
    }

    /**
     * Seed sample image items for Banner and partners types with real images.
     */
    private function seedImageItems()
    {
        $bannerType = ImageType::where('name', 'Banner')->first();
        $partnersType = ImageType::where('name', 'partners')->first();

        // Banner items with Lorem Picsum images
        $bannerItems = [
            ['title' => 'Main Banner', 'description' => 'Homepage main banner image', 'picsumId' => 1025],
            ['title' => 'Promo Banner', 'description' => 'Promotional banner for special offers', 'picsumId' => 1029],
            ['title' => 'Sale Banner', 'description' => 'Flash sale announcement banner', 'picsumId' => 1031],
        ];

        // Partners items with Lorem Picsum images (using logo type for partner logos)
        $partnersItems = [
            ['title' => 'Partner 1', 'description' => 'Strategic partner logo', 'picsumId' => 866],
            ['title' => 'Partner 2', 'description' => 'Business partner logo', 'picsumId' => 883],
            ['title' => 'Partner 3', 'description' => 'Technology partner logo', 'picsumId' => 890],
            ['title' => 'Partner 4', 'description' => 'Corporate partner logo', 'picsumId' => 906],
        ];

        // Seed banners with downloaded images
        if ($bannerType) {
            $count = 0;
            foreach ($bannerItems as $item) {
                $existing = ImageItem::where('title', $item['title'])
                    ->where('image_type_id', $bannerType->id)
                    ->first();

                if (!$existing) {
                    $imagePath = ImageSeederHelper::ensureImage(
                        'images',
                        'banner-' . strtolower(str_replace(' ', '-', $item['title'])),
                        'banner',
                        $item['picsumId']
                    );

                    ImageItem::create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'image_type_id' => $bannerType->id,
                        'image_path' => $imagePath,
                    ]);
                    $count++;
                }
            }
            $this->command->info("Banner image items seeded: {$count} (with HD images from Lorem Picsum)");
        }

        // Seed partners with downloaded images
        if ($partnersType) {
            $count = 0;
            foreach ($partnersItems as $item) {
                $existing = ImageItem::where('title', $item['title'])
                    ->where('image_type_id', $partnersType->id)
                    ->first();

                if (!$existing) {
                    $imagePath = ImageSeederHelper::ensureImage(
                        'images',
                        'partner-' . strtolower(str_replace(' ', '-', $item['title'])),
                        'logo',
                        $item['picsumId']
                    );

                    ImageItem::create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'image_type_id' => $partnersType->id,
                        'image_path' => $imagePath,
                    ]);
                    $count++;
                }
            }
            $this->command->info("Partners image items seeded: {$count} (with HD images from Lorem Picsum)");
        }
    }
}
