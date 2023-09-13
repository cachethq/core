<?php

use Cachet\Cachet;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('sets the Cachet user-agent header on outgoing requests', function () {
    Http::fake(fn (Request $request) => Http::response($request->header('User-Agent')[0]));

    $response = Http::get('https://cachethq.io');

    expect($response->body())->toBe(Cachet::USER_AGENT);
});
