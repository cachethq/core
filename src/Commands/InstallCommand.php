<?php

namespace Cachet\Commands;

use Cachet\Database\Seeders\DatabaseSeeder;
use Cachet\Settings\AppSettings;
use Cachet\Settings\Attributes\Description;
use Illuminate\Console\Command;
use Illuminate\Support\Sleep;
use ReflectionClass;
use ReflectionProperty;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\text;


class InstallCommand extends Command
{
    protected $name = 'cachet:install';

    protected $description = 'Install Cachet';

    public function handle(AppSettings $settings)
    {
        intro('Welcome to the Cachet installer!');

        Sleep::for(2)->seconds();

        $this->call('migrate', ['--seed' => true, '--seeder' => DatabaseSeeder::class]);

        if (confirm('Do you want to configure Cachet before installing?', true)) {
            info('Configuring Cachet...');
            $this->configureEnvironmentSettings();
            $settings = $this->configureDatabaseSettings($settings);
        }

        info('Installing Cachet...');

        $this->call('filament:assets');

        $settings->save();

        info('Cachet is installed ⚡');

        return Command::SUCCESS;
    }

    protected function configureEnvironmentSettings(): void
    {
        //@todo configure environment variables inside cachet.php
    }

    protected function configureDatabaseSettings(AppSettings $settings): AppSettings
    {
        collect(
            (new ReflectionClass($settings))->getProperties(ReflectionProperty::IS_PUBLIC)
        )
            ->filter(fn (ReflectionProperty $property) => array_key_exists($property->getName(), $settings->installable()) )
            ->each(function (ReflectionProperty $property) use ($settings) {
                $description = $property->getAttributes(Description::class)[0]->getArguments()[0];
                $value = match($property->getType()?->getName()) {
                    'bool' => confirm($description ?? $property->getName()),
                    default => text($description ?? $property->getName(), default: $property->getDefaultValue() ?? '', required: true),
                };

                $settings->{$property->getName()} = $value;
            })
            ->pluck('name');

        return $settings;
    }
}