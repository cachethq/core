<?php

namespace Tests\Unit\Settings;

use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewIncidentNotification;
use Cachet\Settings\MailSettings;

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

it('applies Cachet mail settings without changing the host mail defaults', function () {
    config()->set([
        'mail.default' => 'log',
        'mail.from.address' => 'host@example.com',
        'mail.from.name' => 'Host App',
    ]);

    app(MailSettings::class)->fill([
        'mailer' => 'smtp',
        'host' => 'smtp.example.com',
        'from_address' => 'status@example.com',
        'from_name' => 'Example Status',
    ])->save();

    $message = (new NewIncidentNotification(Incident::factory()->create()))
        ->toMail(Subscriber::factory()->create());

    expect(config('mail.default'))->toBe('log')
        ->and(config('mail.mailers.cachet.host'))->toBe('smtp.example.com')
        ->and(config('mail.from.address'))->toBe('host@example.com')
        ->and(config('mail.from.name'))->toBe('Host App')
        ->and($message->mailer)->toBe('cachet')
        ->and($message->from)->toBe(['status@example.com', 'Example Status']);
});

it('uses the application mail configuration when Cachet is not configured', function () {
    $default = config('mail.default');

    $message = (new NewIncidentNotification(Incident::factory()->create()))
        ->toMail(Subscriber::factory()->create());

    expect(config('mail.default'))->toBe($default)
        ->and(config('mail.mailers.cachet'))->toBeNull()
        ->and($message->mailer)->toBeNull()
        ->and($message->from)->toBe([]);
});
