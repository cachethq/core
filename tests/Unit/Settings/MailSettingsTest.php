<?php

namespace Tests\Unit\Settings;

use Cachet\CachetCoreServiceProvider;
use Cachet\Settings\MailSettings;
use ReflectionMethod;

it('is not configured until a mailer is chosen', function () {
    expect(app(MailSettings::class)->configured())->toBeFalse();
});

it('builds an smtp mailer configuration', function () {
    $settings = app(MailSettings::class)->fill([
        'mailer' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 2525,
        'username' => 'mailer@example.com',
        'password' => 'super-secret',
    ]);

    expect($settings->toMailerConfig())->toBe([
        'transport' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 2525,
        'username' => 'mailer@example.com',
        'password' => 'super-secret',
        'timeout' => null,
    ]);
});

it('defaults smtp to port 587', function () {
    $settings = app(MailSettings::class)->fill([
        'mailer' => 'smtp',
        'host' => 'smtp.example.com',
    ]);

    expect($settings->toMailerConfig()['port'])->toBe(587);
});

it('builds a sendmail mailer configuration', function () {
    $settings = app(MailSettings::class)->fill(['mailer' => 'sendmail']);

    expect($settings->toMailerConfig())->toBe([
        'transport' => 'sendmail',
        'path' => config('mail.mailers.sendmail.path'),
    ]);
});

it('overrides the application mail configuration when configured', function () {
    app(MailSettings::class)->fill([
        'mailer' => 'smtp',
        'host' => 'smtp.example.com',
        'from_address' => 'status@example.com',
        'from_name' => 'Example Status',
    ])->save();

    $provider = app()->getProvider(CachetCoreServiceProvider::class);
    (new ReflectionMethod($provider, 'configureMail'))->invoke($provider);

    expect(config('mail.default'))->toBe('cachet')
        ->and(config('mail.mailers.cachet.host'))->toBe('smtp.example.com')
        ->and(config('mail.from.address'))->toBe('status@example.com')
        ->and(config('mail.from.name'))->toBe('Example Status');
});

it('leaves the application mail configuration alone when not configured', function () {
    $default = config('mail.default');

    $provider = app()->getProvider(CachetCoreServiceProvider::class);
    (new ReflectionMethod($provider, 'configureMail'))->invoke($provider);

    expect(config('mail.default'))->toBe($default)
        ->and(config('mail.mailers.cachet'))->toBeNull();
});
