{{--
    OptimizedImage Blade Component

    Usage:
    <x-optimized-image
        src="uploads/products/image.webp"
        alt="Product Name"
        :width="800"
        :height="600"
    />

    With responsive images:
    <x-optimized-image
        src="uploads/banners/desktop.webp"
        src-tablet="uploads/banners/tablet.webp"
        src-mobile="uploads/banners/mobile.webp"
        alt="Banner"
        class="w-full h-auto"
    />
--}}

@if($hasResponsive())
    <picture>
        @if($srcMobile)
            <source
                media="(max-width: 640px)"
                srcset="{{ $srcMobile }}"
                type="image/webp"
            >
        @endif

        @if($srcTablet)
            <source
                media="(max-width: 1024px)"
                srcset="{{ $srcTablet }}"
                type="image/webp"
            >
        @endif

        <img
            src="{{ $src }}"
            srcset="{{ $getSrcset() }}"
            sizes="{{ $sizes }}"
            alt="{{ $alt }}"
            loading="{{ $loading }}"
            decoding="async"
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            @if($class) class="{{ $class }}" @endif
            onerror="this.onerror=null; this.src='{{ $fallback }}';"
        >
    </picture>
@else
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="async"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @if($class) class="{{ $class }}" @endif
        onerror="this.onerror=null; this.src='{{ $fallback }}';"
    >
@endif
