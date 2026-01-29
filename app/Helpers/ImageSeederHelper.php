<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

/**
 * ImageSeederHelper - Helper for seeding HD ecommerce images from Picsum/Lorem Picsum
 *
 * Downloads images from Lorem Picsum (free, high-quality stock photos) and
 * optimizes them using Intervention Image with WebP conversion.
 */
class ImageSeederHelper
{
    /**
     * Default quality for JPEG/WebP compression (1-100)
     */
    protected static int $defaultQuality = 80;

    /**
     * Maximum dimensions for different image types (matches ImageHelper)
     */
    protected static array $dimensions = [
        'thumbnail' => ['width' => 400, 'height' => 400],
        'product' => ['width' => 800, 'height' => 800],
        'banner' => ['width' => 1920, 'height' => 600],
        'banner_tablet' => ['width' => 1024, 'height' => 400],
        'banner_mobile' => ['width' => 640, 'height' => 320],
        'logo' => ['width' => 300, 'height' => 100],
        'avatar' => ['width' => 200, 'height' => 200],
        'gallery' => ['width' => 1200, 'height' => 1200],
        'slider' => ['width' => 1920, 'height' => 800],
        'slider_tablet' => ['width' => 1024, 'height' => 500],
        'slider_mobile' => ['width' => 640, 'height' => 400],
        'brand' => ['width' => 200, 'height' => 200],
        'icon' => ['width' => 100, 'height' => 100],
        'category' => ['width' => 400, 'height' => 400],
        'service' => ['width' => 100, 'height' => 100],
        'testimonial' => ['width' => 200, 'height' => 200],
        'favicon' => ['width' => 32, 'height' => 32],
        'default' => ['width' => 1200, 'height' => 1200],
    ];

    /**
     * Picsum image collections for different categories (photo IDs from Lorem Picsum)
     */
    protected static array $picsumCategories = [
        'slider' => [1011, 1012, 1013, 1015, 1016, 1018, 1019, 1021, 1022, 1024],
        'banner' => [1025, 1029, 1031, 1033, 1035, 1037, 1038, 1040, 1041, 1043],
        'product' => [0, 1, 10, 20, 26, 27, 28, 29, 30, 42, 43, 48, 49, 60],
        'category' => [180, 200, 225, 250, 275, 292, 299, 319, 335, 360],
        'brand' => [500, 501, 502, 503, 504, 505, 506, 507, 508, 509],
        'avatar' => [64, 65, 91, 175, 177, 219, 275, 334, 338, 342],
        'service' => [180, 181, 182, 183, 184, 185, 186, 187, 188, 189],
        'icon' => [200, 201, 202, 203, 204, 205, 206, 207, 208, 209],
        'logo' => [866, 883, 890, 906, 919, 924, 943, 949, 984, 1026],
    ];

    /**
     * Download and save an optimized image from Lorem Picsum
     *
     * @param string $folder Subfolder within public/uploads/
     * @param string $prefix Filename prefix
     * @param string $type Image type (slider, banner, product, etc.)
     * @param int|null $picsumId Specific Picsum photo ID (random from category if null)
     * @param int|null $quality Compression quality (1-100)
     * @return string|null Relative path to saved image or null on failure
     */
    public static function download(
        string $folder = 'seeder-images',
        string $prefix = 'image',
        string $type = 'default',
        ?int $picsumId = null,
        ?int $quality = null
    ): ?string {
        $quality = $quality ?? self::$defaultQuality;
        $dimensions = self::$dimensions[$type] ?? self::$dimensions['default'];

        // Get random photo ID from category if not specified
        if ($picsumId === null) {
            $categoryIds = self::$picsumCategories[$type] ?? self::$picsumCategories['product'];
            $picsumId = $categoryIds[array_rand($categoryIds)];
        }

        // Build Picsum URL with dimensions
        $url = "https://picsum.photos/id/{$picsumId}/{$dimensions['width']}/{$dimensions['height']}";

        try {
            // Download image with timeout
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                // Fallback to random image if specific ID fails
                $fallbackUrl = "https://picsum.photos/{$dimensions['width']}/{$dimensions['height']}";
                $response = Http::timeout(30)->get($fallbackUrl);

                if (!$response->successful()) {
                    return null;
                }
            }

            $imageContent = $response->body();

            // Generate unique filename
            $filename = Str::slug($prefix) . '-' . date('Y-m-d-H-i-s') . '-' . rand(1000, 9999) . '.webp';

            // Ensure directory exists
            $directory = public_path("uploads/{$folder}");
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            $relativePath = "uploads/{$folder}/{$filename}";
            $fullPath = public_path($relativePath);

            // Create image, resize if needed, optimize, and save as WebP
            $image = Image::make($imageContent);

            // Resize maintaining aspect ratio (only downsize, never upscale)
            $image->resize($dimensions['width'], $dimensions['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Save with WebP compression for best file size
            $image->encode('webp', $quality)->save($fullPath);

            return $relativePath;

        } catch (\Exception $e) {
            Log::warning("ImageSeederHelper: Failed to download image - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Download and create responsive versions of an image (desktop, tablet, mobile)
     *
     * @param string $folder Subfolder within public/uploads/
     * @param string $prefix Filename prefix
     * @param string $baseType Base image type (banner, slider)
     * @param int|null $picsumId Specific Picsum photo ID
     * @param int|null $quality Compression quality (1-100)
     * @return array Array of paths ['desktop' => path, 'tablet' => path, 'mobile' => path] or empty array on failure
     */
    public static function downloadResponsive(
        string $folder = 'seeder-images',
        string $prefix = 'image',
        string $baseType = 'banner',
        ?int $picsumId = null,
        ?int $quality = null
    ): array {
        $quality = $quality ?? self::$defaultQuality;
        $paths = [];

        // Get photo ID
        if ($picsumId === null) {
            $categoryIds = self::$picsumCategories[$baseType] ?? self::$picsumCategories['banner'];
            $picsumId = $categoryIds[array_rand($categoryIds)];
        }

        // Download a large source image first
        $sourceUrl = "https://picsum.photos/id/{$picsumId}/1920/1080";

        try {
            $response = Http::timeout(30)->get($sourceUrl);

            if (!$response->successful()) {
                return [];
            }

            $imageContent = $response->body();

            // Generate base filename
            $baseFilename = Str::slug($prefix) . '-' . date('Y-m-d-H-i-s') . '-' . rand(1000, 9999);

            // Ensure directory exists
            $directory = public_path("uploads/{$folder}");
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            // Define responsive sizes based on base type
            $sizes = [
                'desktop' => self::$dimensions[$baseType] ?? self::$dimensions['default'],
                'tablet' => self::$dimensions[$baseType . '_tablet'] ?? ['width' => 1024, 'height' => 500],
                'mobile' => self::$dimensions[$baseType . '_mobile'] ?? ['width' => 640, 'height' => 400],
            ];

            // Create each responsive version
            foreach ($sizes as $breakpoint => $dimensions) {
                $suffix = $breakpoint === 'desktop' ? '' : "_{$breakpoint}";
                $filename = $baseFilename . $suffix . '.webp';
                $relativePath = "uploads/{$folder}/{$filename}";
                $fullPath = public_path($relativePath);

                // Create fresh image instance for each size
                $image = Image::make($imageContent);

                // Resize maintaining aspect ratio
                $image->resize($dimensions['width'], $dimensions['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // Save with WebP compression
                $image->encode('webp', $quality)->save($fullPath);

                $paths[$breakpoint] = $relativePath;
            }

            return $paths;

        } catch (\Exception $e) {
            Log::warning("ImageSeederHelper: Failed to download responsive images - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate a solid color placeholder image (useful as fallback when network fails)
     *
     * @param string $folder Subfolder within public/uploads/
     * @param string $prefix Filename prefix
     * @param string $type Image type for dimensions
     * @param string|null $bgColor Background color (hex without #)
     * @param string|null $text Text to display on image
     * @return string Relative path to saved image
     */
    public static function placeholder(
        string $folder = 'seeder-images',
        string $prefix = 'placeholder',
        string $type = 'default',
        ?string $bgColor = null,
        ?string $text = null
    ): string {
        $dimensions = self::$dimensions[$type] ?? self::$dimensions['default'];
        $bgColor = $bgColor ?? self::randomPastelColor();
        $text = $text ?? strtoupper($type);

        // Generate unique filename
        $filename = Str::slug($prefix) . '-' . date('Y-m-d-H-i-s') . '-' . rand(1000, 9999) . '.webp';

        // Ensure directory exists
        $directory = public_path("uploads/{$folder}");
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $relativePath = "uploads/{$folder}/{$filename}";
        $fullPath = public_path($relativePath);

        // Create canvas with background color
        $image = Image::canvas($dimensions['width'], $dimensions['height'], $bgColor);

        // Add centered text
        $fontSize = min($dimensions['width'], $dimensions['height']) / 10;
        $image->text($text, $dimensions['width'] / 2, $dimensions['height'] / 2, function ($font) use ($fontSize) {
            $font->size($fontSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        // Save as WebP
        $image->encode('webp', 80)->save($fullPath);

        return $relativePath;
    }

    /**
     * Generate a random pastel color
     */
    protected static function randomPastelColor(): string
    {
        $colors = [
            '#FFB3BA', '#FFDFBA', '#FFFFBA', '#BAFFC9', '#BAE1FF',
            '#E0BBE4', '#957DAD', '#D291BC', '#FEC8D8', '#FFDFD3',
            '#B5EAD7', '#C7CEEA', '#FF9AA2', '#FFB7B2', '#FFDAC1',
        ];
        return $colors[array_rand($colors)];
    }

    /**
     * Download or create placeholder (falls back to placeholder on network failure)
     */
    public static function ensureImage(
        string $folder = 'seeder-images',
        string $prefix = 'image',
        string $type = 'default',
        ?int $picsumId = null
    ): string {
        $path = self::download($folder, $prefix, $type, $picsumId);

        if ($path === null) {
            $path = self::placeholder($folder, $prefix, $type);
        }

        return $path;
    }

    /**
     * Download or create responsive placeholders (falls back to placeholders on network failure)
     */
    public static function ensureResponsiveImages(
        string $folder = 'seeder-images',
        string $prefix = 'image',
        string $baseType = 'banner',
        ?int $picsumId = null
    ): array {
        $paths = self::downloadResponsive($folder, $prefix, $baseType, $picsumId);

        if (empty($paths)) {
            // Create placeholders for each breakpoint
            $paths = [
                'desktop' => self::placeholder($folder, $prefix . '-desktop', $baseType),
                'tablet' => self::placeholder($folder, $prefix . '-tablet', $baseType . '_tablet'),
                'mobile' => self::placeholder($folder, $prefix . '-mobile', $baseType . '_mobile'),
            ];
        }

        return $paths;
    }

    /**
     * Check if an image exists
     */
    public static function exists(?string $path): bool
    {
        return $path && File::exists(public_path($path));
    }

    /**
     * Get dimensions for a type
     */
    public static function getDimensions(string $type): array
    {
        return self::$dimensions[$type] ?? self::$dimensions['default'];
    }

    /**
     * Set default quality
     */
    public static function setDefaultQuality(int $quality): void
    {
        self::$defaultQuality = max(1, min(100, $quality));
    }
}
