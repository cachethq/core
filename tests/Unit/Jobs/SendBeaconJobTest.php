<?php

use Cachet\Cachet;
use Cachet\Jobs\SendBeaconJob;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Cachet\Models\Schedule;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('will not send the beacon if disabled', function () {
    Http::fake();

    config(['cachet.beacon' => false]);

    dispatch(new SendBeaconJob);

    Http::assertNothingSent();
});

it('sends telemetry data', function () {
    Http::fake([
        'https://cachethq.io/beacon' => Http::response([]),
    ]);

    config(['cachet.beacon' => true]);

    Component::factory()->count(1)->create();
    Incident::factory()->count(2)->create();
    Metric::factory()->count(3)->create();
    Schedule::factory()->count(4)->create();

    dispatch(new SendBeaconJob);

    Http::assertSent(function (Request $request) {
        return $request['version'] === Cachet::version() &&
            $request['docker'] === false &&
            $request['data']['components'] === 1 &&
            $request['data']['incidents'] === 2 &&
            $request['data']['metrics'] === 3 &&
            $request['data']['schedules'] === 4;
    });
    Http::assertSentCount(1);
});
