<?php

namespace Cachet;

use Cachet\View\Components\Footer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CachetCoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! defined('CACHET_PATH')) {
            define('CACHET_PATH', realpath(__DIR__.'/../'));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->app->register(CachetServiceProvider::class);
        }

        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/cachet.php', 'cachet');
        }

        Route::middlewareGroup('cachet', config('cachet.middleware', []));
        Route::middlewareGroup('cachet:api', config('cachet.api_middleware', []));

        $this->registerResources();
        $this->registerPublishing();
        $this->registerBladeComponents();
    }

    /**
     * Register the package's resources such as routes, migrations, etc.
     */
    private function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cachet');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->registerRoutes();
    }

    /**
     * Register Cachet's routes.
     */
    private function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });

        Cachet::routes()
            ->withAuthenticationRoutes()
            ->register();
    }

    /**
     * Get the Cachet route group configuration array.
     */
    private function routeConfiguration(): array
    {
        return [
            'domain' => config('cachet.domain', null),
            'as' => 'cachet.api.',
            'prefix' => Cachet::path().'/api',
            'middleware' => 'cachet:api',
        ];
    }

    /**
     * Register the package's publishable resources.
     */
    private function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/cachet.php' => config_path('cachet.php'),
        ], 'cachet-config');

        $this->publishes([
            __DIR__.'/../resources/views/dashboard.blade.php' => resource_path('views/vendor/cachet/app.blade.php'),
        ], 'cachet-views');

        $this->publishes([
            __DIR__.'/../public/build/' => public_path('vendor/cachethq/cachet'),
        ], 'cachet-assets');
    }

    private function registerBladeComponents(): void
    {
        Blade::componentNamespace('Cachet\\View\\Components', 'cachet');

        Blade::component('footer', Footer::class);
    }
}
