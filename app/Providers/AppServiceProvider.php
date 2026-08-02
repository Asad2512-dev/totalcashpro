<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Keep generated asset/route URLs on the host the browser is actually using in local.
        if ($this->app->environment('local') && ! $this->app->runningInConsole()) {
            $request = $this->app['request'] ?? null;
            if ($request !== null && filled($request->getSchemeAndHttpHost())) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }
        }
    }
}
