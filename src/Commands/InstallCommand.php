<?php

namespace Cachet\Commands;

use Cachet\Settings\AppSettings;
use Cachet\Settings\Attributes\Description;
use Illuminate\Console\Command;
use Illuminate\Support\Sleep;
use ReflectionClass;
use ReflectionProperty;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;


class InstallCommand extends Command
{
    protected $name = 'cachet:install';

    protected $description = 'Install Cachet';

    public function handle(AppSettings $settings)
    {
        info('Welcome to the Cachet installer!');

        Sleep::for(2)->seconds();

        if (confirm('Do you want to configure Cachet before installing?', true)) {
            info('Configuring Cachet...');
            $this->configureDatabaseSettings($settings);
        }

        info('Installing Cachet...');

        info('Cachet has been installed successfully!');

        return Command::SUCCESS;
    }

    protected function configureDatabaseSettings(AppSettings $settings): void
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

        $settings->save();
    }
}