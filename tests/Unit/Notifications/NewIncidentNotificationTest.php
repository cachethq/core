<?php

namespace Tests\Unit\Notifications;

use Cachet\Cachet;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Notifications\NewIncidentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;

it('is queued', function () {
    expect(new NewIncidentNotification(Incident::factory()->create()))
        ->toBeInstanceOf(ShouldQueue::class);
});

it('renders the themed incident email with an unsubscribe link', function () {
    $incident = Incident::factory()->create([
        'name' => 'API latency',
        'message' => 'We are **investigating** elevated error rates.',
    ]);
    $subscriber = Subscriber::factory()->verified()->create();

    $mail = (new NewIncidentNotification($incident))->toMail($subscriber);

    $html = (new Markdown(app('view'), ['theme' => Cachet::MAIL_THEME]))
        ->render($mail->markdown, $mail->viewData)
        ->toHtml();

    expect($mail->subject)->toBe(__('cachet::subscriber.mail.new_incident.subject', ['incident' => 'API latency']))
        ->and($mail->theme)->toBe(Cachet::MAIL_THEME)
        ->and($html)->toContain('API latency')
        ->toContain($incident->status->getLabel())
        ->toContain('investigating</strong>')
        ->toContain(e(route('cachet.status-page.incident', ['incident' => $incident])))
        ->toContain(e($subscriber->unsubscribeUrl()));
});
