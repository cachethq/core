<?php

declare(strict_types=1);

namespace Cachet\Tests\Feature\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    assertTrue($user->fresh()->hasVerifiedEmail());
    $response->assertRedirect(route('cachet.status-page', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    actingAs($user)->get($verificationUrl);
    assertFalse($user->fresh()->hasVerifiedEmail());
});
