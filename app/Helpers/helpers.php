<?php

declare(strict_types=1);

if (! function_exists('brand_name')) {
    function brand_name(): string
    {
        return (string) config('totalcashpro.name', 'TotalCashPro');
    }
}

if (! function_exists('brand_tagline')) {
    function brand_tagline(): string
    {
        return (string) config('totalcashpro.tagline');
    }
}

if (! function_exists('brand_asset_url')) {
    function brand_asset_url(string $filename): string
    {
        $path = public_path($filename);

        if (! is_file($path)) {
            return asset($filename);
        }

        return asset($filename).'?v='.filemtime($path);
    }
}

if (! function_exists('brand_logo_url')) {
    function brand_logo_url(): string
    {
        return brand_asset_url('logo.png');
    }
}

if (! function_exists('brand_favicon_url')) {
    function brand_favicon_url(): string
    {
        return brand_asset_url('favicon.png');
    }
}

if (! function_exists('brand_favicon_ico_url')) {
    function brand_favicon_ico_url(): string
    {
        return brand_asset_url('favicon.ico');
    }
}

if (! function_exists('brand_apple_touch_icon_url')) {
    function brand_apple_touch_icon_url(): string
    {
        return brand_asset_url('apple-touch-icon.png');
    }
}
