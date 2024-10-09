<?php

use Illuminate\Support\Facades\Bus;

it('will not send the beacon if it is disabled', function () {
    config()->set('cachet.beacon', false);

    $this->artisan('cachet:beacon')
        ->assertExitCode(1);
});

it('will dispatch the send beacon job', function () {
    Bus::fake();

    config()->set('cachet.beacon', true);

    $this->artisan('cachet:beacon')
        ->assertExitCode(0);

    Bus::assertDispatched(\Cachet\Jobs\SendBeaconJob::class);
});
