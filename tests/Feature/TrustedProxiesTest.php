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

    protected function trustAllProxies(Application $app): void
    {
        $app['config']->set('cachet.trusted_proxies', '*');
    }

    #[DefineEnvironment('trustAllProxies')]
    public function test_it_respects_the_forwarded_scheme_from_trusted_proxies(): void
    {
        $request = Request::create('/', server: [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'REMOTE_ADDR' => '127.0.0.1',
        ]);

        app(TrustProxies::class)->handle($request, function (Request $request): Response {
            $this->assertTrue($request->isSecure());

            return new Response;
        });
    }
}
