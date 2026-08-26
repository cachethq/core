<?php

use Cachet\Actions\Integrations\ResolvePublicUrl;

it('resolves a public IP URL for a pinned request', function () {
    $resolvedUrl = app(ResolvePublicUrl::class)->handle('https://93.184.216.34/status/');

    expect($resolvedUrl->url)->toBe('https://93.184.216.34/status/json')
        ->and($resolvedUrl->curlResolve())->toBe('93.184.216.34:443:93.184.216.34');
});

it('brackets public IPv6 addresses for a pinned request', function () {
    $resolvedUrl = app(ResolvePublicUrl::class)->handle('https://[2606:4700:4700::1111]/');

    expect($resolvedUrl->url)->toBe('https://[2606:4700:4700::1111]/json')
        ->and($resolvedUrl->curlResolve())->toBe('[2606:4700:4700::1111]:443:[2606:4700:4700::1111]');
});

it('rejects non-public destinations', function (string $url) {
    app(ResolvePublicUrl::class)->handle($url);
})->throws(InvalidArgumentException::class)->with([
    'loopback IPv4' => 'http://127.0.0.1',
    'private IPv4' => 'http://10.0.0.1',
    'link-local IPv4' => 'http://169.254.169.254',
    'multicast IPv4' => 'http://224.0.0.1',
    'loopback IPv6' => 'http://[::1]',
    'private IPv6' => 'http://[fc00::1]',
    'link-local IPv6' => 'http://[fe80::1]',
    'site-local IPv6' => 'http://[fec0::1]',
]);

it('rejects unsupported URL schemes', function () {
    app(ResolvePublicUrl::class)->handle('file:///etc/passwd');
})->throws(InvalidArgumentException::class);
