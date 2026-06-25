<?php

use Cachet\Data\Checks\CheckResult;
use Cachet\Enums\ComponentStatusEnum;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

function checkResponse(int $status): Response
{
    return new Response(new Psr7Response($status));
}

it('maps a first-try success to operational', function () {
    $result = CheckResult::fromResponse(checkResponse(200), attempts: 1, transferTime: 0.25);

    expect($result->status)->toBe(ComponentStatusEnum::operational)
        ->and($result->successful)->toBeTrue()
        ->and($result->responseCode)->toBe(200)
        ->and($result->responseTime)->toBe(250);
});

it('maps a retried success to performance issues', function () {
    $result = CheckResult::fromResponse(checkResponse(200), attempts: 2);

    expect($result->status)->toBe(ComponentStatusEnum::performance_issues)
        ->and($result->successful)->toBeTrue();
});

it('maps a 4xx response to a partial outage', function () {
    $result = CheckResult::fromResponse(checkResponse(404), attempts: 1);

    expect($result->status)->toBe(ComponentStatusEnum::partial_outage)
        ->and($result->successful)->toBeFalse();
});

it('maps a 5xx response to a major outage', function () {
    $result = CheckResult::fromResponse(checkResponse(500), attempts: 1);

    expect($result->status)->toBe(ComponentStatusEnum::major_outage)
        ->and($result->successful)->toBeFalse();
});

it('maps a connection exception to a partial outage', function () {
    $result = CheckResult::fromException(new ConnectionException('timeout'));

    expect($result->status)->toBe(ComponentStatusEnum::partial_outage)
        ->and($result->successful)->toBeFalse()
        ->and($result->error)->toBe('timeout');
});

it('maps a server-side request exception to a major outage', function () {
    $result = CheckResult::fromException(new RequestException(checkResponse(503)));

    expect($result->status)->toBe(ComponentStatusEnum::major_outage)
        ->and($result->responseCode)->toBe(503);
});
