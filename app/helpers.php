<?php

if (!function_exists('handleImageUpload')) {
    function handleImageUpload($request, $key, $folder = 'uploads')
    {
        if ($request->hasFile($key)) {
            $file = $request->file($key); // UploadedFile instance
            $filename = time().'_'.$file->getClientOriginalName();
            $path = 'uploads/'.$folder;
            $file->move(public_path($path), $filename);

            return $path . '/' . $filename;
        }

        return null; // no file uploaded
    }
}

/**
 * Check if current route matches any of the given patterns
 * Useful for sidebar active state detection
 *
 * @param string|array $patterns Route pattern(s) to match
 * @return bool
 */
if (!function_exists('routeIs')) {
    function routeIs($patterns): bool
    {
        if (is_string($patterns)) {
            return Route::is($patterns);
        }

        foreach ((array) $patterns as $pattern) {
            if (Route::is($pattern)) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Return 'active' class if route matches any of the given patterns
 *
 * @param string|array $patterns Route pattern(s) to match
 * @param string $class CSS class to return (default: 'active')
 * @return string
 */
if (!function_exists('activeClass')) {
    function activeClass($patterns, string $class = 'active'): string
    {
        return routeIs($patterns) ? $class : '';
    }
}


