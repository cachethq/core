<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cachet Enabled
    |--------------------------------------------------------------------------
    |
    | This option determines if Cachet is enabled. If Cachet is not enabled,
    | the status page will not be accessible. This is useful when you need
    | to disable the status page for maintenance or other reasons.
    |
    */
    'enabled' => env('CACHET_ENABLED', true),

    /*
     |--------------------------------------------------------------------------
     | Cachet Path
     |--------------------------------------------------------------------------
     |
     | This is the URI path where Cachet will be accessible from.
     */
    'path' => env('CACHET_PATH', '/'),

    'guard' => env('CACHET_GUARD', null),

    /*
     |--------------------------------------------------------------------------
     | The User Model.
     |--------------------------------------------------------------------------
     |
     | This is the model that will be used to authenticate users. This model
     | must be an instance of Illuminate\Foundation\Auth\User.
     */
    'user_model' => env('CACHET_USER_MODEL', \App\Models\User::class),

    'user_migrations' => env('CACHET_USER_MIGRATIONS', true),

    /*
     |--------------------------------------------------------------------------
     | Cachet Domain
     |--------------------------------------------------------------------------
     |
     | This is the domain where Cachet will be accessible from.
     |
     */
    'domain' => env('CACHET_DOMAIN'),

    /*
     |--------------------------------------------------------------------------
     | Cachet Title
     |--------------------------------------------------------------------------
     |
     | This is the title of the status page. By default, this will be the name
     | of your application.
     |
     */
    'title' => env('CACHET_TITLE', env('APP_NAME').' - Status'),

    /*
     |--------------------------------------------------------------------------
     | Cachet Middleware
     |--------------------------------------------------------------------------
     |
     | This is the middleware that will be applied to the status page. By
     | default, the "web" middleware group will be applied, which means
     | that the status page will be accessible by anyone.
     |
     */
    'middleware' => [
        'web',
        //        \Cachet\Http\Middleware\AuthenticateRemoteUser::class,
    ],

    'api_middleware' => [
        'api',
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet API Rate Limit (attempts per minute)
     |--------------------------------------------------------------------------
     |
     | This is the rate limit for the Cachet API. By default, the API is rate
     | limited to 300 requests a minute (or 5 requests a second). You can
     | adjust the limit as needed by your application.
     |
     */
    'api_rate_limit' => env('CACHET_API_RATE_LIMIT', 300),

    /*
     |--------------------------------------------------------------------------
     | Cachet Beacon
     |--------------------------------------------------------------------------
     |
     | Enable Cachet's telemetry. Cachet will only ever send anonymous data
     | to the cachethq.io domain. This enables us to understand how Cachet
     | is used.
     |
     */
    'beacon' => env('CACHET_BEACON', true),

    /*
     |--------------------------------------------------------------------------
     | Cachet Docker
     |--------------------------------------------------------------------------
     |
     | Determines whether Cachet is running from within a Docker instance.
     |
     */
    'docker' => env('CACHET_DOCKER', false),

    /*
     |--------------------------------------------------------------------------
     | Cachet Queue
     |--------------------------------------------------------------------------
     |
     | Configure the default queue connection and queue name used by cachet.
     |
     */
    'queue' => [
        'connection' => env('CACHET_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'database')),
        'name' => env('CACHET_QUEUE_NAME', 'default'),
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet Webhooks
     |--------------------------------------------------------------------------
     |
     | Configure how Cachet sends webhooks for events.
     |
     */
    'webhooks' => [
        'queue_connection' => env('CACHET_WEBHOOK_QUEUE_CONNECTION', env('CACHET_QUEUE_CONNECTION', 'database')),
        'queue_name' => env('CACHET_WEBHOOK_QUEUE_NAME', env('CACHET_QUEUE_NAME', 'webhooks')),

        'logs' => [
            'prune_logs_after_days' => 30,
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet Subscribers
     |--------------------------------------------------------------------------
     |
     | Configure how Cachet sends notifications to subscribers as well as the
     | expiry time of the verification link when a new subscriber is created.
     |
     */
    'subscribers' => [
        'queue_connection' => env('CACHET_SUBSCRIBERS_QUEUE_CONNECTION', env('CACHET_QUEUE_CONNECTION', 'database')),
        'queue_name' => env('CACHET_SUBSCRIBERS_QUEUE_NAME', env('CACHET_QUEUE_NAME', 'subscribers')),

        'verification_expiry_minutes' => 60,
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet Supported Locales
     |--------------------------------------------------------------------------
     |
     | Configure which locales are supported by Cachet.
     |
     */
    'supported_locales' => [
        'de_AT' => 'Deutsch (AT)',
        'de_CH' => 'Deutsch (CH)',
        'de_DE' => 'Deutsch (DE)',
        'en' => 'English',
        'en_GB' => 'English (UK)',
        'nl_NL' => 'Nederlands',
        'pt_BR' => 'Português (BR)',
        'zh_CN' => '简体中文',
        'zh_TW' => '繁體中文',
        'ph' => 'Filipino',
    ],
];
