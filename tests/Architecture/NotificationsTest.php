<?php

use Cachet\Notifications\VerifySubscriberEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

test('notifications test')
    ->expect('Cachet\Notifications')
    ->toBeClasses()
    ->toExtend(Notification::class)
    ->toImplement(ShouldQueue::class)
    ->toHaveSuffix('Notification')
    ->ignoring(VerifySubscriberEmail::class);
