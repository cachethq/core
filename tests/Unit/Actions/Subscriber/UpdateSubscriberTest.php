<?php

use Cachet\Actions\Subscriber\UpdateSubscriber;
use Cachet\Models\Component;
use Cachet\Models\Subscriber;

it('can update a subscriber\'s email address', function () {
    $subscriber = Subscriber::factory()->create();

    app(UpdateSubscriber::class)->handle($subscriber, email: 'james@alt-three.com');

    expect($subscriber->fresh())
        ->email->toBe('james@alt-three.com');
});

it('can update a subscriber without passing an email address', function () {
    $subscriber = Subscriber::factory()->verified()->create([
        'email' => 'james@alt-three.com',
    ]);

    app(UpdateSubscriber::class)->handle($subscriber);

    expect($subscriber->fresh())
        ->email->toBe('james@alt-three.com')
        ->verified_at->not()->toBeNull();
});

it('does not reset the verified_at column when updating a subscriber without changing email', function () {
    $subscriber = Subscriber::factory()->verified()->create([
        'email' => 'james@alt-three.com',
    ]);

    app(UpdateSubscriber::class)->handle($subscriber, email: 'james@alt-three.com');

    expect($subscriber->fresh())
        ->email->toBe('james@alt-three.com')
        ->verified_at->not()->toBeNull();
});

it('resets the verified_at and verify_code columns when changing email', function () {
    $subscriber = Subscriber::factory()->verified()->create([
        'email' => 'james@alt-three.com',
    ]);

    $verifyCode = $subscriber->verify_code;

    app(UpdateSubscriber::class)->handle($subscriber, email: 'james@cachethq.io');

    expect($subscriber->fresh())
        ->email->toBe('james@cachethq.io')
        ->verified_at->toBeNull()
        ->verify_code->not()->toBe($verifyCode);
});

it('can update a subscriber\'s component subscriptions', function () {
    [$componentA, $componentB] = Component::factory()->count(2)->create();
    $subscriber = Subscriber::factory()->hasComponents()->create();

    expect($subscriber->components)
        ->toHaveCount(1);

    app(UpdateSubscriber::class)->handle($subscriber, components: [
        $componentA->id, $componentB->id,
    ]);

    expect($subscriber->fresh())
        ->components->toHaveCount(2)
        ->and($subscriber->components()->first())
        ->toBeInstanceOf(Component::class);
});
