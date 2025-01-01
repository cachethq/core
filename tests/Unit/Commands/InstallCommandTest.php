<?php

namespace Tests\Unit\Commands;

use Cachet\Settings\AppSettings;
use Illuminate\Support\Facades\File;

it('runs install command successfully without configuration', function () {
    $this->artisan('cachet:install')
        ->expectsOutputToContain('Welcome to the Cachet installer!')
        ->expectsConfirmation('Do you want to configure Cachet before installing?', 'no')
        ->expectsConfirmation('Do you wish to seed any sample data?', 'no')
        ->expectsConfirmation('Do you want to create a new user?', 'no')
        ->expectsOutputToContain('Installing Cachet...')
        ->expectsOutputToContain('Cachet is installed ⚡')
        ->assertSuccessful();
});

it('updates app settings and config file when configuration is passed', function () {
    File::copy(base_path('.env.example'), base_path('.env'));

    $this->artisan('cachet:install')
        ->expectsOutputToContain('Welcome to the Cachet installer!')
        ->expectsConfirmation('Do you want to configure Cachet before installing?', 'yes')
        ->expectsOutputToContain('Configuring Cachet...')
        ->expectsQuestion('Which path do you want Cachet to be accessible from?', '/status')
        ->expectsQuestion('What will the title of your status page be?', 'Laravel Envoyer')
        ->expectsQuestion('Which database connection do you wish to use for Cachet?', 'default')
        ->expectsQuestion('Do you wish to send anonymous data to cachet to help us understand how Cachet is used?', true)
        ->expectsQuestion('What is the name of your application?', 'Laravel Envoyer')
        ->expectsConfirmation('Do you want to show support for Cachet?', 'yes')
        ->expectsQuestion('What timezone is is the application located in?', 'America/New_York')
        ->expectsConfirmation('Would you like to show your timezone on the status page?', 'yes')
        ->expectsConfirmation('Would you like to only show the days with disruption?', 'yes')
        ->expectsQuestion('How many incident days should be shown in the timeline?', 14)
        ->expectsConfirmation('Should the dashboard login link be shown?', 'no')
        ->expectsQuestion('Major outage threshold %', 50)
        ->expectsConfirmation('Do you wish to seed any sample data?', 'no')
        ->expectsConfirmation('Do you want to create a new user?', 'no')
        ->expectsOutputToContain('Installing Cachet...')
        ->expectsOutputToContain('Cachet is installed ⚡')
        ->assertSuccessful();

    $envContent = file_get_contents(base_path('.env'));

    expect($envContent)
        ->toContain('CACHET_PATH=/status')
        ->toContain('CACHET_TITLE="Laravel Envoyer"')
        ->toContain('CACHET_DB_CONNECTION=default')
        ->toContain('CACHET_BEACON=true');

    $settings = app(AppSettings::class);
    expect($settings->name)->toBe('Laravel Envoyer')
        ->and($settings->show_support)->toBeTrue()
        ->and($settings->timezone)->toBe('America/New_York')
        ->and($settings->show_timezone)->toBeTrue()
        ->and($settings->only_disrupted_days)->toBeTrue()
        ->and($settings->incident_days)->toBe(14)
        ->and($settings->dashboard_login_link)->toBeFalse()
        ->and($settings->major_outage_threshold)->toBe(50);
});

afterAll(function() {
    File::delete(base_path('.env'));
});