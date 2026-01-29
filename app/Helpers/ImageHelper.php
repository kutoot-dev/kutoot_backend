<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Default quality for JPEG/WebP compression (1-100)
     */
    protected static int $defaultQuality = 80;

    /**
     * Maximum dimensions for different image types
     */
    protected static array $maxDimensions = [
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
        'default' => ['width' => 1200, 'height' => 1200],
    ];

    /**
     * Responsive breakpoints for banner/slider images
     */
    protected static array $responsiveBreakpoints = [
        'desktop' => ['width' => 1920, 'suffix' => ''],
        'tablet' => ['width' => 1024, 'suffix' => '_tablet'],
        'mobile' => ['width' => 640, 'suffix' => '_mobile'],
    ];

    /**
     * Upload and optimize an image
     *
     * @param UploadedFile $file The uploaded file
     * @param string $folder Subfolder within uploads/
     * @param string $prefix Filename prefix
     * @param string $type Image type for sizing (thumbnail, product, banner, etc.)
     * @param int|null $quality Compression quality (1-100)
     * @param string|null $oldImage Path to old image to delete
     * @param bool $convertToWebp Convert to WebP format for better compression
     * @return string Relative path to saved image
     */
    public static function upload(
        UploadedFile $file,
        string $folder = 'custom-images',
        string $prefix = 'image',
        string $type = 'default',
        ?int $quality = null,
        ?string $oldImage = null,
        bool $convertToWebp = true
    ): string {
        $quality = $quality ?? self::$defaultQuality;
        $dimensions = self::$maxDimensions[$type] ?? self::$maxDimensions['default'];

        // Generate unique filename
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::slug($prefix) . '-' . date('Y-m-d-H-i-s') . '-' . rand(1000, 9999);

        // Convert to WebP for better compression (for jpg, jpeg, png)
        $saveAsWebp = $convertToWebp && in_array($extension, ['jpg', 'jpeg', 'png']);
        $finalExtension = $saveAsWebp ? 'webp' : $extension;
        $filename .= '.' . $finalExtension;

        // Ensure directory exists
        $directory = public_path("uploads/{$folder}");
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $relativePath = "uploads/{$folder}/{$filename}";
        $fullPath = public_path($relativePath);

        // Create image, resize if needed, optimize, and save
        $image = Image::make($file);

        // Resize maintaining aspect ratio (only downsize, never upscale)
        $image->resize($dimensions['width'], $dimensions['height'], function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Save with compression
        if ($saveAsWebp) {
            $image->encode('webp', $quality)->save($fullPath);
        } else {
            $image->save($fullPath, $quality);
        }

        // Delete old image if provided
        if ($oldImage) {
            self::delete($oldImage);
        }

        return $relativePath;
    }

    /**
     * Upload and create responsive versions of an image (desktop, tablet, mobile)
     *
     * @param UploadedFile $file The uploaded file
     * @param string $folder Subfolder within uploads/
     * @param string $prefix Filename prefix
     * @param string $baseType Base image type (banner, slider)
     * @param int|null $quality Compression quality (1-100)
     * @param array|null $oldImages Array of old image paths to delete ['desktop' => path, 'tablet' => path, 'mobile' => path]
     * @param bool $convertToWebp Convert to WebP format
     * @return array Array of paths ['desktop' => path, 'tablet' => path, 'mobile' => path]
     */
    public static function uploadResponsive(
        UploadedFile $file,
        string $folder = 'custom-images',
        string $prefix = 'image',
        string $baseType = 'banner',
        ?int $quality = null,
        ?array $oldImages = null,
        bool $convertToWebp = true
    ): array {
        $quality = $quality ?? self::$defaultQuality;
        $paths = [];

        // Generate base filename
        $extension = strtolower($file->getClientOriginalExtension());
        $baseFilename = Str::slug($prefix) . '-' . date('Y-m-d-H-i-s') . '-' . rand(1000, 9999);

        // Convert to WebP for better compression
        $saveAsWebp = $convertToWebp && in_array($extension, ['jpg', 'jpeg', 'png']);
        $finalExtension = $saveAsWebp ? 'webp' : $extension;

        // Ensure directory exists
        $directory = public_path("uploads/{$folder}");
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        // Define responsive sizes based on base type
        $sizes = [
            'desktop' => self::$maxDimensions[$baseType] ?? self::$maxDimensions['default'],
            'tablet' => self::$maxDimensions[$baseType . '_tablet'] ?? ['width' => 1024, 'height' => null],
            'mobile' => self::$maxDimensions[$baseType . '_mobile'] ?? ['width' => 640, 'height' => null],
        ];

        // Create each responsive version
        foreach ($sizes as $breakpoint => $dimensions) {
            $suffix = $breakpoint === 'desktop' ? '' : "_{$breakpoint}";
            $filename = $baseFilename . $suffix . '.' . $finalExtension;
            $relativePath = "uploads/{$folder}/{$filename}";
            $fullPath = public_path($relativePath);

            // Create fresh image instance for each size
            $image = Image::make($file);

            // Calculate height maintaining aspect ratio if not specified
            $height = $dimensions['height'];
            if ($height === null) {
                $ratio = $image->height() / $image->width();
                $height = intval($dimensions['width'] * $ratio);
            }

            // Resize maintaining aspect ratio (only downsize, never upscale)
            $image->resize($dimensions['width'], $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Save with compression
            if ($saveAsWebp) {
                $image->encode('webp', $quality)->save($fullPath);
            } else {
                $image->save($fullPath, $quality);
            }

            $paths[$breakpoint] = $relativePath;
        }

        // Delete old images if provided
        if ($oldImages) {
            foreach ($oldImages as $oldPath) {
                if ($oldPath) {
                    self::delete($oldPath);
                }
            }
        }

        return $paths;
    }

    /**
     * Upload multiple images (for galleries)
     *
     * @param array $files Array of UploadedFile
     * @param string $folder Subfolder within uploads/
     * @param string $prefix Filename prefix
     * @param string $type Image type for sizing
     * @param int|null $quality Compression quality
     * @param bool $convertToWebp Convert to WebP format
     * @return array Array of relative paths
     */
    public static function uploadMultiple(
        array $files,
        string $folder = 'custom-images',
        string $prefix = 'gallery',
        string $type = 'gallery',
        ?int $quality = null,
        bool $convertToWebp = true
    ): array {
        $paths = [];
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = self::upload(
                    $file,
                    $folder,
                    $prefix . '-' . ($index + 1),
                    $type,
                    $quality,
                    null,
                    $convertToWebp
                );
            }
        }
        return $paths;
    }

    /**
     * Delete an image file
     *
     * @param string|null $path Relative path to image
     * @return bool
     */
    public static function delete(?string $path): bool
    {
        if ($path && File::exists(public_path($path))) {
            return unlink(public_path($path));
        }
        return false;
    }

    /**
     * Delete multiple images (including responsive variants)
     *
     * @param array $paths Array of paths to delete
     * @return int Number of files deleted
     */
    public static function deleteMultiple(array $paths): int
    {
        $deleted = 0;
        foreach ($paths as $path) {
            if (self::delete($path)) {
                $deleted++;
            }
        }
        return $deleted;
    }

    /**
     * Set default quality for all uploads
     *
     * @param int $quality Quality value (1-100)
     */
    public static function setDefaultQuality(int $quality): void
    {
        self::$defaultQuality = max(1, min(100, $quality));
    }

    /**
     * Get default quality
     *
     * @return int
     */
    public static function getDefaultQuality(): int
    {
        return self::$defaultQuality;
    }

    /**
     * Add or update dimension preset
     *
     * @param string $type Type name
     * @param int $width Max width
     * @param int $height Max height
     */
    public static function setDimensions(string $type, int $width, int $height): void
    {
        self::$maxDimensions[$type] = ['width' => $width, 'height' => $height];
    }

    /**
     * Get dimensions for a type
     *
     * @param string $type Type name
     * @return array ['width' => int, 'height' => int]
     */
    public static function getDimensions(string $type): array
    {
        return self::$maxDimensions[$type] ?? self::$maxDimensions['default'];
    }

    /**
     * Get all dimension presets
     *
     * @return array
     */
    public static function getAllDimensions(): array
    {
        return self::$maxDimensions;
    }

    /**
     * Get the URL for an image path
     *
     * @param string|null $path Relative path
     * @return string|null Full URL or null
     */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        return asset($path);
    }

    /**
     * Check if an image exists
     *
     * @param string|null $path Relative path
     * @return bool
     */
    public static function exists(?string $path): bool
    {
        return $path && File::exists(public_path($path));
    }

    /**
     * Generate optimized img tag with lazy loading
     *
     * @param string|null $path Image path
     * @param string $alt Alt text
     * @param string|null $class CSS classes
     * @param string $type Image type for getting dimensions
     * @param bool $lazy Enable lazy loading
     * @return string HTML img tag
     */
    public static function imgTag(
        ?string $path,
        string $alt = '',
        ?string $class = null,
        string $type = 'default',
        bool $lazy = true
    ): string {
        $url = self::url($path) ?? asset('images/placeholder.webp');
        $dimensions = self::$maxDimensions[$type] ?? self::$maxDimensions['default'];

        $attributes = [
            'src' => $url,
            'alt' => htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'),
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'decoding' => 'async',
        ];

        if ($lazy) {
            $attributes['loading'] = 'lazy';
        }

        if ($class) {
            $attributes['class'] = $class;
        }

        $attrString = implode(' ', array_map(
            fn($key, $value) => "{$key}=\"{$value}\"",
            array_keys($attributes),
            $attributes
        ));

        return "<img {$attrString}>";
    }

    /**
     * Generate responsive picture element for banners/sliders
     *
     * @param array $paths Responsive image paths ['desktop' => path, 'tablet' => path, 'mobile' => path]
     * @param string $alt Alt text
     * @param string|null $class CSS classes
     * @param bool $lazy Enable lazy loading
     * @return string HTML picture element
     */
    public static function pictureTag(
        array $paths,
        string $alt = '',
        ?string $class = null,
        bool $lazy = true
    ): string {
        $desktop = self::url($paths['desktop'] ?? null) ?? asset('images/placeholder.webp');
        $tablet = self::url($paths['tablet'] ?? null);
        $mobile = self::url($paths['mobile'] ?? null);

        $loading = $lazy ? 'loading="lazy"' : '';
        $classAttr = $class ? "class=\"{$class}\"" : '';
        $altAttr = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');

        $html = '<picture>';

        if ($mobile) {
            $html .= "<source media=\"(max-width: 640px)\" srcset=\"{$mobile}\" type=\"image/webp\">";
        }

        if ($tablet) {
            $html .= "<source media=\"(max-width: 1024px)\" srcset=\"{$tablet}\" type=\"image/webp\">";
        }

        $html .= "<img src=\"{$desktop}\" alt=\"{$altAttr}\" {$loading} decoding=\"async\" {$classAttr}>";
        $html .= '</picture>';

        return $html;
    }

    /**
     * Generate srcset attribute for responsive images
     *
     * @param array $paths Responsive image paths ['640w' => path, '1024w' => path, '1920w' => path]
     * @return string srcset attribute value
     */
    public static function srcset(array $paths): string
    {
        $srcset = [];

        foreach ($paths as $width => $path) {
            $url = self::url($path);
            if ($url) {
                $srcset[] = "{$url} {$width}";
            }
        }

        return implode(', ', $srcset);
    }
}
