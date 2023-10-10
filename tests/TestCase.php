<?php

namespace Cachet\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase, WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Cachet\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->withoutApiRateLimiting();
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set([
            'auth.providers.users.model' => 'Workbench\\App\\User',
            'database.default' => 'testing',
            // 'query-builder.request_data_source' => 'body',
        ]);
    }

    /**
     * Overrides the rate limiting defined by workbench.
     */
    protected function withoutApiRateLimiting(): void
    {
        RateLimiter::for('api', fn () => null);
    }
}
