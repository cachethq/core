<?php

namespace Tests\Unit\Commands;

use Cachet\Settings\AppSettings;

it('runs install command successfully without configuration', function () {
    $this->artisan('cachet:install')
        ->expectsOutputToContain('Welcome to the Cachet installer!')
        ->expectsConfirmation('Do you want to configure Cachet before installing?', 'no')
        ->expectsOutputToContain('Installing Cachet...')
        ->expectsOutputToContain('Cachet is installed ⚡')
        ->assertSuccessful();
});

it('updates app settings when configuration is passed', function () {
    $this->artisan('cachet:install')
        ->expectsOutputToContain('Welcome to the Cachet installer!')
        ->expectsConfirmation('Do you want to configure Cachet before installing?', 'yes')
        ->expectsOutputToContain('Configuring Cachet...')
        ->expectsQuestion('What is the name of your application?', 'Laravel Envoyer')
        ->expectsQuestion('What is your application about?', 'Zero downtime deployment tool.')
        ->expectsConfirmation('Do you want to show support for Cachet?', 'yes')
        ->expectsQuestion('What timezone is is the application located in?', 'America/New_York')
        ->expectsConfirmation('Would you like to show your timezone on the status page?', 'yes')
        ->expectsConfirmation('Would you like to only show the days with disruption?', 'yes')
        ->expectsQuestion('How many incident days should be shown in the timeline?', 14)
        ->expectsQuestion('After how many seconds should the status page automatically refresh?', 10)
        ->expectsConfirmation('Should the dashboard login link be shown?', 'no')
        ->expectsQuestion('Major outage threshold %', 50)
        ->expectsOutputToContain('Installing Cachet...')
        ->expectsOutputToContain('Cachet is installed ⚡')
        ->assertSuccessful();

    $settings = app(AppSettings::class);
    expect($settings->name)->toBe('Laravel Envoyer')
        ->and($settings->about)->toBe('Zero downtime deployment tool.')
        ->and($settings->show_support)->toBeTrue()
        ->and($settings->timezone)->toBe('America/New_York')
        ->and($settings->show_timezone)->toBeTrue()
        ->and($settings->only_disrupted_days)->toBeTrue()
        ->and($settings->incident_days)->toBe(14)
        ->and($settings->refresh_rate)->toBe(10)
        ->and($settings->dashboard_login_link)->toBeFalse()
        ->and($settings->major_outage_threshold)->toBe(50);
});