<?php

namespace Tests\Unit\Notifications;

use Cachet\Cachet;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Models\Incident;
use Cachet\Notifications\LongRunningIncidentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Workbench\App\User;

it('renders the themed long-running incident email', function () {
    $incident = Incident::factory()->create([
        'name' => 'API latency',
        'created_at' => now()->subHours(12),
    ]);

    $notification = new LongRunningIncidentNotification($incident);

    expect($notification)->toBeInstanceOf(ShouldQueue::class);

    $mail = $notification->toMail(User::factory()->create());

    $html = (new Markdown(app('view'), ['theme' => Cachet::MAIL_THEME]))
        ->render($mail->markdown, $mail->viewData)
        ->toHtml();

    expect($mail->subject)->toBe(__('cachet::incident.mail.long_running.subject', ['incident' => 'API latency']))
        ->and($html)->toContain('API latency')
        ->toContain(__('cachet::incident.mail.long_running.heading'))
        ->toContain(e(IncidentResource::getUrl('edit', ['record' => $incident], panel: 'cachet')));
});
