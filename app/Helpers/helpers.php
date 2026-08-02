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

if (! function_exists('brand_logo_url')) {
    function brand_logo_url(): string
    {
        return asset('logo.png');
    }
}

if (! function_exists('brand_favicon_url')) {
    function brand_favicon_url(): string
    {
        return asset('logo.png');
    }
}
