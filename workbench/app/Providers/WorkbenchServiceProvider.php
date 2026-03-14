<?php

namespace Workbench\App\Providers;

use Cachet\Cachet;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Workbench\App\User;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        config([
            'cachet.path' => '/',
            'cachet.user_model' => User::class,
        ]);

        // Reinitialize the redirect URIs to ensure they use the correct path for the workbench environment.
        config([
            'services.keycloak.redirect' => env('KEYCLOAK_REDIRECT_URI', Cachet::dashboardPath().'/oauth/callback/keycloak'),
            'services.github.redirect' => env('GITHUB_REDIRECT_URI', Cachet::dashboardPath().'/oauth/callback/github'),
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('keycloak', \SocialiteProviders\Keycloak\Provider::class);
        });
    }
}
