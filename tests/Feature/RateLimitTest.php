<?php

use Illuminate\Support\Facades\Route;
use Pest\Expectation;

use function Pest\Laravel\getJson;

beforeEach(function () {
    Route::get('/test', fn () => response()->json())
        ->middleware('throttle:cachet-api');
});

it('API routes are rate limited to 300 requests a minute', function () {
    for ($i = 0; $i <= 300; $i++) {
        $response = getJson('/test');

        expect($response->status())
            ->when($i < 300, fn (Expectation $value) => $value->toBe(200))
            ->when($i === 300, fn (Expectation $value) => $value->toBe(429));
    }
});

test('API rate limiting can be configured via config', function (int $limit) {
    $this->app['config']->set(['cachet.api_rate_limit' => $limit]);

    for ($i = 0; $i <= $limit; $i++) {
        $response = getJson('test');

        expect($response->status())
            ->when($i < $limit, fn (Expectation $value) => $value->toBe(200))
            ->when($i === $limit, fn (Expectation $value) => $value->toBe(429));
    }
})->with([
    1,
    10,
    60,
    100,
    1000,
]);
