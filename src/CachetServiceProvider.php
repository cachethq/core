<?php

declare(strict_types=1);

namespace Cachet;

use Illuminate\Support\ServiceProvider;

class CachetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    /**
     * Register the package's publishable resources.
     */
    private function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/cachet.php' => config_path('cachet.php'),
        ], 'cachet-config');

        $this->publishes([
            __DIR__.'/../resources/views/status-page/index.blade.php' => resource_path('views/vendor/cachet/status-page/index.blade.php'),
        ], 'cachet-views');

        $this->publishes([
            __DIR__.'/../public/build/' => public_path('vendor/cachethq/cachet'),
        ], 'cachet-assets');
    }
}
