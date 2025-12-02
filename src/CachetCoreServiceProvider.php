<?php

namespace Cachet;

use BladeUI\Icons\Factory;
use Cachet\Commands\MakeUserCommand;
use Cachet\Commands\SendBeaconCommand;
use Cachet\Commands\VersionCommand;
use Cachet\Database\Seeders\DatabaseSeeder;
use Cachet\Listeners\SendWebhookListener;
use Cachet\Listeners\WebhookCallEventListener;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Settings\AppSettings;
use Cachet\View\ViewManager;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\Server;
use Dedoc\Scramble\Support\RouteInfo;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

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
        $this->app->singleton(ViewManager::class);
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
        $this->registerSchedules();
        $this->registerResources();
        $this->registerPublishing();
        $this->registerBladeComponents();

        Event::listen([
            'Cachet\Events\Incidents\*',
            'Cachet\Events\Components\*',
            'Cachet\Events\Subscribers\*',
            'Cachet\Events\Metrics\*',
        ], SendWebhookListener::class);
        Event::listen([WebhookCallSucceededEvent::class, WebhookCallFailedEvent::class], WebhookCallEventListener::class);

        Http::globalRequestMiddleware(fn ($request) => $request->withHeader(
            'User-Agent', Cachet::USER_AGENT
        ));

        FilamentColor::register([
            'cachet' => Color::generateV3Palette('rgb(4, 193, 71)'),
        ]);

        $this->configureScramble();
    }

    /**
     * Register the package's resources such as routes, migrations, etc.
     */
    private function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cachet');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'cachet');

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
                ->by($request->user()?->id ?: $request->ip());
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
        view()->share('appSettings', app(AppSettings::class));
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
                MakeUserCommand::class,
                SendBeaconCommand::class,
                VersionCommand::class,
            ]);

            AboutCommand::add('Cachet', fn () => [
                'Beacon' => AboutCommand::format(config('cachet.beacon'), console: fn ($value) => $value ? '<fg=yellow;options=bold>ENABLED</>' : 'OFF'),
                'Enabled' => AboutCommand::format(config('cachet.enabled'), console: fn ($value) => $value ? '<fg=yellow;options=bold>ENABLED</>' : 'OFF'),
                'Install ID' => app(AppSettings::class)->install_id,
                'Version' => app(Cachet::class)->version(),
            ]);
        }
    }

    /**
     * Register the package's schedules.
     */
    private function registerSchedules(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            $demoMode = fn () => Cachet::demoMode();

            $schedule->command('cachet:beacon')->daily();

            $schedule->command('db:seed', [
                '--class' => DatabaseSeeder::class,
                '--force',
            ])->everyThirtyMinutes()->when($demoMode);
        });
    }

    /**
     * Scramble is installed as dev dependency hence the class existence check.
     */
    private function configureScramble(): void
    {
        if (! class_exists(Scramble::class)) {
            return;
        }

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->info->description = 'API documentation for Cachet, the open-source, self-hosted status page system.';

                $openApi->addServer(Server::make('https://v3.cachethq.io/api')->setDescription('The Cachet v3 demo server.'));
                $openApi->secure(SecurityScheme::http('bearer'));
            })
            ->withOperationTransformers(function (Operation $operation, RouteInfo $routeInfo) {
                $hasAuthMiddleware = collect($routeInfo->route->gatherMiddleware())->contains(fn ($m) => Str::startsWith($m, 'auth:'));

                if (! $hasAuthMiddleware) {
                    $operation->security = [];
                }
            });
    }
}
