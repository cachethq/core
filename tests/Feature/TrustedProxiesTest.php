<?php

namespace Cachet\Tests\Feature;

use Cachet\Tests\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Symfony\Component\HttpFoundation\Response;

class TrustedProxiesTest extends TestCase
{
    protected function tearDown(): void
    {
        TrustProxies::flushState();

        parent::tearDown();
    }

    protected function configureCachetTrustedProxies(Application $app): void
    {
        $app['config']->set('cachet.trusted_proxies', '*');
    }

    #[DefineEnvironment('configureCachetTrustedProxies')]
    public function test_it_does_not_change_the_hosts_trusted_proxies(): void
    {
        $request = Request::create('/', server: [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'REMOTE_ADDR' => '127.0.0.1',
        ]);

        app(TrustProxies::class)->handle($request, function (Request $request): Response {
            $this->assertFalse($request->isSecure());

            return new Response;
        });
    }
}
