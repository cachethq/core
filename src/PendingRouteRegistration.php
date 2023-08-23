<?php

namespace Cachet;

use Cachet\Http\Controllers\HealthController;
use Cachet\Http\Controllers\StatusPage\StatusPageController;
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
                $router->get('/incidents/{incident}', [StatusPageController::class, 'show'])->name('status-page.incident');

                // @todo subscription routes... subscribe, manage subscriptions, unsubscribe

                $router->get('/health', HealthController::class)->name('health');
            });
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
