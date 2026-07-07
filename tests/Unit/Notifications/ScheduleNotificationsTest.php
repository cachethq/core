<?php

namespace Tests\Unit\Notifications;

use Cachet\Cachet;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Models\Update;
use Cachet\Notifications\NewScheduleNotification;
use Cachet\Notifications\ScheduleUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;

it('renders the themed new schedule email with an unsubscribe link', function () {
    $schedule = Schedule::factory()->create([
        'name' => 'Database maintenance',
        'message' => 'We will be upgrading the **database**.',
        'scheduled_at' => now()->addDay(),
    ]);
    $subscriber = Subscriber::factory()->verified()->create();

    $notification = new NewScheduleNotification($schedule);

    expect($notification)->toBeInstanceOf(ShouldQueue::class);

    $mail = $notification->toMail($subscriber);

    $html = (new Markdown(app('view'), ['theme' => Cachet::MAIL_THEME]))
        ->render($mail->markdown, $mail->viewData)
        ->toHtml();

    expect($mail->subject)->toBe(__('cachet::subscriber.mail.new_schedule.subject', ['schedule' => 'Database maintenance']))
        ->and($html)->toContain('Database maintenance')
        ->toContain($schedule->scheduled_at->toDayDateTimeString())
        ->toContain('database</strong>')
        ->toContain(e(route('cachet.status-page.schedule', ['schedule' => $schedule])))
        ->toContain(e($subscriber->unsubscribeUrl()));
});

it('renders the themed schedule completed email with an unsubscribe link', function () {
    $schedule = Schedule::factory()->create([
        'name' => 'Database maintenance',
        'scheduled_at' => now()->subHours(2),
        'completed_at' => now(),
    ]);
    $subscriber = Subscriber::factory()->verified()->create();

    $mail = (new \Cachet\Notifications\ScheduleCompletedNotification($schedule))->toMail($subscriber);

    $html = (new Markdown(app('view'), ['theme' => Cachet::MAIL_THEME]))
        ->render($mail->markdown, $mail->viewData)
        ->toHtml();

    expect($mail->subject)->toBe(__('cachet::subscriber.mail.schedule_completed.subject', ['schedule' => 'Database maintenance']))
        ->and($html)->toContain('Database maintenance')
        ->toContain($schedule->completed_at->toDayDateTimeString())
        ->toContain(e($subscriber->unsubscribeUrl()));
});

it('renders the themed schedule update email with an unsubscribe link', function () {
    $schedule = Schedule::factory()->create(['name' => 'Database maintenance']);

    $update = new Update(['message' => 'Maintenance is **complete**.']);
    $schedule->updates()->save($update);

    $subscriber = Subscriber::factory()->verified()->create();

    $notification = new ScheduleUpdatedNotification($update->fresh());

    expect($notification)->toBeInstanceOf(ShouldQueue::class);

    $mail = $notification->toMail($subscriber);

    $html = (new Markdown(app('view'), ['theme' => Cachet::MAIL_THEME]))
        ->render($mail->markdown, $mail->viewData)
        ->toHtml();

    expect($mail->subject)->toBe(__('cachet::subscriber.mail.schedule_updated.subject', ['schedule' => 'Database maintenance']))
        ->and($html)->toContain('Database maintenance')
        ->toContain('complete</strong>')
        ->toContain(e($subscriber->unsubscribeUrl()));
});
