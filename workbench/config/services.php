<?php

use Cachet\Cachet;
use Illuminate\Support\Facades\Route;

return [

    /*
    |--------------------------------------------------------------------------
    | Socialite OAuth Configuration
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of OAuth authentication in Cachet.
    |
    | NOTE: It is highly discouraged to enable the 'registration' option in a
    | production environment without also configuring the 'domainAllowlist'
    | option, as otherwise anyone with an account on any of the providers
    | can register an account on your Cachet instance and gain access to it.
    |
    */

   'oauth' => [
        /*
        * Whether to remember the user once they have logged in using a social provider.
        */
        'rememberLogin' => env('OAUTH_REMEMBER_LOGIN', false),
        /*
        * Whether to allow users who authenticate via a social provider to be automatically
        * registered, if no account already exists for them.
        */
        'registration' => env('OAUTH_REGISTRATION', false),
        /*
        * An allowlist of email domains which are permitted to authenticate via social providers.
        * If empty or not set, there is no restriction on email domains.
        */
        'domainAllowlist' => explode(',', env('OAUTH_DOMAIN_ALLOWLIST', 'does_not_exists.local')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Socialite Providers
    |--------------------------------------------------------------------------
    |
    | Configuration for Laravel Socialite providers. This is used to configure the providers which
    | users can use to authenticate with Cachet. The default providers are GitHub and Keycloak.
    |
    | Only the providers which have a client_id configured here will be offered as authentication
    | options to users.
    |
    */

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI', Cachet::dashboardPath().'/oauth/callback/github'),
    ],

    'keycloak' => [
        'client_id' => env('KEYCLOAK_CLIENT_ID'),
        'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
        'redirect' => env('KEYCLOAK_REDIRECT_URI', Cachet::dashboardPath().'/oauth/callback/keycloak'),
        'base_url' => env('KEYCLOAK_BASE_URL'),
        'realms' => env('KEYCLOAK_REALM', 'master'),
    ],

];
