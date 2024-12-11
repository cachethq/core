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
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'components' => ComponentController::class,
    'component-groups' => ComponentGroupController::class,
    'incidents' => IncidentController::class,
    'incident-templates' => IncidentTemplateController::class,
    'metrics' => MetricController::class,
    'schedules' => ScheduleController::class,
]);

Route::apiResource('incidents.updates', IncidentUpdateController::class)
    ->scoped(['updateable_id']);

Route::apiResource('schedules.updates', ScheduleUpdateController::class)
    ->scoped(['updateable_id']);

Route::apiResource('metrics.points', MetricPointController::class)
    ->parameter('points', 'metricPoint')
    ->scoped();

Route::get('/ping', [GeneralController::class, 'ping'])->name('ping');
Route::get('/version', [GeneralController::class, 'version'])->name('version');
Route::get('/status', StatusController::class)->name('status');
