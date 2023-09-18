<?php

use Cachet\Http\Controllers\Api\ComponentController;
use Cachet\Http\Controllers\Api\ComponentGroupController;
use Cachet\Http\Controllers\Api\GeneralController;
use Cachet\Http\Controllers\Api\IncidentController;
use Cachet\Http\Controllers\Api\IncidentUpdateController;
use Cachet\Http\Controllers\Api\MetricController;
use Cachet\Http\Controllers\Api\MetricPointController;
use Cachet\Http\Controllers\Api\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/components', [ComponentController::class, 'index'])->name('components.index');
Route::post('/components', [ComponentController::class, 'store'])->name('components.store');
Route::get('/components/{component}', [ComponentController::class, 'show'])->name('components.show');
Route::put('/components/{component}', [ComponentController::class, 'update'])->name('components.update');
Route::delete('/components/{component}', [ComponentController::class, 'destroy'])->name('components.destroy');

Route::get('/component-groups', [ComponentGroupController::class, 'index'])->name('component-groups.index');
Route::post('/component-groups', [ComponentGroupController::class, 'store'])->name('component-groups.store');
Route::get('/component-groups/{componentGroup}', [ComponentGroupController::class, 'show'])->name('component-groups.show');
Route::put('/component-groups/{componentGroup}', [ComponentGroupController::class, 'update'])->name('component-groups.update');
Route::delete('/component-groups/{componentGroup}', [ComponentGroupController::class, 'destroy'])->name('component-groups.destroy');

Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');

Route::get('/incidents/{incident}/updates', [IncidentUpdateController::class, 'index'])->name('incidents.updates.index');
Route::post('/incidents/{incident}/updates', [IncidentUpdateController::class, 'store'])->name('incidents.updates.store');
Route::get('/incidents/{incident}/updates/{incidentUpdate}', [IncidentUpdateController::class, 'show'])->name('incidents.updates.show')->scopeBindings();
Route::put('/incidents/{incident}/updates/{incidentUpdate}', [IncidentUpdateController::class, 'update'])->name('incidents.updates.update')->scopeBindings();
Route::delete('/incidents/{incident}/updates/{incidentUpdate}', [IncidentUpdateController::class, 'destroy'])->name('incidents.updates.destroy')->scopeBindings();

Route::get('/metrics', [MetricController::class, 'index'])->name('metrics.index');
Route::post('/metrics', [MetricController::class, 'store'])->name('metrics.store');
Route::get('/metrics/{metric}', [MetricController::class, 'show'])->name('metrics.show');
Route::put('/metrics/{metric}', [MetricController::class, 'update'])->name('metrics.update');
Route::delete('/metrics/{metric}', [MetricController::class, 'destroy'])->name('metrics.destroy');

Route::get('/metrics/{metric}/points', [MetricPointController::class, 'index'])->name('metrics.points.index');
Route::post('/metrics/{metric}/points', [MetricPointController::class, 'store'])->name('metrics.points.store');
Route::get('/metrics/{metric}/points/{metricPoint}', [MetricPointController::class, 'show'])->name('metrics.points.show')->scopeBindings();
Route::delete('/metrics/{metric}/points/{metricPoint}', [MetricPointController::class, 'destroy'])->name('metrics.points.destroy');

Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

Route::get('/ping', [GeneralController::class, 'ping'])->name('ping');
Route::get('/version', [GeneralController::class, 'version'])->name('version');
