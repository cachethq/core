<?php

namespace Cachet;

use Composer\InstalledVersions;
use Doctrine\Common\Cache\Cache;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CachetServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/cachet.php', 'cachet'
        );

        $this->app->singleton(Cachet::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerResources();
        $this->registerPublishing();
        $this->registerCommands();
    }

    /**
     * Register the package's routes.
     */
    private function registerRoutes()
    {
        $this->callAfterResolving('router', function (Router $router, Application $application) {
            $router->group([
                'domain' => config('cachet.domain', null),
                'as' => 'cachet.api.',
                'prefix' => Cachet::path().'/api',
                'middleware' => 'cachet:api',
            ], function (Router $router) {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            });

            Cachet::routes()
                ->register();
        });
    }

    /**
     * Register the package's resources.
     */
    private function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cachet');
    }

    /**
     * Register the package's publishable resources.
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cachet.php' => config_path('cachet.php'),
            ], ['cachet', 'cachet-config']);

            $this->publishes([
                __DIR__.'/../resources/views/status-page/index.blade.php' => resource_path('views/vendor/cachet/status-page/index.blade.php'),
            ], ['cachet', 'cachet-views']);

            $this->publishes([
                __DIR__.'/../public/build/' => public_path('vendor/cachethq/cachet'),
            ], ['cachet', 'cachet-assets']);
        }
    }

    /**
     * Register the package's commands.
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            AboutCommand::add('Cache', fn () => [
                'Version' => app(Cachet::class)->version(),
                'Enabled' => AboutCommand::format(config('cachet.enabled'), console: fn ($value) => $value ? '<fg=yellow;options=bold>ENABLED</>' : 'OFF'),
            ]);
        }
    }
}
