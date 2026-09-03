<?php

namespace Cachet\Tests\Feature;

use Cachet\Cachet;
use Cachet\CachetCoreServiceProvider;
use Cachet\Tests\TestCase;
use Filament\Facades\Filament;
use Illuminate\Console\Scheduling\Schedule;
use ReflectionClass;

class CachetDisabledTest extends TestCase
{
    private string|false $previousEnabled;

    protected function setUp(): void
    {
        $this->previousEnabled = getenv('CACHET_ENABLED');
        putenv('CACHET_ENABLED=false');
        $_ENV['CACHET_ENABLED'] = 'false';
        $_SERVER['CACHET_ENABLED'] = 'false';

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->previousEnabled === false) {
            putenv('CACHET_ENABLED');
            unset($_ENV['CACHET_ENABLED'], $_SERVER['CACHET_ENABLED']);

            return;
        }

        putenv('CACHET_ENABLED='.$this->previousEnabled);
        $_ENV['CACHET_ENABLED'] = $this->previousEnabled;
        $_SERVER['CACHET_ENABLED'] = $this->previousEnabled;
    }

    public function test_it_registers_nothing_when_cachet_is_disabled(): void
    {
        $corePath = realpath(dirname((new ReflectionClass(CachetCoreServiceProvider::class))->getFileName()).'/../database/migrations');
        $migrationPaths = array_map(fn (string $path) => realpath($path), $this->app['migrator']->paths());
        $scheduledCommands = collect($this->app->make(Schedule::class)->events())
            ->map(fn ($event) => $event->command ?? '')
            ->filter(fn (string $command) => str_contains($command, 'cachet:'));
        $routeNames = collect($this->app['router']->getRoutes()->getRoutesByName())->keys();

        $this->assertFalse($this->app->bound(Cachet::class));
        $this->assertNotContains($corePath, $migrationPaths);
        $this->assertEmpty($scheduledCommands);
        $this->assertFalse($routeNames->contains(fn (string $name) => str_starts_with($name, 'cachet.')));
        $this->assertArrayNotHasKey('cachet', Filament::getPanels());
    }
}
