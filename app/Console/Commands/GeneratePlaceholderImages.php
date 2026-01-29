<?php

namespace App\Console\Commands;

use App\Helpers\ImageSeederHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class GeneratePlaceholderImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:generate-placeholders {--force : Force regenerate existing placeholders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate placeholder images for fallbacks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating placeholder images...');

        $directory = public_path('images');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        // Generate main placeholder
        $placeholderPath = public_path('images/placeholder.webp');

        if (!File::exists($placeholderPath) || $this->option('force')) {
            $image = Image::canvas(800, 600, '#f0f0f0');
            $image->text('Image Not Available', 400, 300, function ($font) {
                $font->size(36);
                $font->color('#999999');
                $font->align('center');
                $font->valign('middle');
            });
            $image->encode('webp', 80)->save($placeholderPath);
            $this->line('  ✓ Created placeholder.webp');
        }

        // Generate type-specific placeholders
        $types = [
            'product' => ['width' => 800, 'height' => 800, 'text' => 'Product Image'],
            'banner' => ['width' => 1920, 'height' => 600, 'text' => 'Banner'],
            'slider' => ['width' => 1920, 'height' => 800, 'text' => 'Slider'],
            'category' => ['width' => 400, 'height' => 400, 'text' => 'Category'],
            'brand' => ['width' => 200, 'height' => 200, 'text' => 'Brand'],
            'avatar' => ['width' => 200, 'height' => 200, 'text' => 'Avatar'],
            'icon' => ['width' => 100, 'height' => 100, 'text' => 'Icon'],
            'thumbnail' => ['width' => 400, 'height' => 400, 'text' => 'Thumbnail'],
        ];

        foreach ($types as $type => $config) {
            $path = public_path("images/placeholder-{$type}.webp");

            if (!File::exists($path) || $this->option('force')) {
                $image = Image::canvas($config['width'], $config['height'], '#e8e8e8');
                $fontSize = min($config['width'], $config['height']) / 10;
                $image->text($config['text'], $config['width'] / 2, $config['height'] / 2, function ($font) use ($fontSize) {
                    $font->size($fontSize);
                    $font->color('#888888');
                    $font->align('center');
                    $font->valign('middle');
                });
                $image->encode('webp', 80)->save($path);
                $this->line("  ✓ Created placeholder-{$type}.webp");
            }
        }

        $this->info('Placeholder images generated successfully!');

        return Command::SUCCESS;
    }
}
