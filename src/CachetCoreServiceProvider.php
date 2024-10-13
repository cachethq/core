<?php

namespace Cachet;

use BladeUI\Icons\Factory;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Settings\AppSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
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
            define('CACHET_PATH', dirname(__DIR__).'/');
        }

        $this->app->singleton(Cachet::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/cachet.php', 'cachet');
        }

        Route::middlewareGroup('cachet', config('cachet.middleware', []));
        Route::middlewareGroup('cachet:api', config('cachet.api_middleware', []));

        Relation::morphMap([
            'incident' => Incident::class,
            'schedule' => Schedule::class,
        ]);

        $this->registerCommands();
        $this->registerResources();
        $this->registerPublishing();
        $this->registerBladeComponents();

        Http::globalRequestMiddleware(fn ($request) => $request->withHeader(
            'User-Agent', Cachet::USER_AGENT
        ));
    }

    /**
     * Register the package's resources such as routes, migrations, etc.
     */
    private function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cachet');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        $this->configureRateLimiting();
        $this->registerRoutes();
    }

    /**
     * Configure the rate limiting for the application.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('cachet-api', function ($request) {
            return Limit::perMinute(config('cachet.api_rate_limit', 300))
                ->by(optional($request->user())->id ?: $request->ip());
        });
    }

    /**
     * Register Cachet's routes.
     */
    private function registerRoutes(): void
    {
        $this->callAfterResolving('router', function (Router $router, Application $application) {
            $router->group([
                'domain' => config('cachet.domain'),
                'as' => 'cachet.api.',
                'prefix' => Cachet::path().'/api',
                'middleware' => ['cachet:api', 'throttle:cachet-api'],
            ], function (Router $router) {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            });

            Cachet::routes()
                ->register();
        });
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
            'middleware' => ['cachet:api', 'throttle:cachet-api'],
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
        ], ['cachet', 'cachet-config']);

        $this->publishes([
            __DIR__.'/../resources/views/status-page/index.blade.php' => resource_path('views/vendor/cachet/status-page/index.blade.php'),
        ], ['cachet', 'cachet-views']);

        $this->publishes([
            __DIR__.'/../public/' => public_path('vendor/cachethq/cachet'),
        ], ['cachet', 'cachet-assets']);
    }

    /**
     * Register the package's Blade components.
     */
    private function registerBladeComponents(): void
    {
        Blade::componentNamespace('Cachet\\View\\Components', 'cachet');

        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add('cachet', [
                'path' => __DIR__.'/../resources/svg',
                'prefix' => 'cachet',
            ]);
        });
    }

    /**
     * Register the package's commands.
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\SendBeaconCommand::class,
                Commands\VersionCommand::class,
            ]);

            AboutCommand::add('Cachet', fn () => [
                'Beacon' => AboutCommand::format(config('cachet.beacon'), console: fn ($value) => $value ? '<fg=yellow;options=bold>ENABLED</>' : 'OFF'),
                'Enabled' => AboutCommand::format(config('cachet.enabled'), console: fn ($value) => $value ? '<fg=yellow;options=bold>ENABLED</>' : 'OFF'),
                'Install ID' => app(AppSettings::class)->install_id,
                'Version' => app(Cachet::class)->version(),
            ]);
        }
    }
}
