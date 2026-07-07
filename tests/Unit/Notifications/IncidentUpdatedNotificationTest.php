<?php

namespace Tests\Unit\Notifications;

use Cachet\Cachet;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Models\Update;
use Cachet\Notifications\IncidentUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;

it('renders the themed incident update email with an unsubscribe link', function () {
    $incident = Incident::factory()->create(['name' => 'API latency']);

    $update = new Update([
        'message' => 'The issue has been **identified** and a fix is underway.',
        'status' => IncidentStatusEnum::identified,
    ]);
    $incident->updates()->save($update);

    $subscriber = Subscriber::factory()->verified()->create();

    $notification = new IncidentUpdatedNotification($update->fresh());

    expect($notification)->toBeInstanceOf(ShouldQueue::class);

    $mail = $notification->toMail($subscriber);

    $html = (new Markdown(app('view'), ['theme' => Cachet::MAIL_THEME]))
        ->render($mail->markdown, $mail->viewData)
        ->toHtml();

    expect($mail->subject)->toBe(__('cachet::subscriber.mail.incident_updated.subject', ['incident' => 'API latency']))
        ->and($mail->theme)->toBe(Cachet::MAIL_THEME)
        ->and($html)->toContain('API latency')
        ->toContain(IncidentStatusEnum::identified->getLabel())
        ->toContain('identified</strong>')
        ->toContain(e(route('cachet.status-page.incident', ['incident' => $incident])))
        ->toContain(e($subscriber->unsubscribeUrl()));
});
