<?php

namespace Cachet;

use Cachet\Http\Controllers\Auth\AuthenticatedSessionController;
use Cachet\Http\Controllers\Auth\LoginController;
use Cachet\Http\Controllers\Dashboard\DashboardController;
use Cachet\Http\Controllers\HealthController;
use Cachet\Http\Controllers\Setup\SetupController;
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

                $router->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

                $router->get('/setup', [SetupController::class, 'index'])->name('setup.index');
                $router->post('/setup', [SetupController::class, 'store'])->name('setup.store');

                // @todo subscription routes... subscribe, manage subscriptions, unsubscribe

                $router->get('/health', HealthController::class)->name('health');
            });
    }

    public function withAuthenticationRoutes($middleware = ['cachet'])
    {
        Cachet::withAuthentication();

        Route::namespace('Cachet\\Http\\Controllers')
            ->domain(config('cachet.domain', null))
            ->middleware($middleware)
            ->prefix(Cachet::path())
            ->as('cachet.')
            ->group(function (Router $router) {
                $router->get('/login', [AuthenticatedSessionController::class, 'show'])->name('cachet.login');
                $router->post('/login', [AuthenticatedSessionController::class, 'store'])->name('cachet.login.post');
            });

        return $this;
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
