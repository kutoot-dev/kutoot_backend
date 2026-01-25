<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * OptimizedImage - Blade component for rendering optimized images
 *
 * Features:
 * - Lazy loading with loading="lazy"
 * - Responsive srcset for different screen sizes
 * - WebP format support
 * - Proper width/height to prevent layout shift
 * - Fallback for missing images
 */
class OptimizedImage extends Component
{
    public string $src;
    public ?string $srcTablet;
    public ?string $srcMobile;
    public string $alt;
    public ?string $class;
    public ?int $width;
    public ?int $height;
    public string $loading;
    public ?string $sizes;
    public ?string $fallback;

    /**
     * Create a new component instance.
     *
     * @param string $src Primary image source path
     * @param string $alt Alt text for accessibility
     * @param string|null $srcTablet Tablet-sized image source
     * @param string|null $srcMobile Mobile-sized image source
     * @param string|null $class CSS classes
     * @param int|null $width Image width
     * @param int|null $height Image height
     * @param string $loading Loading strategy: 'lazy' or 'eager'
     * @param string|null $sizes Responsive sizes attribute
     * @param string|null $fallback Fallback image path
     */
    public function __construct(
        string $src,
        string $alt = '',
        ?string $srcTablet = null,
        ?string $srcMobile = null,
        ?string $class = null,
        ?int $width = null,
        ?int $height = null,
        string $loading = 'lazy',
        ?string $sizes = null,
        ?string $fallback = null
    ) {
        $this->src = $this->resolveImageUrl($src);
        $this->srcTablet = $srcTablet ? $this->resolveImageUrl($srcTablet) : null;
        $this->srcMobile = $srcMobile ? $this->resolveImageUrl($srcMobile) : null;
        $this->alt = $alt;
        $this->class = $class;
        $this->width = $width;
        $this->height = $height;
        $this->loading = $loading;
        $this->sizes = $sizes ?? '(max-width: 640px) 100vw, (max-width: 1024px) 80vw, 60vw';
        $this->fallback = $fallback ?? asset('images/placeholder.webp');
    }

    /**
     * Resolve image URL from path
     */
    protected function resolveImageUrl(?string $path): string
    {
        if (!$path) {
            return $this->fallback ?? '';
        }

        // Already a full URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Relative path - convert to asset URL
        return asset($path);
    }

    /**
     * Check if responsive versions are available
     */
    public function hasResponsive(): bool
    {
        return $this->srcTablet !== null || $this->srcMobile !== null;
    }

    /**
     * Build srcset attribute
     */
    public function getSrcset(): string
    {
        $srcset = [];

        if ($this->srcMobile) {
            $srcset[] = "{$this->srcMobile} 640w";
        }

        if ($this->srcTablet) {
            $srcset[] = "{$this->srcTablet} 1024w";
        }

        $srcset[] = "{$this->src} 1920w";

        return implode(', ', $srcset);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.optimized-image');
    }
}
