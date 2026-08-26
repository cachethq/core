<?php

use App\Models\User;
use Cachet\Http\Middleware\AuthenticateApiIfProtected;
use Cachet\Http\Middleware\AuthenticateMcpIfProtected;
use Cachet\Http\Middleware\EnsureApiIsEnabled;
use Cachet\Http\Middleware\EnsureMcpIsEnabled;
use Cachet\Http\Middleware\ForceJsonResponse;
use Cachet\Http\Middleware\SetStatusPageLocale;
use Illuminate\Routing\Middleware\SubstituteBindings;

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
    'user_model' => env('CACHET_USER_MODEL', User::class),

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
        SetStatusPageLocale::class,
        //        \Cachet\Http\Middleware\AuthenticateRemoteUser::class,
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet API Middleware
     |--------------------------------------------------------------------------
     |
     | This is the middleware that will be applied to the Cachet API routes.
     | Cachet manages its own rate limiting via the "cachet.api_rate_limit"
     | option, so the host application's "api" middleware group (which may
     | register its own, more restrictive "throttle:api" limiter) is
     | intentionally not included here.
     |
     */
    'api_middleware' => [
        ForceJsonResponse::class,
        EnsureApiIsEnabled::class,
        AuthenticateApiIfProtected::class,
        SubstituteBindings::class,
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet MCP Middleware
     |--------------------------------------------------------------------------
     |
     | This is the middleware that will be applied to the Cachet MCP server
     | endpoint. The MCP server is disabled until the "Enable MCP server"
     | setting is turned on from the dashboard.
     |
     */
    'mcp_middleware' => [
        EnsureMcpIsEnabled::class,
        AuthenticateMcpIfProtected::class,
    ],

    'trusted_proxies' => env('CACHET_TRUSTED_PROXIES', ''),

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
     | Cachet MCP Rate Limit (attempts per minute)
     |--------------------------------------------------------------------------
     |
     | This is the rate limit for the Cachet MCP server. By default, the MCP
     | server is rate limited to 300 requests a minute. You can adjust the
     | limit as needed by your application.
     |
     */
    'mcp_rate_limit' => env('CACHET_MCP_RATE_LIMIT', 300),

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
     | Cachet Settings Cache
     |--------------------------------------------------------------------------
     |
     | Cache Cachet's settings so they are not read from the database on every
     | request. The cache is refreshed automatically whenever settings are
     | saved, so it never serves stale values.
     |
     */
    'settings_cache' => env('CACHET_SETTINGS_CACHE', true),

    /*
     |--------------------------------------------------------------------------
     | Cachet Image Uploads
     |--------------------------------------------------------------------------
     |
     | Configure where custom images are stored, their maximum size in
     | kilobytes, and the MIME types that may be uploaded.
     |
     */
    'uploads' => [
        'disk' => env('CACHET_UPLOAD_DISK', 'public'),
        'max_size' => (int) env('CACHET_UPLOAD_MAX_SIZE', 1024),
        'image_mime_types' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('CACHET_UPLOAD_IMAGE_MIME_TYPES', 'image/jpeg,image/png,image/gif,image/webp')),
        ))),
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet Migrations
     |--------------------------------------------------------------------------
     |
     | Whether Cachet loads its migrations into the application's migrator.
     | Disable this when the host application manages Cachet's migrations
     | itself.
     |
     */
    'run_migrations' => env('CACHET_RUN_MIGRATIONS', true),

    /*
     |--------------------------------------------------------------------------
     | Cachet Schedules
     |--------------------------------------------------------------------------
     |
     | Whether Cachet registers its scheduled tasks (beacon, notifications,
     | pruning) with the application's scheduler. Disable this when the
     | host application schedules these commands itself.
     |
     */
    'register_schedules' => env('CACHET_REGISTER_SCHEDULES', true),

    /*
     |--------------------------------------------------------------------------
     | Cachet Component Checks
     |--------------------------------------------------------------------------
     |
     | Configure how long component check results are kept before they are
     | pruned by the model:prune scheduled task.
     |
     */
    'checks' => [
        'prune_checks_after_days' => env('CACHET_PRUNE_CHECKS_AFTER_DAYS', 30),
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet Metrics
     |--------------------------------------------------------------------------
     |
     | Metrics are a curated, human-readable display series, not a time series
     | database. "retention_days" is how long metric points are kept before
     | the model:prune scheduled task removes them; set it to null to keep
     | every point forever, and expect the table to grow without bound.
     |
     | "max_included_points" caps how many points the API will attach to a
     | metric through "?include=points". Use the metric points endpoint,
     | which is paginated, to walk the full history of a metric.
     |
     */
    'metrics' => [
        'retention_days' => env('CACHET_METRICS_RETENTION_DAYS', 90),

        'max_included_points' => env('CACHET_METRICS_MAX_INCLUDED_POINTS', 100),

        'max_batch_points' => env('CACHET_METRICS_MAX_BATCH_POINTS', 1000),
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet Webhooks
     |--------------------------------------------------------------------------
     |
     | Configure how Cachet sends webhooks for events. When the connection or
     | queue name is null, the application's default queue connection and
     | queue are used.
     |
     */
    'webhooks' => [
        'queue_connection' => env('CACHET_WEBHOOK_QUEUE_CONNECTION'),
        'queue_name' => env('CACHET_WEBHOOK_QUEUE_NAME'),

        'logs' => [
            'prune_logs_after_days' => 30,
        ],
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
        'de' => 'Deutsch (DE)',
        'de_AT' => 'Deutsch (AT)',
        'de_CH' => 'Deutsch (CH)',
        'en' => 'English',
        'en_GB' => 'English (UK)',
        'es_ES' => 'Spanish (ES)',
        'ko' => '한국어',
        'nl' => 'Nederlands',
        'ph' => 'Filipino',
        'pt_BR' => 'Português (BR)',
        'zh_CN' => '简体中文',
        'zh_TW' => '繁體中文',
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet Demo Mode
     |--------------------------------------------------------------------------
     |
     | Whether to run Cachet in demo mode. This will adjust some of the default
     | settings to allow Cachet to run in a demo environment.
     |
     */
    'demo_mode' => env('CACHET_DEMO_MODE', false),

    /*
     |--------------------------------------------------------------------------
     | Cachet Template Renderers
     |--------------------------------------------------------------------------
     |
     | Configure which renderers a template body may be rendered with. Twig
     | bodies are rendered inside a sandbox. Blade bodies are compiled to
     | PHP and run with the permissions of the application, so the Blade
     | renderer is only used when an installation enables it here.
     |
     */
    'renderers' => [
        'blade' => env('CACHET_BLADE_RENDERER', false),
    ],

    /*
     |--------------------------------------------------------------------------
     | Cachet Blog Feed
     |--------------------------------------------------------------------------
     |
     | This is the URI to the Cachet blog feed. This is used to display
     | the latest blog posts on the status page. By default, this is
     | set to the public Cachet blog feed.
     |
     */
    'feed' => [
        'uri' => env('CACHET_FEED_URI', 'https://blog.cachethq.io/rss'),
        'cache' => env('CACHET_FEED_CACHE', 3600),
    ],

];
