<?php

namespace Cachet;

use Cachet\Http\Controllers\HealthController;
use Cachet\Http\Controllers\RssController;
use Cachet\Http\Controllers\Setup\SetupController;
use Cachet\Http\Controllers\StatusPage\StatusPageController;
use Cachet\Http\Controllers\Subscribers\SubscriberController;
use Cachet\Http\Controllers\Subscribers\VerifySubscriberEmailController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class PendingRouteRegistration
{
    /**
     * Indicates if the routes have been registered.
     */
    protected bool $registered = false;

    /**
     * Register Cachet's routes.
     */
    public function register(): void
    {
        $this->registered = true;

        Route::namespace('Cachet\\Http\\Controllers')
            ->domain(config('cachet.domain', null))
            ->middleware(config('cachet.middleware', []))
            ->prefix(Cachet::path())
            ->as('cachet.')
            ->group(function (Router $router) {
                $router->get('/', [StatusPageController::class, 'index'])->name('status-page');
                $router->get('/incidents/{incident:guid}', [StatusPageController::class, 'show'])->name('status-page.incident');

                $router->get('/setup', [SetupController::class, 'index'])->name('setup.index');
                $router->post('/setup', [SetupController::class, 'store'])->name('setup.store');

                $this->registerSubscriptionRoutes($router);;

                $router->get('/health', HealthController::class)->name('health');

                $router->get('/rss', RssController::class)->name('rss');
            });
    }

    private function registerSubscriptionRoutes(Router $router): void
    {
        $router->get('/subscribers/create', [SubscriberController::class, 'create'])->name('subscribers.create');

        $router->get('/subscribers/verify/{subscriber}/{hash}', VerifySubscriberEmailController::class)->name('subscribers.verify')
            ->middleware(['signed', 'throttle:6,1']);
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}
