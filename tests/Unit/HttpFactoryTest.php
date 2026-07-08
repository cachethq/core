<?php

use Cachet\Cachet;
use Cachet\Jobs\SendBeaconJob;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('sets the Cachet user-agent header on beacon requests', function () {
    config()->set('cachet.beacon', true);

    Http::fake(['https://cachethq.io/*' => Http::response()]);

    (new SendBeaconJob)->handle();

    Http::assertSent(fn (Request $request) => $request->header('User-Agent')[0] === Cachet::USER_AGENT);
});

it('does not override the user-agent of non-Cachet requests', function () {
    Http::fake(fn (Request $request) => Http::response($request->header('User-Agent')[0]));

    $response = Http::get('https://example.com');

    expect($response->body())->not->toBe(Cachet::USER_AGENT);
});
