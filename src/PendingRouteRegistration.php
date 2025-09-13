<?php

namespace Cachet;

use Cachet\Http\Controllers\Auth\VerifyEmailController;
use Cachet\Http\Controllers\HealthController;
use Cachet\Http\Controllers\RssController;
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
                $router->get('/incidents/{incident:guid}', [StatusPageController::class, 'show'])->name('status-page.incident');

                $router->get('/setup', [SetupController::class, 'index'])->name('setup.index');
                $router->post('/setup', [SetupController::class, 'store'])->name('setup.store');

                // @todo subscription routes... subscribe, manage subscriptions, unsubscribe

                $router->get('/health', HealthController::class)->name('health');

                $router->get('/rss', RssController::class)->name('rss');

            });

        $this->registerEmailVerificationRoutes();
    }

    private function registerEmailVerificationRoutes(): void
    {
        Route::middleware('auth')->group(function () {
            Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');
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
