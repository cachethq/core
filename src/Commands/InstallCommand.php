<?php

namespace Cachet\Commands;

use Cachet\Database\Seeders\DatabaseSeeder;
use Cachet\Settings\AppSettings;
use Cachet\Settings\Attributes\Description;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
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

    public function handle(AppSettings $settings): int
    {
        intro('Welcome to the Cachet installer!');

        $configureCachet = false;

        if (confirm('Do you want to configure Cachet before installing?', true)) {
            info('Configuring Cachet...');
            $this->configureEnvironmentSettings();
            $configureCachet = true;
        }

        Sleep::for(2)->seconds();

        $this->call('migrate', ['--database' => config('cachet.database_connection')]);

        if ($configureCachet) {
            $settings = $this->configureDatabaseSettings($settings);
        }

        if (confirm('Do you wish to seed any sample data?', true)) {
            $this->call(
                'db:seed',
                [
                    'class' => DatabaseSeeder::class,
                    '--database' => config('cachet.database_connection')
                ]
            );
        }

        if (confirm('Do you want to create a new user?', false)) {
            $this->call('cachet:make:user');
        }

        info('Installing Cachet...');

        $this->call('filament:assets');

        $settings->save();

        info('Cachet is installed ⚡');

        return Command::SUCCESS;
    }

    protected function configureEnvironmentSettings(): void
    {
        $path = text(
            'Which path do you want Cachet to be accessible from?',
            default: config('cachet.path')
        );

        $title = text(
            'What will the title of your status page be?',
            default: config('cachet.title')
        );

        $connection = text(
            'Which database connection do you wish to use for Cachet?',
            default: config('cachet.database_connection')
        );

        $beacon = confirm(
            'Do you wish to send anonymous data to cachet to help us understand how Cachet is used?',
            default: config('cachet.beacon')
        );

        // Override default connection to Laravel Settings saves to correct connection
        app('db')->setDefaultConnection(config('cachet.database_connection'));

        $this->writeEnv([
            'CACHET_PATH' => $path,
            'CACHET_TITLE' => $title,
            'CACHET_DB_CONNECTION' => $connection,
            'CACHET_BEACON' => $beacon,
        ]);
    }

    protected function writeEnv(array $values): void
    {
        $environmentFile = app()->environmentFile();
        $environmentPath = app()->environmentPath();
        $fullPath = $environmentPath . '/' . $environmentFile;

        $envFileContents = File::lines($fullPath)->collect();

        foreach ($values as $key => $value) {
            $existingKey = $envFileContents->search(function ($line) use ($key) {
                return Str::contains($line, $key, true);
            });

            $value = match (true) {
                Str::contains($value, ' ') => Str::wrap($value,'"'),
                $value === true => 'true',
                $value === false => 'false',
                default => $value
            };

            if ($existingKey === false) {
                $envFileContents->push($key . '=' . $value);
            } else {
                $envFileContents->put($existingKey, $key . '=' . $value);
            }
        }

        File::put($fullPath, $envFileContents->implode("\n"));
    }

    protected function configureDatabaseSettings(AppSettings $settings): AppSettings
    {
        collect(
            (new ReflectionClass($settings))->getProperties(ReflectionProperty::IS_PUBLIC)
        )
            ->filter(fn (ReflectionProperty $property) => array_key_exists($property->getName(), $settings->installable()) )
            ->each(function (ReflectionProperty $property) use ($settings) {
                $descriptionAttribute = $property->getAttributes(Description::class);

                if (empty($descriptionAttribute)) {
                    return;
                }

                $descriptionAttributeClass = $descriptionAttribute[0]->newInstance();
                $default = $descriptionAttributeClass->default();
                $required = $descriptionAttributeClass->required();

                if ($required === false) {
                    return;
                }

                /** @var \ReflectionNamedType $namedType */
                $namedType = $property->getType();

                $value = match($namedType->getName()) {
                    'bool' => confirm($default ?: $property->getName()),
                    default => text($default ?: $property->getName(), default: $property->getDefaultValue() ?: '', required: true),
                };

                $settings->{$property->getName()} = $value;
            })
            ->pluck('name');

        return $settings;
    }
}