<?php

namespace Cachet;

use Cachet\Http\Controllers\Auth\AuthenticatedSessionController;
use Cachet\Http\Controllers\Auth\EmailVerificationNotificationController;
use Cachet\Http\Controllers\Auth\EmailVerificationPromptController;
use Cachet\Http\Controllers\Auth\NewPasswordController;
use Cachet\Http\Controllers\Auth\PasswordResetLinkController;
use Cachet\Http\Controllers\Auth\VerifyEmailController;
use Cachet\Http\Controllers\Dashboard\DashboardController;
use Cachet\Http\Controllers\Dashboard\KitchenSinkController;
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

                $router->prefix('/dashboard')->name('dashboard.')->group(function () use ($router) {
                    $router->get('/', [DashboardController::class, 'index'])->name('index');
                    $router->get('/kitchen-sink', [KitchenSinkController::class, 'index'])->name('kitchen-sink');
                });

                $router->get('/setup', [SetupController::class, 'index'])->name('setup.index');
                $router->post('/setup', [SetupController::class, 'store'])->name('setup.store');

                // @todo subscription routes... subscribe, manage subscriptions, unsubscribe

                $router->get('/health', HealthController::class)->name('health');
            });
    }

    /**
     * Register Cachet's authentication routes.
     */
    public function withAuthenticationRoutes($middleware = ['cachet']): self
    {
        Cachet::withAuthentication();

        Route::namespace('Cachet\\Http\\Controllers')
            ->domain(config('cachet.domain', null))
            ->middleware($middleware)
            ->prefix(Cachet::path())
            ->as('cachet.')
            ->group(function (Router $router) {
                $router->get('/login', [AuthenticatedSessionController::class, 'show'])->name('login');
                $router->post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

                $router->get('/forgot-password', [PasswordResetLinkController::class, 'show'])->name('password.request');
                $router->post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

                $router->get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
                $router->post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

                // @todo Make these routes auth protected.
                $router->get('/verify-email', EmailVerificationPromptController::class)->name('verification.notice');
                $router->get('/verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
                $router->post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                    ->middleware('throttle:6,1')
                    ->name('verification.send');

                $router->post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
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
