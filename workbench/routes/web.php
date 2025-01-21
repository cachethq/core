<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use Cachet\Http\Controllers\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::resource('subscriptions', SubscriptionController::class)->only(['create', 'store']);
