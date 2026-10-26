<?php

namespace Tests\Feature\Http\Controllers\Subscribers;

use Cachet\Enums\CaptchaProviderEnum;
use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;
use Cachet\Settings\CaptchaSettings;
use Cachet\Settings\MailSettings;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\post;

beforeEach(function () {
    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();

    Notification::fake();
});

function useCaptcha(CaptchaProviderEnum $provider, ?string $siteKey = null, ?string $secretKey = null): void
{
    $settings = app(CaptchaSettings::class);
    $settings->provider = $provider;
    $settings->site_key = $siteKey;
    $settings->secret_key = $secretKey;
    $settings->altcha_complexity = 1000;
    $settings->save();
}

/**
 * Solve an ALTCHA challenge the way the browser does.
 *
 * @param  array{algorithm: string, challenge: string, maxnumber: int, salt: string, signature: string}  $challenge
 */
function solveAltcha(array $challenge): string
{
    foreach (range(0, $challenge['maxnumber']) as $number) {
        if (hash('sha256', $challenge['salt'].$number) === $challenge['challenge']) {
            return base64_encode(json_encode([
                'algorithm' => $challenge['algorithm'],
                'challenge' => $challenge['challenge'],
                'number' => $number,
                'salt' => $challenge['salt'],
                'signature' => $challenge['signature'],
            ]));
        }
    }

    throw new \RuntimeException('Unsolvable challenge.');
}

it('does not require a captcha by default', function () {
    get(route('cachet.subscribers.create'))
        ->assertOk()
        ->assertDontSee('data-slot="captcha"', escape: false);

    post(route('cachet.subscribers.store'), ['email' => 'james@example.com'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('cachet_subscriber_status', 'subscribed');

    expect(Subscriber::query()->count())->toBe(1);
});

it('does not serve challenges when altcha is not the provider', function () {
    getJson(route('cachet.subscribers.captcha'))->assertNotFound();
});

describe('altcha', function () {
    beforeEach(fn () => useCaptcha(CaptchaProviderEnum::altcha));

    it('renders the altcha widget', function () {
        get(route('cachet.subscribers.create'))
            ->assertOk()
            ->assertSee('data-captcha="altcha"', escape: false)
            ->assertSee('x-data="cachetAltcha(', escape: false)
            ->assertSee(str_replace('/', '\/', route('cachet.subscribers.captcha')), escape: false)
            ->assertDontSee('crypto.subtle', escape: false);
    });

    it('serves a signed challenge', function () {
        getJson(route('cachet.subscribers.captcha'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonStructure(['algorithm', 'challenge', 'maxnumber', 'salt', 'signature'])
            ->assertJson(['algorithm' => 'SHA-256', 'maxnumber' => 1000]);
    });

    it('does not serve challenges when subscriptions are not allowed', function () {
        $settings = app(MailSettings::class);
        $settings->allow_subscribers = false;
        $settings->save();

        getJson(route('cachet.subscribers.captcha'))->assertNotFound();
    });

    it('subscribes with a solved challenge', function () {
        $payload = solveAltcha(getJson(route('cachet.subscribers.captcha'))->json());

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', 'altcha' => $payload])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('cachet_subscriber_status', 'subscribed');

        Notification::assertSentTo(Subscriber::query()->sole(), VerifySubscriberEmail::class);
    });

    it('rejects a missing or malformed solution', function (?string $payload) {
        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', 'altcha' => $payload])
            ->assertSessionHasErrors('captcha');

        expect(Subscriber::query()->count())->toBe(0);
        Notification::assertNothingSent();
    })->with([
        'missing' => [null],
        'not base64' => ['%%%'],
        'not json' => [base64_encode('nope')],
    ]);

    it('rejects a solution sent as an array', function () {
        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', 'altcha' => ['x']])
            ->assertSessionHasErrors('captcha');

        expect(Subscriber::query()->count())->toBe(0);
    });

    it('rejects a wrong number', function () {
        $challenge = getJson(route('cachet.subscribers.captcha'))->json();
        $payload = json_decode(base64_decode(solveAltcha($challenge)), true);
        $payload['number']++;

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', 'altcha' => base64_encode(json_encode($payload))])
            ->assertSessionHasErrors('captcha');

        expect(Subscriber::query()->count())->toBe(0);
    });

    it('rejects a challenge that was not signed by Cachet', function () {
        $salt = 'abc?expires='.(now()->getTimestamp() + 60);
        $payload = solveAltcha([
            'algorithm' => 'SHA-256',
            'challenge' => hash('sha256', $salt.'5'),
            'maxnumber' => 10,
            'salt' => $salt,
            'signature' => str_repeat('0', 64),
        ]);

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', 'altcha' => $payload])
            ->assertSessionHasErrors('captcha');
    });

    it('rejects an expired challenge', function () {
        $payload = solveAltcha(getJson(route('cachet.subscribers.captcha'))->json());

        $this->travel(301)->seconds();

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', 'altcha' => $payload])
            ->assertSessionHasErrors('captcha');
    });

    it('rejects a solution that was already used', function () {
        $payload = solveAltcha(getJson(route('cachet.subscribers.captcha'))->json());

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', 'altcha' => $payload])
            ->assertSessionHasNoErrors();

        post(route('cachet.subscribers.store'), ['email' => 'someone-else@example.com', 'altcha' => $payload])
            ->assertSessionHasErrors('captcha');

        expect(Subscriber::query()->pluck('email')->all())->toBe(['james@example.com']);
    });
});

describe('hosted providers', function () {
    dataset('providers', [
        'turnstile' => [CaptchaProviderEnum::turnstile, 'cf-turnstile', 'cf-turnstile-response', 'https://challenges.cloudflare.com/turnstile/v0/siteverify'],
        'hcaptcha' => [CaptchaProviderEnum::hcaptcha, 'h-captcha', 'h-captcha-response', 'https://api.hcaptcha.com/siteverify'],
        'recaptcha' => [CaptchaProviderEnum::recaptcha, 'g-recaptcha', 'g-recaptcha-response', 'https://www.google.com/recaptcha/api/siteverify'],
    ]);

    it('renders the widget with the site key', function (CaptchaProviderEnum $provider, string $widget) {
        useCaptcha($provider, 'site-key', 'secret-key');

        get(route('cachet.subscribers.create'))
            ->assertOk()
            ->assertSee('class="'.$widget.'" data-sitekey="site-key"', escape: false)
            ->assertDontSee('secret-key');
    })->with('providers');

    it('subscribes when the provider accepts the response', function (CaptchaProviderEnum $provider, string $widget, string $field, string $url) {
        useCaptcha($provider, 'site-key', 'secret-key');
        Http::fake([$url => Http::response(['success' => true])]);

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', $field => 'token'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('cachet_subscriber_status', 'subscribed');

        Http::assertSent(fn (Request $request) => $request->url() === $url
            && $request['secret'] === 'secret-key'
            && $request['response'] === 'token'
            && $request['remoteip'] === '127.0.0.1');

        expect(Subscriber::query()->count())->toBe(1);
    })->with('providers');

    it('rejects a response the provider refuses', function (CaptchaProviderEnum $provider, string $widget, string $field, string $url) {
        useCaptcha($provider, 'site-key', 'secret-key');
        Http::fake([$url => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']])]);

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', $field => 'token'])
            ->assertSessionHasErrors('captcha');

        expect(Subscriber::query()->count())->toBe(0);
        Notification::assertNothingSent();
    })->with('providers');

    it('rejects a missing response without calling the provider', function (CaptchaProviderEnum $provider) {
        useCaptcha($provider, 'site-key', 'secret-key');
        Http::fake();

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com'])
            ->assertSessionHasErrors('captcha');

        Http::assertNothingSent();
    })->with('providers');

    it('fails closed when the provider cannot be reached', function (CaptchaProviderEnum $provider, string $widget, string $field, string $url) {
        useCaptcha($provider, 'site-key', 'secret-key');
        Http::fake([$url => fn () => throw new ConnectionException('timeout')]);

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', $field => 'token'])
            ->assertSessionHasErrors('captcha');

        expect(Subscriber::query()->count())->toBe(0);
    })->with('providers');

    it('fails closed when the provider returns an error', function (CaptchaProviderEnum $provider, string $widget, string $field, string $url) {
        useCaptcha($provider, 'site-key', 'secret-key');
        Http::fake([$url => Http::response(['success' => true], 500)]);

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com', $field => 'token'])
            ->assertSessionHasErrors('captcha');
    })->with('providers');

    it('stays disabled until both keys are set', function (CaptchaProviderEnum $provider) {
        useCaptcha($provider, 'site-key');
        Http::fake();

        get(route('cachet.subscribers.create'))->assertDontSee('data-slot="captcha"', escape: false);

        post(route('cachet.subscribers.store'), ['email' => 'james@example.com'])
            ->assertSessionHasNoErrors();

        Http::assertNothingSent();
    })->with('providers');
});
