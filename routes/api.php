<?php

use Cachet\Http\Controllers\Api\ComponentController;
use Cachet\Http\Controllers\Api\ComponentGroupController;
use Cachet\Http\Controllers\Api\GeneralController;
use Cachet\Http\Controllers\Api\IncidentController;
use Cachet\Http\Controllers\Api\IncidentTemplateController;
use Cachet\Http\Controllers\Api\IncidentUpdateController;
use Cachet\Http\Controllers\Api\MetricController;
use Cachet\Http\Controllers\Api\MetricPointController;
use Cachet\Http\Controllers\Api\ScheduleController;
use Cachet\Http\Controllers\Api\ScheduleUpdateController;
use Cachet\Http\Controllers\Api\StatusController;
use Cachet\Http\Controllers\Api\UptimeKumaWebhookController;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'components' => ComponentController::class,
    'component-groups' => ComponentGroupController::class,
    'incidents' => IncidentController::class,
    'incident-templates' => IncidentTemplateController::class,
    'metrics' => MetricController::class,
    'schedules' => ScheduleController::class,
], ['except' => ['store', 'update', 'destroy']]);

Route::apiResource('incidents.updates', IncidentUpdateController::class, [
    'except' => ['store', 'update', 'destroy'],
])
    ->scoped(['updateable_id']);

Route::apiResource('schedules.updates', ScheduleUpdateController::class, [
    'except' => ['store', 'update', 'destroy'],
])
    ->scoped(['updateable_id']);

Route::apiResource('metrics.points', MetricPointController::class, [
    'except' => ['store', 'update', 'destroy'],
])
    ->parameter('points', 'metricPoint')
    ->scoped();

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResources([
        'components' => ComponentController::class,
        'component-groups' => ComponentGroupController::class,
        'incidents' => IncidentController::class,
        'incident-templates' => IncidentTemplateController::class,
        'metrics' => MetricController::class,
        'schedules' => ScheduleController::class,
    ], ['except' => ['index', 'show']]);

    Route::apiResource('incidents.updates', IncidentUpdateController::class, [
        'except' => ['index', 'show'],
    ])
        ->scoped(['updateable_id']);

    Route::apiResource('schedules.updates', ScheduleUpdateController::class, [
        'except' => ['index', 'show'],
    ])
        ->scoped(['updateable_id']);

    Route::apiResource('metrics.points', MetricPointController::class, [
        'except' => ['index', 'show', 'update'],
    ])
        ->parameter('points', 'metricPoint')
        ->scoped();
});

Route::get('/ping', [GeneralController::class, 'ping'])->name('ping');
Route::get('/version', [GeneralController::class, 'version'])->name('version');
Route::get('/status', StatusController::class)->name('status');

/*
* Uptime Kuma Webhook Endpoint
*/

Route::prefix('integrations/uptime-kuma')->group(function () {
    //webhook info and instructions steps
    Route::get('/webhook', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'Uptime Kuma webhook endpoint ready to receive notifications.',
            'description' => 'This endpoint receives webhook notifications from Uptime Kuma when monitor status changes.',
            'uptime_kuma_url' => config('cachet.uptime_kuma.url'),
            'status_page_slug' => config('cachet.uptime_kuma.status_page_slug'),
            'integration_enabled' => config('cachet.uptime_kuma.enabled'),
            'instructions' => [
                '1. In Uptime Kuma, go to Settings → Notifications',
                '2. Add a new notification with type "Webhook"',
                '3. Set the URL to: '.url('/api/integrations/uptime-kuma/webhook'),
                '4. Set Content-Type to "application/json"',
                '5. Enable the notification for your monitors',
            ],
        ]);
    })->name('integrations.uptime-kuma.info');
    Route::post('/webhook', UptimeKumaWebhookController::class)
        ->name('integrations.uptime-kuma.webhook')
        ->middleware('throttle:60,1');
});
