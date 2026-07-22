<?php

namespace Cachet\Tests\Feature;

use Cachet\CachetCoreServiceProvider;
use Cachet\Tests\TestCase;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use ReflectionClass;

class ProviderConfigTogglesTest extends TestCase
{
    protected function disableMigrations(Application $app): void
    {
        $app['config']->set('cachet.run_migrations', false);
    }

    protected function disableSchedules(Application $app): void
    {
        $app['config']->set('cachet.register_schedules', false);
    }

    #[DefineEnvironment('disableMigrations')]
    public function test_it_does_not_load_the_package_migrations_when_disabled(): void
    {
        $corePath = realpath(dirname((new ReflectionClass(CachetCoreServiceProvider::class))->getFileName()).'/../database/migrations');

        $paths = array_map(fn (string $path) => realpath($path), $this->app['migrator']->paths());

        $this->assertNotContains($corePath, $paths);
    }

    #[DefineEnvironment('disableSchedules')]
    public function test_it_does_not_register_the_package_schedules_when_disabled(): void
    {
        $commands = collect($this->app->make(Schedule::class)->events())
            ->map(fn ($event) => $event->command ?? '')
            ->filter(fn (string $command) => str_contains($command, 'cachet:'));

        $this->assertEmpty($commands);
    }
}
