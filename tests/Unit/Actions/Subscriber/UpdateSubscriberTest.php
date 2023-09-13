<?php

use Cachet\Actions\Subscriber\UpdateSubscriber;
use Cachet\Models\Subscriber;

it('can update a subscriber\'s email address', function () {
    $subscriber = Subscriber::factory()->create();

    UpdateSubscriber::run($subscriber, email: 'james@alt-three.com');

    expect($subscriber->fresh())
        ->email->toBe('james@alt-three.com');
});

it('can update a subscriber without passing an email address', function () {
    $subscriber = Subscriber::factory()->verified()->create([
        'email' => 'james@alt-three.com',
    ]);

    UpdateSubscriber::run($subscriber);

    expect($subscriber->fresh())
        ->email->toBe('james@alt-three.com')
        ->verified_at->not()->toBeNull();
});

it('updating a subscriber but not changing email does not reset the verified_at column', function () {
    $subscriber = Subscriber::factory()->verified()->create([
        'email' => 'james@alt-three.com',
    ]);

    UpdateSubscriber::run($subscriber, email: 'james@alt-three.com');

    expect($subscriber->fresh())
        ->email->toBe('james@alt-three.com')
        ->verified_at->not()->toBeNull();
});

it('changing a subscriber\'s email resets the verified_at and verify_code columns', function () {
    $subscriber = Subscriber::factory()->verified()->create([
        'email' => 'james@alt-three.com',
    ]);

    $verifyCode = $subscriber->verify_code;

    UpdateSubscriber::run($subscriber, email: 'james@cachethq.io');

    expect($subscriber->fresh())
        ->email->toBe('james@cachethq.io')
        ->verified_at->toBeNull()
        ->verify_code->not()->toBe($verifyCode);
});
