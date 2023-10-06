<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Workbench\Database\Factories\UserFactory;

test('email verification screen can be rendered', function () {
    $user = UserFactory::new()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get('/status/verify-email');

    $response->assertStatus(200);
});

test('email can be verified', function () {
    $user = UserFactory::new()->create([
        'email_verified_at' => null,
    ]);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'cachet.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(config('cachet.routes.dashboard', '/status/dashboard').'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = UserFactory::new()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'cachet.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
